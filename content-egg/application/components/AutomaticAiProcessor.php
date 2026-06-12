<?php

namespace ContentEgg\application\components;

defined('\ABSPATH') || exit;

use ContentEgg\application\admin\GeneralConfig;
use ContentEgg\application\components\ai\ContentHelper;
use ContentEgg\application\components\PrefillLogger;
use ContentEgg\application\components\ContentManipulator;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\models\PrefillQueueModel;
use ContentEgg\application\Plugin;



/**
 * AutomaticAiProcessor class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */

class AutomaticAiProcessor
{
    protected PrefillLogger $logger;
    protected ContentManipulator $manipulator;
    protected PrefillQueueModel $queue;
    protected \Wp_Post $post;
    protected $prompt;
    protected array $modules = [];
    private $used_keywords = [];
    private $used_product_ids = [];
    private $module_data = [];
    private $recommended_words_per_block = 0;

    const PRODUCT_GROUP = 'AiPrefill';

    public function __construct(

        $prompt,
        PrefillLogger $logger,
        ContentManipulator $manipulator
    )
    {
        $this->prompt           = $prompt;
        $this->logger           = $logger           ?? new PrefillLogger();
        $this->manipulator      = $manipulator      ?? new ContentManipulator();
        $this->queue            = PrefillQueueModel::model();
    }

    public function processAndSave(\WP_Post $post, array $modules): int
    {
        $this->post = $post;
        $this->modules = $modules;
        $this->manipulator->setPost($post);

        // Decide how many product blocks to insert
        $blockCount = $this->getRecommendedBlockCount($post);

        // Choose which sections will get a block
        $minSectionWords = (int) apply_filters('cegg_ai_prefill_min_section_words', 120);
        $maxSectionWords = (int) apply_filters('cegg_ai_prefill_min_section_words', $this->recommended_words_per_block + 50);
        $adSections = $this->manipulator->getAdSlotSections($blockCount, $minSectionWords, $maxSectionWords);

        $this->logger->notice(sprintf('Ad sections found: %d', count($adSections)));

        // Prepare one shortcode/block per slot
        $blockHtmls = $this->getAdSlotShortcodes($adSections);

        if ($this->used_keywords)
        {
            $this->logger->notice(sprintf('Keywords used: %s', TextHelper::truncate(implode(', ', (array) $this->used_keywords), 300)));
        }

        if (!$blockHtmls)
        {
            $this->logger->notice('No products found for this post');
            return 0;
        }

        // Inject those blocks into the full chunk list
        $newChunks = $this->manipulator->injectAdSlots($adSections, $blockHtmls);

        $nonEmptyBlocks = count(array_filter($blockHtmls, fn($html) => trim($html) !== ''));
        $this->logger->notice(sprintf('Blocks added: %d', $nonEmptyBlocks));

        // Re‐assemble into one HTML blob
        $modified = $this->manipulator->assembleContent($newChunks);

        if ($modified === $post->post_content)
        {
            return 0;
        }

        // Optionally add a hero grid block at the beginning of the content
        $should_add_grid = apply_filters('cegg_ai_prefill_add_hero_grid', true, $this->post);

        if ($should_add_grid)
        {
            $hero_grid = $this->buildHeroBlock();

            if (!empty($hero_grid))
            {
                $modified = $this->manipulator->addHeroBlock($modified, $hero_grid);
            }
        }

        // Save product data
        if (!empty($this->module_data))
        {
            $this->assignGroupToProducts();

            // Flatten all products from all modules
            $all_products = [];
            foreach ($this->module_data as $data)
            {
                foreach ($data as $product)
                {
                    $all_products[] = $product;
                }
            }

            // Process all products through the AI
            $processedProducts = $this->prompt->processProductDataForShortcode($all_products);

            // Regroup by module_id in one line
            $this->module_data = $this->groupByModuleId($processedProducts);

            // Save products to DB
            foreach ($this->module_data as $module_id => $data)
            {
                ContentManager::saveData($data, $module_id, $this->post->ID);
            }
        }

        $update = [
            'ID'           => $this->post->ID,
            'post_content' => $modified,
        ];

        return wp_update_post(wp_slash($update), true);
    }

    protected function getWordsPerBlock(int $wordCount): int
    {
        if ($wordCount >= 5000)
        {
            $this->recommended_words_per_block = 550;
        }
        elseif ($wordCount >= 3000)
        {
            $this->recommended_words_per_block = 450;
        }
        elseif ($wordCount >= 2000)
        {
            $this->recommended_words_per_block = 350;
        }
        elseif ($wordCount >= 1000)
        {
            $this->recommended_words_per_block = 250;
        }
        elseif ($wordCount >= 500)
        {
            $this->recommended_words_per_block = 200;
        }
        else
        {
            $this->recommended_words_per_block = 120;
        }

        return $this->recommended_words_per_block;
    }

    /**
     * Calculate the recommended number of blocks for a given post.
     */
    protected function getRecommendedBlockCount(\WP_Post $post): int
    {
        $textContent    = ContentHelper::prepareBlockPostContent($post->post_content);
        $lang           = GeneralConfig::getInstance()->option('ai_language');
        $wordCount      = ContentHelper::countWords($textContent, $lang);
        $wordsPerBlock  = $this->getWordsPerBlock($wordCount);

        $this->logger->notice(sprintf('Word count: %d', $wordCount));

        // clamp extreme values
        $minBlocks = 1;
        $maxBlocks = 10;

        $blocks = (int) ceil($wordCount / $wordsPerBlock);
        $blocks = max($minBlocks, min($maxBlocks, $blocks));

        return (int) apply_filters('cegg_ai_prefill_block_count', $blocks, $wordCount);
    }

    protected function getAdSlotShortcodes(array $adSections): array
    {
        $this->module_data = [];
        $this->used_product_ids = [];

        $shortcodes = [];
        foreach ($adSections as $i => $section)
        {
            $shortcodes[] = $this->getAdSlotShortcode($section, $i);
        }

        if ($this->module_data)
        {
            $modules = [];

            foreach ($this->module_data as $module_id => $data)
            {
                $count = count($data);
                $modules[] = ModuleManager::getInstance()->getModuleNameById($module_id) . ": {$count}";
            }
            $total = array_sum(array_map('count', $this->module_data));
            $log = "Products added: {$total} (" . implode(', ', $modules) . ")";
            $this->logger->notice($log);
        }

        // if every shortcode is empty, bail out
        $hasNonEmpty = (bool) array_filter(
            $shortcodes,
            fn($code) => trim((string) $code) !== ''
        );

        return $hasNonEmpty ? $shortcodes : [];
    }

    protected function getAdSlotShortcode(array $section, int $section_num): string
    {
        // 0. Don’t show an ad for a short introduction section.
        if (
            $section_num === 0
            && $section['word_count'] <= 200
            && strpos($section['html'], '<!--more-->') !== false
        )
        {
            return '';
        }

        // 1. Get the keywords for this section
        $keywords = $this->getKeywordsForSection($section);

        if (!$keywords)
        {
            if (Plugin::isDevEnvironment())
            {
                $this->logger->notice(sprintf('No keywords found for section #%d.', $section_num));
            }

            return '';
        }

        // 2. Find product and build shortcode/block
        $irrelevan_product_count = 0;
        $module_id = $this->modules[0];
        foreach ($keywords as $ki => $keyword)
        {
            $this->used_keywords[] = $keyword;

            if ($ki)
            {
                $sleepTime = ($module_id === 'Amazon') ? 1800000 : 500000;
                usleep($sleepTime);
            }

            foreach ($this->modules as $module_id)
            {
                $parser = ModuleManager::getInstance()->parserFactory($module_id);
                $max_per_module = $this->searchResultsCount($module_id);
                $settings = ['entries_per_page' => $max_per_module];

                try
                {
                    $parser->getConfigInstance()->applyCustomOptions($settings);
                    $searchResults = $parser->doMultipleRequests($keyword);
                }
                catch (\Exception $e)
                {
                    $this->logger->notice(sprintf(__('Module error "%s": %s', 'content-egg'), $module_id, TextHelper::truncate($e->getMessage(), 200)));
                    continue;
                }

                if (!$searchResults)
                {
                    if (Plugin::isDevEnvironment())
                    {
                        $this->logger->notice(sprintf('No products found for module "%s" and keyword "%s".', $module_id, $keyword));
                    }
                    continue;
                }

                if (in_array($module_id, ['Aliexpress2', 'Ebay2']))
                {
                    shuffle($searchResults);
                }

                $candidate_product = null;

                // Check for duplicates
                foreach ($searchResults as $product)
                {
                    $uniqueId = $product->unique_id;
                    if (!in_array($uniqueId, $this->used_product_ids))
                    {
                        $this->used_product_ids[] = $uniqueId;
                        $candidate_product = $product;
                        break; // 1 keyword = 1 product
                    }
                }

                if (!$candidate_product)
                {
                    continue;
                }

                // Check for relevant product
                $check_relevance = apply_filters('cegg_ai_prefill_check_relevance', true, $this->post, $candidate_product);
                if ($check_relevance && !$this->isRelevantProduct($section, $candidate_product))
                {
                    $irrelevan_product_count++;
                    continue;
                }

                $product = $candidate_product;
                $product->order_num = $section_num + 1;

                $this->module_data[$module_id] = array_merge(
                    $this->module_data[$module_id] ?? [],
                    [
                        $product,
                    ]
                );

                if ($irrelevan_product_count)
                {
                    $module_name = ModuleManager::getInstance()->getModuleNameById($module_id);
                    $this->logger->notice(sprintf('Irrelevant products filtered (%s): %d', $module_name, $irrelevan_product_count));
                }

                return $this->buildSectionBlock($product, $section_num);
            }
        }

        if ($irrelevan_product_count)
        {
            $this->logger->notice(sprintf('No relevant products found for section "%s".', $section['h2_heading'] ?: $section_num));
        }

        return '';
    }

    protected function getKeywordsForSection(array $section): array
    {
        $max_keywords = 2;

        $heading = '';
        if ($section['h2_heading'])
        {
            $heading .= $section['h2_heading'];
        }

        if ($section['heading'] && $section['heading'] !== $section['h2_heading'])
        {
            if ($heading)
            {
                $heading .= ' / ';
            }
            $heading .= $section['heading'];
        }

        $keywords = $this->prompt->suggestProductKeywordsForSection(
            $this->post->post_title,
            $heading,
            $section['text'],
            $max_keywords,
            $this->modules,
        );

        $this->queue->updateAiStat($this->post->ID, $this->prompt->getLastUsageStat());

        $keywords = array_diff($keywords, $this->used_keywords);

        if (!$keywords || !is_array($keywords))
        {
            return [];
        }

        return $keywords;
    }

    public function searchResultsCount($module_id)
    {
        // Search for multiple results per keyword to handle duplicates and irrelevant products
        if (strstr($module_id, 'AE__'))
            $searchResultsCount = 2;
        if (in_array($module_id, ['AmazonNoApi']))
            $searchResultsCount = 4;
        if (in_array($module_id, ['Amazon', 'Bolcom']))
            $searchResultsCount = 8;
        elseif (in_array($module_id, ['Aliexpress2']))
            $searchResultsCount = 10;
        else
            $searchResultsCount = 6;

        return (int) apply_filters('cegg_ai_prefill_search_results_count', $searchResultsCount, $module_id);
    }

    private function isRelevantProduct(array $section, ContentProduct $product)
    {
        $heading = '';
        if ($section['h2_heading'])
        {
            $heading .= $section['h2_heading'];
        }
        if ($section['heading'] && $section['heading'] !== $section['h2_heading'])
        {
            if ($heading)
            {
                $heading .= ': ';
            }
            $heading .= $section['heading'];
        }

        $is_relevant = $this->prompt->isRelevantProductForSection(
            $this->post->post_title,
            $heading,
            $product->title,
        );

        $this->queue->updateAiStat($this->post->ID, $this->prompt->getLastUsageStat());

        return $is_relevant;
    }

    protected function getProductGroup()
    {
        return apply_filters('cegg_ai_prefill_product_group', self::PRODUCT_GROUP);
    }

    protected function assignGroupToProducts(): self
    {
        if (empty($this->module_data))
        {
            return $this;
        }

        $group = $this->getProductGroup();

        foreach ($this->module_data as &$products)
        {
            foreach ($products as &$product)
            {
                $product->group = $group;
            }
        }

        return $this;
    }

    public function buildSectionBlock(ContentProduct $product, ?int $sectionNum = null): string
    {
        $params = [
            'products'   => $product->unique_id,
            'visible'    => ['description'],
            //'hide'       => ['rating'],
            'title_tag'  => 'div',
            'img_ratio'  => '4x3',
        ];

        if ($sectionNum === null || $sectionNum % 2 === 0)
        {
            $params['cols_order'] = '2,1';
        }

        return $this->renderCE('price_comparison_card', $params);
    }

    public function buildHeroBlock(): string
    {
        $total = array_sum(array_map('count', $this->module_data));

        // Only show block if at least 2 products have been added
        if ($total < 2)
        {
            return '';
        }

        $group = $this->getProductGroup();

        // Default grid settings
        $params = [
            'limit'   => 4,
            'cols'    => 4,
            'cols_xs' => 2,
            'hide'    => ['price', 'rating', 'subtitle'],
            'groups'  => [$group],
        ];

        if ($total <= 3)
        {
            $params['limit']  = 2;
            $params['cols']   = 2;

            if ($total === 3)
            {
                $params['offset'] = 1;
            }
        }
        elseif ($total >= 5)
        {
            $params['offset'] = 1;
        }

        return $this->renderCE('offers_grid', $params);
    }

    private function renderCE(string $template, array $params): string
    {
        return has_blocks($this->post)
            ? $this->renderBlock($template, $params)
            : $this->renderShortcode($template, $params);
    }

    private function renderBlock(string $template, array $params): string
    {
        $payload = array_merge(['template' => $template], $params);
        $json    = wp_json_encode($payload, JSON_UNESCAPED_SLASHES);

        $block   = sprintf(
            '<!-- wp:content-egg/products %s /-->',
            $json
        );

        return apply_filters('cegg_ai_prefill_product_block', $block, $template, $params);
    }

    private function renderShortcode(string $template, array $params): string
    {
        $attrs = array_map(function ($key, $value)
        {
            $val = is_array($value)
                ? implode(',', $value)
                : $value;
            return sprintf('%s="%s"', esc_attr($key), esc_attr((string)$val));
        }, array_keys($params), $params);

        $shortcode = sprintf(
            '[content-egg-block template="%s" %s]',
            esc_attr($template),
            implode(' ', $attrs)
        );

        return apply_filters('cegg_ai_prefill_product_shortcode', $shortcode, $template, $params);
    }

    private function groupByModuleId(array $products): array
    {
        $grouped = [];
        foreach ($products as $product)
        {
            $grouped[$product->module_id][] = $product;
        }
        return $grouped;
    }
}
