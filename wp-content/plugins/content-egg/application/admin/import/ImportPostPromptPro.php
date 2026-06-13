<?php

namespace ContentEgg\application\admin\import;

use ContentEgg\application\components\ai\ContentHelper;
use ContentEgg\application\components\ai\Prompt;
use ContentEgg\application\helpers\ProductHelper;

defined('\ABSPATH') || exit;

/**
 * ImportPostPromptPro class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */

class ImportPostPromptPro extends Prompt
{
    private array $customPrompts = [];

    protected $sourceProduct = [];
    protected $product = [];
    protected $postTitle;

    public function setSourceProduct(array $sourceProduct)
    {
        $this->sourceProduct = $sourceProduct;
    }

    public function setProduct(array $product)
    {
        $this->product = $product;
    }

    public function setPostTitle(string $postTitle)
    {
        $this->postTitle = $postTitle;
    }

    public function getTitleMethod(string $methodKey)
    {
        $methods = self::titleMethods();
        foreach ($methods as $item)
        {
            if ($item['key'] === $methodKey)
                return $item['method'];
        }
        return false;
    }

    public function getDescriptionMethod(string $methodKey)
    {
        $methods = self::descriptionMethods();
        foreach ($methods as $item)
        {
            if ($item['key'] === $methodKey)
                return $item['method'];
        }
        return false;
    }

    public function getShortDescriptionMethod(string $methodKey)
    {
        $methods = self::shortDescriptionMethods();
        foreach ($methods as $item)
        {
            if ($item['key'] === $methodKey)
                return $item['method'];
        }
        return false;
    }

    public static function titleMethods(): array
    {
        return [
            [
                'key'    => 'generate_review_title',
                'label'  => __('Generate a Review Title', 'content-egg'),
                'method' => 'generateReviewTitle',
            ],
            [
                'key'    => 'generate_buyers_guide_title',
                'label'  => __("Generate a Buyer's Guide Title", 'content-egg'),
                'method' => 'generateBuyersGuideTitle',
            ],
            [
                'key'    => 'generate_how_to_use_title',
                'label'  => __('Generate a How-to-Use Title', 'content-egg'),
                'method' => 'generateHowToUseTitle',
            ],
            [
                'key'    => 'generate_question_title',
                'label'  => __('Generate a Question-Article Title', 'content-egg'),
                'method' => 'generateQuestionTitle',
            ],
            [
                'key'    => 'shorten',
                'label'  => __('Shorten Product Title', 'content-egg'),
                'method' => 'shortenProductTitle',
            ],
            [
                'key'    => 'rephrase',
                'label'  => __('Rephrase Product Title', 'content-egg'),
                'method' => 'rephraseProductTitle',
            ],
            [
                'key'    => 'improve',
                'label'  => __('Improve for WooCommerce Listing', 'content-egg'),
                'method' => 'improveForWooCommerceTitle',
            ],
            [
                'key'    => 'translate',
                'label'  => __('Translate Product Title', 'content-egg'),
                'method' => 'translateProductTitle',
            ],
            [
                'key'    => 'prompt1',
                'label'  => __('Custom Prompt #1', 'content-egg'),
                'method' => 'customPrompt1Title',
            ],
            [
                'key'    => 'prompt2',
                'label'  => __('Custom Prompt #2', 'content-egg'),
                'method' => 'customPrompt2Title',
            ],
            [
                'key'    => 'prompt3',
                'label'  => __('Custom Prompt #3', 'content-egg'),
                'method' => 'customPrompt3Title',
            ],
        ];
    }

    public static function descriptionMethods(): array
    {
        return [
            [
                'key'    => 'write_review',
                'label'  => __('Write a Review', 'content-egg'),
                'method' => 'writeReviewProductDescription',
            ],
            [
                'key'    => 'write_buyers_guide',
                'label'  => __("Write a Buyer's Guide", 'content-egg'),
                'method' => 'writeBuyersGuideProductDescription',
            ],
            [
                'key'    => 'write_how_to_use',
                'label'  => __('Write How-to-Use Instructions', 'content-egg'),
                'method' => 'writeHowToUseProductDescription',
            ],
            [
                'key'    => 'write_article',
                'label'  => __('Write an Article', 'content-egg'),
                'method' => 'writeArticleProductDescription',
            ],
            [
                'key'    => 'rewrite',
                'label'  => __('Rewrite', 'content-egg'),
                'method' => 'rewriteProductDescription',
            ],
            [
                'key'    => 'paraphrase',
                'label'  => __('Paraphrase', 'content-egg'),
                'method' => 'paraphraseProductDescription',
            ],
            [
                'key'    => 'translate',
                'label'  => __('Translate', 'content-egg'),
                'method' => 'translateProductDescription',
            ],
            [
                'key'    => 'summarize',
                'label'  => __('Summarize', 'content-egg'),
                'method' => 'summarizeProductDescription',
            ],
            [
                'key'    => 'bullet_points',
                'label'  => __('Bullet Points', 'content-egg'),
                'method' => 'bulletPointsProductDescription',
            ],
            [
                'key'    => 'bullet_points_compact',
                'label'  => __('Bullet Points (Concise)', 'content-egg'),
                'method' => 'bulletPointsCompactProductDescription',
            ],
            [
                'key'    => 'write_paragraphs',
                'label'  => __('Write a Few Paragraphs', 'content-egg'),
                'method' => 'writeParagraphsProductDescription',
            ],
            [
                'key'    => 'improve',
                'label'  => __('Improve for WooCommerce Listing', 'content-egg'),
                'method' => 'improveForWooCommerceDescription',
            ],
            [
                'key'    => 'craft_description',
                'label'  => __('Craft a Product Description', 'content-egg'),
                'method' => 'craftProductDescription',
            ],
            [
                'key'    => 'turn_into_advertising',
                'label'  => __('Turn into Advertising', 'content-egg'),
                'method' => 'turnIntoAdvertisingProductDescription',
            ],
            [
                'key'    => 'cta_text',
                'label'  => __('Generate CTA Text', 'content-egg'),
                'method' => 'ctaTextProductDescription',
            ],
            [
                'key'    => 'prompt1',
                'label'  => __('Custom Prompt #1', 'content-egg'),
                'method' => 'customPrompt1Description',
            ],
            [
                'key'    => 'prompt2',
                'label'  => __('Custom Prompt #2', 'content-egg'),
                'method' => 'customPrompt2Description',
            ],
            [
                'key'    => 'prompt3',
                'label'  => __('Custom Prompt #3', 'content-egg'),
                'method' => 'customPrompt3Description',
            ],
        ];
    }

    public static function shortDescriptionMethods(): array
    {
        return [
            [
                'key'    => 'generate_short_description',
                'label'  => __('Generate Short Description', 'content-egg'),
                'method' => 'generateProductShortDescription',
            ],
            [
                'key'    => 'summarize',
                'label'  => __('Summarize', 'content-egg'),
                'method' => 'summarizeProductDescription',
            ],
            [
                'key'    => 'bullet_points',
                'label'  => __('Bullet Points', 'content-egg'),
                'method' => 'bulletPointsProductDescription',
            ],
            [
                'key'    => 'bullet_points_compact',
                'label'  => __('Bullet Points (Concise)', 'content-egg'),
                'method' => 'bulletPointsCompactProductDescription',
            ],
            [
                'key'    => 'write_paragraphs',
                'label'  => __('Write a Few Paragraphs', 'content-egg'),
                'method' => 'writeParagraphsProductDescription',
            ],
            [
                'key'    => 'turn_into_advertising',
                'label'  => __('Turn into Advertising', 'content-egg'),
                'method' => 'turnIntoAdvertisingProductDescription',
            ],
            [
                'key'    => 'cta_text',
                'label'  => __('Generate CTA Text', 'content-egg'),
                'method' => 'ctaTextProductDescription',
            ],
            [
                'key'    => 'prompt1',
                'label'  => __('Custom Prompt #1', 'content-egg'),
                'method' => 'customPrompt1Description',
            ],
            [
                'key'    => 'prompt2',
                'label'  => __('Custom Prompt #2', 'content-egg'),
                'method' => 'customPrompt2Description',
            ],
            [
                'key'    => 'prompt3',
                'label'  => __('Custom Prompt #3', 'content-egg'),
                'method' => 'customPrompt3Description',
            ],
        ];
    }

    public static function getTitleMethodOptions(): array
    {
        $options = [];
        foreach (self::titleMethods() as $method)
        {
            $options[$method['key']] = $method['label'];
        }
        return $options;
    }

    public static function getDescriptionMethodOptions(): array
    {
        $options = [];
        foreach (self::descriptionMethods() as $method)
        {
            $options[$method['key']] = $method['label'];
        }
        return $options;
    }

    public static function getShortDescriptionMethodOptions(): array
    {
        $options = [];
        foreach (self::shortDescriptionMethods() as $method)
        {
            $options[$method['key']] = $method['label'];
        }
        return $options;
    }

    public function setCustomPrompts(array $prompts): void
    {
        $this->customPrompts = $prompts;
    }

    public function customPrompt1Title(): string
    {
        return $this->runCustomPromptTitle('prompt1');
    }

    public function customPrompt2Title(): string
    {
        return $this->runCustomPromptTitle('prompt2');
    }

    public function customPrompt3Title(): string
    {
        return $this->runCustomPromptTitle('prompt3');
    }

    private function runCustomPromptTitle(string $key): string
    {
        if (empty($this->customPrompts[$key]))
        {
            return '';
        }

        $prompt = $this->customPrompts[$key];
        $prompt .= $this->getGeneralInstructionsForTitle();

        return ContentHelper::prepareProductTitle($this->query($prompt));
    }

    public function customPrompt1Description(): string
    {
        return $this->runCustomPromptDescription('prompt1');
    }

    public function customPrompt2Description(): string
    {
        return $this->runCustomPromptDescription('prompt2');
    }

    public function customPrompt3Description(): string
    {
        return $this->runCustomPromptDescription('prompt3');
    }

    private function runCustomPromptDescription(string $key): string
    {
        if (empty($this->customPrompts[$key]))
        {
            return '';
        }

        $prompt = $this->customPrompts[$key];

        $prompt .= $this->getGeneralInstructionsForDescription();

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    // === Title Methods ===

    public function generateReviewTitle(): string
    {
        $prompt = 'Create a concise, SEO-friendly and eye-catching title for a product review post about "%SOURCE.title%".';
        $prompt .= $this->getGeneralInstructionsForTitle();

        return ContentHelper::prepareProductTitle($this->query($prompt));
    }

    public function generateBuyersGuideTitle(): string
    {
        $prompt = 'Write a clear, concise title for a buyer\'s guide about "%SOURCE.title%".';
        $prompt .= $this->getGeneralInstructionsForTitle();

        return ContentHelper::prepareProductTitle($this->query($prompt));
    }

    public function generateHowToUseTitle(): string
    {
        $prompt = 'Create a clear, concise title for a how-to-use guide about "%SOURCE.title%".';
        $prompt .= $this->getGeneralInstructionsForTitle();

        return ContentHelper::prepareProductTitle($this->query($prompt));
    }

    public function generateQuestionTitle(): string
    {
        $prompt = "Write a list of 5 short and concise questions related to the product \"%SOURCE.title%\", ensuring each question includes the product name or model and is suitable for use as a post title.";
        $prompt .= $this->getGeneralInstructionsForTitleList();

        $titles = ContentHelper::listToArray($this->query($prompt));
        shuffle($titles);
        $title = reset($titles);

        return ContentHelper::prepareProductTitle($title);
    }

    public function shortenProductTitle(): string
    {
        $prompt  = 'Shorten the following product title to between 5 and 7 words: "' . '%SOURCE.title%' . '".';
        $prompt .= $this->getGeneralInstructionsForTitle();

        return ContentHelper::prepareProductTitle($this->query($prompt));
    }

    public function rephraseProductTitle(): string
    {
        $prompt  = 'Rephrase the following product title to improve clarity and style. Make it useful for WooCoomerce product title. Product title: "' . '%SOURCE.title%' . '".';
        $prompt .= $this->getGeneralInstructionsForTitle();

        return ContentHelper::prepareProductTitle($this->query($prompt));
    }

    public function improveForWooCommerceTitle(): string
    {
        $prompt = 'Rewrite this product title for a WooCommerce store. Make it clearer, more attractive to potential buyers, and optimized for SEO. Keep the title under approximately 60 characters.';
        $prompt .= $this->getGeneralInstructionsForTitle();
        $prompt .= "\n\nSource product title: \"%SOURCE.title%\".";

        return ContentHelper::prepareProductTitle($this->query($prompt));
    }

    public function translateProductTitle(): string
    {
        $prompt = "Translate the product title into %lang%: \"%SOURCE.title%\".";
        $prompt .= $this->getGeneralInstructionsForTitle();

        return ContentHelper::prepareProductTitle($this->query($prompt));
    }

    // === Description Methods ===

    public function writeReviewProductDescription(): string
    {
        $prompt = "You are provided with product data.";
        $prompt .= "\nWrite a comprehensive product review";

        if ($this->postTitle)
        {
            $prompt .= " titled: \"{$this->postTitle}\"";
        }

        $prompt .= " for the product \"%PRODUCT.title%\". ";

        $prompt .= "\nInstructions:";
        $prompt .= "\nInclude introduction. Provide a brief overview of the product, including the manufacturer, product category, and intended use.";
        $prompt .= "\nDescribe the product's appearance, materials used, or overall aesthetic. Mention any unique design features or elements.";
        $prompt .= "\nList the key features or specifications of the product.";
        $prompt .= "\nDetail experience using the product in various scenarios.";
        $prompt .= "\nInclude Pros and Cons. ";
        $prompt .= "\nInclude Conclusion. Summarize your overall impression of the product.";

        $prompt .= "\n\nThe review should be in HTML format. Each section should provide detailed insights, highlighting both the strengths and weaknesses of the product. The review should be objective and thorough, providing valuable information for potential buyers. Use HTML tags to properly structure the content, ensuring it is well-organized and easy to read.";

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'description',
            'price',
            'specifications',
            'userRating',
            'userReviewsCount',
            'userReviews',
        ]);
        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        $prompt .= "\n\n\n<html><body>[ADD REVIEW TEXT HERE]<body></html>";

        return ContentHelper::prepareArticle($this->query($prompt), $this->postTitle);
    }

    public function writeBuyersGuideProductDescription(): string
    {
        $prompt = "You are provided with product data.";
        $prompt .= "\nWrite a comprehensive Buying Guide";

        if ($this->postTitle)
        {
            $prompt .= " titled: \"{$this->postTitle}\"";
        }

        $prompt .= " on how to select \"%PRODUCT.title%\". ";

        $prompt .= "Divide the Guide with subheadings. Format in html. Do not include CSS styles.";

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'description',
            'price',
            'specifications',
            'userRating',
            'userReviewsCount',
        ]);
        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        return ContentHelper::prepareArticle($this->query($prompt), $this->postTitle);
    }

    public function writeHowToUseProductDescription(): string
    {
        $prompt = "You are provided with product data.";
        $prompt .= "\nWrite an instruction of How to use this product for beginners.";
        $prompt .= "Add subheadings. Use lists. Format in html. Do not include CSS styles.";

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'description',
            'price',
            'specifications',
            'userRating',
            'userReviewsCount',
            'userReviews',
        ]);
        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        $prompt .= "\n\n\n<html><body>[HOW TO USE]<body></html>";

        return ContentHelper::prepareArticle($this->query($prompt), $this->postTitle);
    }

    public function writeArticleProductDescription(): string
    {
        $prompt = "You are provided with product data.";
        $prompt .= "\nWrite an article about the product";

        if ($this->postTitle)
        {
            $prompt .= " titled: \"{$this->postTitle}\"";
        }

        $prompt .= ".";

        $prompt .= "\nAdd subheadings. Use lists. Format in html. Do not include CSS styles.";

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'description',
            'price',
            'specifications',
            'userRating',
            'userReviewsCount',
            'userReviews',
        ]);
        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        $prompt .= "\n\n\n<html><body>[ARTICLE TEXT]<body></html>";

        return ContentHelper::prepareArticle($this->query($prompt), $this->postTitle);
    }

    public function rewriteProductDescription(): string
    {
        if (!empty($this->sourceProduct['description']))
            $description = $this->sourceProduct['description'];
        elseif (!empty($this->product['description']))
            $description = $this->product['description'];
        else
            return '';

        $description = ContentHelper::htmlToText($description);

        $prompt = "Rewrite and format the following product description of the product titled \"%SOURCE.title%\".";
        $prompt .= $this->getGeneralInstructionsForDescription();
        $prompt .= "\n\nSource product description:\n" . $description;

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    public function paraphraseProductDescription(): string
    {
        if (!empty($this->sourceProduct['description']))
            $description = $this->sourceProduct['description'];
        elseif (!empty($this->product['description']))
            $description = $this->product['description'];
        else
            return '';

        $description = ContentHelper::htmlToText($description);

        $prompt = "Paraphrase and format the following product description of the product titled \"%SOURCE.TITLE%\".";
        $prompt .= $this->getGeneralInstructionsForDescription();
        $prompt .= "\n\nSource product description:\n" . $description;

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    public function translateProductDescription(): string
    {
        if (!empty($this->sourceProduct['description']))
            $description = $this->sourceProduct['description'];
        elseif (!empty($this->product['description']))
            $description = $this->product['description'];
        else
            return '';

        $prompt = "Translate to %lang% the product description below.";
        $prompt .= "\nSave HTML fomatting in answer. Provide the translated result only without any additional text or explanation.";

        $prompt .= "\nSource product description:\n" . $description;

        return ContentHelper::prepareArticle($this->query($prompt));
    }

    public function summarizeProductDescription(): string
    {
        if (!empty($this->sourceProduct['description']))
            $description = $this->sourceProduct['description'];
        elseif (!empty($this->product['description']))
            $description = $this->product['description'];
        else
            return '';

        $description = ContentHelper::htmlToText($description);

        $prompt = "Summarize the following product description of the product titled \"%SOURCE.title%\".";

        $prompt .= $this->getGeneralInstructionsForDescription();
        $prompt .= "\nSource product description:\n" . $description;

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    public function bulletPointsProductDescription(): string
    {
        $prompt = "You are given product data.";
        $prompt .= "\nGenerate 5–8 concise bullet points.";
        $prompt .= "\nFormat the output as a Markdown list.";
        $prompt .= "\nOnly return the bullet points — no extra text, titles, or explanations.";

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'description',
            'specifications',
        ]);
        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    public function bulletPointsCompactProductDescription(): string
    {
        $prompt = "You are provided with product data.";
        $prompt .= "\nGenerate exactly 4 ultra-concise, spec-style bullet points for the product below. Each bullet must be 30 characters or fewer. Format the output as an HTML <ul> list. Example:\n<ul>\n<li>Battery: 18–36 hrs</li>\n<li>Display: Always-On Retina</li>\n<li>Sensors: ECG, Blood Oxygen, Temp</li>\n<li>Chip: Latest S10</li>\n</ul>";

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'description',
            'specifications',
        ]);
        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        return ContentHelper::prepareHtml($this->query($prompt));
    }

    public function writeParagraphsProductDescription(): string
    {
        $prompt = "You are provided with product data.";
        $prompt .= "\nWrite a few paragraphs about the product.";
        $prompt .= $this->getGeneralInstructionsForDescription();

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'description',
            'specifications',
        ]);
        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    public function improveForWooCommerceDescription(): string
    {
        $prompt = 'Craft a product description for a WooCommerce store. '
            . 'Make it compelling and easy to scan (bullets, short paragraphs) '
            . 'and optimise it for SEO.';

        $prompt .= $this->getGeneralInstructionsForDescription();

        $product_data = ImportPrompt::prepareProductData(
            $this->sourceProduct,
            ['title', 'description', 'specifications']
        );

        $prompt .= "\n\n<product>\n"
            . json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            . "\n</product>";

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    public function craftProductDescription(): string
    {
        $prompt = "You are provided with product data.";
        $prompt .= "\nCraft a product description for the product.";
        $prompt .= $this->getGeneralInstructionsForDescription();

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'specifications',
        ]);
        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    public function turnIntoAdvertisingProductDescription(): string
    {
        $prompt = "You are provided with product data.";
        $prompt .= "\nTurn into advertising the following product description.";
        $prompt .= $this->getGeneralInstructionsForDescription();

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'description',
            'price',
            'specifications',
            'userRating',
            'userReviewsCount',
        ]);
        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    public function ctaTextProductDescription(): string
    {
        $prompt = "You are provided with product data.";
        $prompt .= "\nWrite a few sentences CTA for the product.";
        $prompt .= $this->getGeneralInstructionsForDescription();

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'description',
            'price',
            'specifications',
            'userRating',
            'userReviewsCount',
        ]);
        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    // === Short Description Method ===

    public function generateProductShortDescription(): string
    {
        $prompt = "You are given product data. Write a concise and engaging short description for the product. Keep it short.";
        $prompt .= $this->getGeneralInstructionsForDescription();

        $product_data = ImportPrompt::prepareProductData($this->sourceProduct, [
            'title',
            'description',
            'specifications',
        ]);

        $prompt .= "\n\n<product>\n" .
            json_encode($product_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) .
            "\n</product>";

        return ContentHelper::prepareMarkdown($this->query($prompt));
    }

    private function getGeneralInstructionsForTitle(): string
    {
        $instructions = "\nReturn only the title as plain text.";
        $instructions .= "\nDo NOT include HTML tags, Markdown formatting, emojis, or any additional text or explanation.";

        return $instructions;
    }

    private function getGeneralInstructionsForTitleList(): string
    {
        $instructions  = "\nProvide only the list of titles, with each title on its own line.";
        $instructions .= "\nDo not include HTML tags, Markdown syntax, links, emojis, any additional commentary, or follow-up offers.";
        return $instructions;
    }

    private function getGeneralInstructionsForDescription(): string
    {
        $instructions = "\n\nReturn the result in Markdown format only.";
        $instructions .= "\nDo not include any additional text, explanations, or follow-up offers.";

        return $instructions;
    }

    public function generateTitle(string $methodKey): string
    {
        $method = $this->getTitleMethod($methodKey);
        if (!(bool) $method)
        {
            throw new \Exception(sprintf('Title method "%s" does not exist.', esc_html($methodKey)));
        }

        $title = $this->$method();

        return $title;
    }

    public function generateDescription(string $methodKey): string
    {
        $method = $this->getDescriptionMethod($methodKey);
        if (!(bool) $method)
        {
            throw new \Exception(sprintf('Description method "%s" does not exist.', esc_html($methodKey)));
        }

        return $this->$method();
    }

    public function generateShortDescription(string $methodKey): string
    {
        $method = $this->getShortDescriptionMethod($methodKey);
        if (!(bool) $method)
        {
            throw new \Exception(sprintf('Short description method "%s" does not exist.', esc_html($methodKey)));
        }

        return $this->$method();
    }

    protected function query($prompt, array $params = array(), array $ai_params = array())
    {
        $prompt = ProductHelper::replaceImportPatterns($prompt, $this->sourceProduct, $this->product);

        return parent::query($prompt, $params, $ai_params);
    }
}
