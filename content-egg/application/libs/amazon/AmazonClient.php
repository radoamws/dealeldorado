<?php

namespace ContentEgg\application\libs\amazon;

defined('\ABSPATH') || exit;

use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\libs\ParserClient;

/**
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
class AmazonClient extends ParserClient
{
    protected $locale;
    protected $canonical_domain;

    protected $scraperapi_token;
    protected $proxycrawl_token;
    protected $scrapingdog_token;
    protected $scrapeowl_token;

    public function __construct($locale)
    {
        $this->setLocale($locale);
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        $host = AmazonLocales::getDomain($this->locale);
        $this->canonical_domain = 'https://www.' . $host;
    }

    public function setScraperapiToken($token)
    {
        $this->scraperapi_token = $token;
    }

    public function setProxycrawlToken($token)
    {
        $this->proxycrawl_token = $token;
    }

    public function setScrapingdogToken($token)
    {
        $this->scrapingdog_token = $token;
    }

    public function setScrapeowlToken($token)
    {
        $this->scrapeowl_token = $token;
    }

    public function setUrl($url)
    {
        if ($this->scrapingdog_token)
        {
            $url = 'https://api.scrapingdog.com/scrape?api_key=' . urlencode($this->scrapingdog_token) . '&url=' . urlencode($url) . '&dynamic=false';
        }
        elseif ($this->scrapeowl_token)
        {
            $url = 'https://api.scrapeowl.com/v1/scrape?api_key=' . urlencode($this->scrapeowl_token) . '&json_response=false&url=' . urlencode($url);
        }
        elseif ($this->scraperapi_token)
        {
            $url = 'http://api.scraperapi.com?api_key=' . urlencode($this->scraperapi_token) . '&url=' . urlencode($url);
        }
        elseif ($this->proxycrawl_token)
        {
            $api_base = 'https://api.crawlbase.com/';
            $query    = http_build_query([
                'token' => $this->proxycrawl_token,
                'url'   => $url,
            ], '', '&');

            $url = $api_base . '?' . $query;
        }

        $url = \apply_filters('cegg_amazon_client_url', $url);
        parent::setUrl($url);
    }

    public function restGet($uri, $query = null)
    {
        $opts = array();

        $opts = array(
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:138.0) Gecko/20100101 Firefox/138.0',
        );

        $client = self::getHttpClient($opts);
        $client->resetParameters();
        $client->setUri($uri);

        if ($query)
            $client->setParameterGet($query);

        $body = $this->getResult($client->request('GET'));

        // fix html
        $body = preg_replace('/<table id="HLCXComparisonTable".+?<\/table>/uims', '', $body);

        $html = preg_replace('/<head\b[^>]*>(.*?)<\/head>/uims', '', $body);
        if ($html)
            $body = $html;

        $body = preg_replace('/<script.*?>.*?<\/script>/uims', '', $body);
        $body = preg_replace('/<style.*?>.*?<\/style>/uims', '', $body);

        return $this->decodeCharset($body);
    }

    public function search($keyword, $limit, $max_price, $p_n_date = '')
    {
        $url = $this->getSearchUrl($keyword, $max_price, $p_n_date);
        $this->setUrl($url);

        $urls = $this->parseCatalog($limit);

        $urls = array_slice($urls, 0, $limit);

        $products = array();
        foreach ($urls as $url)
        {
            try
            {
                $product = $this->product($url);
            }
            catch (\Exception $e)
            {
                continue;
            }

            if ($product)
                $products[] = $product;
        }

        return $products;
    }

    public function product($url)
    {
        $this->setUrl($url);
        $product = array();

        $product['title'] = (string) $this->parseTitle();
        if (!$product['title'])
        {
            throw new \Exception(
                sprintf(
                    'The product URL can not be parsed: %s',
                    esc_url($url)
                )
            );
        }

        $product['url'] = $url;
        $product['description'] = TextHelper::sanitizeHtml((string) $this->parseDescription());
        $product['price'] = TextHelper::parsePriceAmount((string) $this->parsePrice());
        $product['priceOld'] = TextHelper::parsePriceAmount((string) $this->parseOldPrice());
        if ($product['price'] >= $product['priceOld'])
            $product['priceOld'] = 0;
        $product['img'] = (string) $this->parseImg();
        $product['manufacturer'] = (string) $this->parseManufacturer();
        $product['currencyCode'] = (string) $this->getCurrency();
        $product['features'] = (array) $this->parseFeatures();
        $product['extra'] = (array) $this->parseExtra();
        $product['promo'] = \sanitize_text_field((string) $this->parsePromo());

        if ((bool) $this->isInStock())
            $product['stock_status'] = 1;
        else
            $product['stock_status'] = -1;

        return $product;
    }

    public function getSearchUrl($keyword, $max_price = 0, $p_n_date = '')
    {
        $keyword = urlencode($keyword);
        $keyword = str_replace('%20', '+', $keyword);
        $url = $this->canonical_domain . '/s?k=' . $keyword;

        if ($max_price)
        {
            $url .= '&rh=p_36%3A-' . TextHelper::pricePenniesDenomination($max_price);
        }

        if ($p_n_date)
        {
            $url .= '&p_n_date=' . urlencode($p_n_date);
        }

        return $url;
    }

    public function parseCatalog($max)
    {
        $xpath = array(
            ".//div[contains(@class, 'SEARCH_RESULTS')]//a[contains(@class, 'a-link-normal') and contains(@href, '/dp/')]/@href",
            ".//span[@data-component-type='s-search-results']//a[contains(@class, 'a-link-normal') and contains(@href, '/dp/')]/@href",
            ".//div[contains(@class, 's-main-slot')]//h2[contains(@class, 'a-color-base')]//a[contains(@class, 'a-link-normal') and contains(@href, '/dp/')]/@href",
            ".//div[@class='p13n-desktop-grid']//a[@class='a-link-normal']/@href",
            ".//*[@class='aok-inline-block zg-item']/a[@class='a-link-normal']/@href",
            ".//h3[@class='newaps']/a/@href",
            ".//div[@id='resultsCol']//a[contains(@class,'s-access-detail-page')]/@href",
            ".//div[@class='zg_title']/a/@href",
            ".//div[@id='rightResultsATF']//a[contains(@class,'s-access-detail-page')]/@href",
            ".//div[@id='atfResults']/ul//li//div[contains(@class,'a-column')]/a/@href",
            ".//div[@id='mainResults']//li//a[@title]/@href",
            ".//*[@id='zg_centerListWrapper']//a[@class='a-link-normal' and not(@title)]/@href",
            ".//h5/a[@class='a-link-normal a-text-normal']/@href",
            ".//*[@data-component-type='s-product-image']//a[@class='a-link-normal']/@href",
            ".//div[@class='a-section a-spacing-none']/h2/a/@href",
            ".//h2/a[@class='a-link-normal a-text-normal']/@href",
            ".//span[@data-component-type='s-product-image']/a/@href",
        );

        $urls = $this->xpathArray($xpath);

        if (!$urls)
            return array();

        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = $this->canonical_domain . $url;
        }

        // picassoRedirect fix
        foreach ($urls as $i => $url)
        {
            if (strstr($url, 'app.primevideo.com'))
                unset($urls[$i]);

            if (!strstr($url, '/gp/slredirect/picassoRedirect.html/'))
                continue;
            $parts = parse_url($url);
            if (empty($parts['query']))
                continue;
            parse_str($parts['query'], $output);
            if (isset($output['url']))
                $urls[$i] = $output['url'];
            else
                unset($urls[$i]);
        }

        // fix urls. prevent duplicates for autobloging
        $res = array();
        foreach ($urls as $key => $url)
        {
            if ($asin = self::parseAsinFromUrl($url))
                $res[] = $this->canonical_domain . '/dp/' . $asin . '/';
        }

        $res = array_unique($res);
        $res = array_values($res);

        return $res;
    }

    public function parseTitle()
    {

        $paths = array(
            ".//h1[@id='title']/span",
            ".//*[@id='fine-ART-ProductLabelArtistNameLink']",
            ".//meta[@name='title']/@content",
            ".//h1",
        );

        return $this->xpathScalar($paths);
    }

    public static function parseAsinFromUrl($url)
    {
        $regex = '~/(?:exec/obidos/ASIN/|o/|gp/product/|gp/offer-listing/|(?:(?:[^"\'/]*)/)?dp/|)([0-9A-Z]{10})(?:(?:/|\?|\#)(?:[^"\'\s]*))?~isx';
        if (preg_match($regex, $url, $matches))
            return $matches[1];
        else
            return false;
    }

    public function parseDescription()
    {
        $path = array(
            ".//div[@id='featurebullets_feature_div']//span[@class='a-list-item']",
            ".//div[@id='featurebullets_feature_div']//li",
            ".//h3[@class='product-facts-title']//..//li",
        );
        if ($results = $this->xpathArray($path))
        {
            $results = array_map('\sanitize_text_field', $results);
            $key = array_search('Make sure this fits by entering your model number.', $results);
            if ($key !== false)
                unset($results[$key]);
            return '<ul><li>' . implode("</li><li>\n", $results) . '</li></ul>';
        }

        $result = $this->xpathScalar(".//script[contains(.,'iframeContent')]");
        if ($result && preg_match('/iframeContent\s=\s"(.+?)"/msi', $result, $match))
        {
            $res = urldecode($match[1]);
            if (preg_match('/class="productDescriptionWrapper">(.+?)</msi', $res, $match))
                return trim($match[1]);
        }

        $paths = array(
            ".//*[@id='bookDescription_feature_div']/noscript/div",
            ".//*[@id='productDescription']//*[@class='productDescriptionWrapper']",
            ".//*[@id='productDescription']/p/*[@class='btext']",
            ".//*[@id='productDescription']/p",
            ".//*[@id='bookDescription_feature_div']/noscript",
            ".//*[@class='dv-simple-synopsis dv-extender']",
            ".//*[@id='bookDescription_feature_div']//noscript/div",
            ".//div[@id='bookDescription_feature_div']",
        );

        if ($description = $this->xpathScalar($paths, true))
            return $description;

        if (preg_match('/bookDescEncodedData = "(.+?)",/', $this->_html, $matches))
            return html_entity_decode(urldecode($matches[1]));

        if (preg_match('/(<div id="bookDescription_feature_div".+?)<a href="/ims', $this->_html, $matches))
            return $matches[1];

        return '';
    }

    public function parsePrice()
    {
        if (!$this->isInStock())
            return 0;

        $paths = array(
            ".//span[@id='subscriptionPrice']//span[@data-a-color='price']//span[@class='a-offscreen']",
            ".//table[@class='a-lineitem a-align-top']//span[@data-a-color='price']//span[@class='a-offscreen']",
            ".//*[contains(@class, 'priceToPay')]//*[@class='a-offscreen']",
            ".//*[@class='a-price aok-align-center reinventPricePriceToPayMargin priceToPay']",
            ".//div[@class='a-section a-spacing-none aok-align-center']//span[@class='a-offscreen']",
            ".//span[contains(@class, 'a-price') and contains(@class, 'priceToPay')]//span[@class='a-offscreen']",
            ".//h5//span[@id='price']",
            ".//span[@class='a-price a-text-price header-price a-size-base a-text-normal']//span[@class='a-offscreen']",
            ".//span[@class='a-price a-text-price a-size-medium apexPriceToPay']//span[@class='a-offscreen']",
            ".//span[contains(@class, 'priceToPay')]",
            ".//div[@class='a-section a-spacing-small a-spacing-top-small']//a/span[@class='a-size-base a-color-price']",
            ".//div[@class='a-section a-spacing-none aok-align-center']//span[@class='a-offscreen']",
            ".//*[@id='priceblock_dealprice']",
            ".//span[@id='priceblock_ourprice']",
            ".//span[@id='priceblock_saleprice']",
            ".//div[@class='twisterSlotDiv addTwisterPadding']//span[@id='color_name_0_price']",
            ".//input[@name='displayedPrice']/@value",
            ".//*[@id='unqualifiedBuyBox']//*[@class='a-color-price']",
            ".//*[@class='dv-button-text']",
            ".//*[@id='cerberus-data-metrics']/@data-asin-price",
            ".//div[@id='olp-upd-new-freeshipping']//span[@class='a-color-price']",
            ".//span[@id='rentPrice']",
            ".//span[@id='newBuyBoxPrice']",
            ".//div[@id='olp-new']//span[@class='a-size-base a-color-price']",
            ".//span[@id='unqualified-buybox-olp']//span[@class='a-color-price']",
            ".//span[@id='price_inside_buybox']",
            ".//span[@class='slot-price']//span[@class='a-size-base a-color-price a-color-price']",
            ".//span[@class='a-button-inner']//span[contains(@class, 'a-color-price')]",
            ".//div[@id='booksHeaderSection']//span[@id='price']",
            ".//div[@class='a-box-inner a-padding-base']//span[@class='a-color-price aok-nowrap']",
            ".//span[@id='kindle-price']",
            ".//span[contains(@class, 'a-price')]//span/@aria-hidden",
            ".//span[contains(@class, 'a-price')]//span[@class='a-price-whole']",
        );

        $price = $this->xpathScalar($paths);
        $price = str_replace('R$', '', $price);

        if (!$price && $price = $this->xpathScalar(".//span[@id='priceblock_ourprice']//*[@class='buyingPrice' or @class='price-large']"))
        {
            if ($cent = $this->xpathScalar(".//span[@id='priceblock_ourprice']//*[@class='verticalAlign a-size-large priceToPayPadding' or @class='a-size-small price-info-superscript']"))
                $price = $price . '.' . $cent;
        }

        if (strstr($price, ' - '))
        {
            $tprice = explode('-', $price);
            $price = $tprice[0];
        }

        $parts = explode('opzioni', $price);
        $price = end($parts);

        return $price;
    }

    public function parseOldPrice()
    {
        if (!$this->isInStock())
            return;

        $paths = array(
            ".//*[not(@class='pricePerUnit')]//span[@class='a-price a-text-price a-size-base']//span[@class='a-offscreen']",
            ".//*[@id='price']//span[@class='a-text-strike']",
            ".//div[@id='price']//td[contains(@class,'a-text-strike')]",
            "(.//*[@id='price']//span[@class='a-text-strike'])[2]",
            ".//*[@id='buyBoxInner']//*[contains(@class, 'a-text-strike')]",
            ".//*[@id='price']//span[contains(@class, 'priceBlockStrikePriceString')]",
            ".//span[@id='rentListPrice']",
            ".//span[@id='listPrice']",
            ".//*[not(@class='pricePerUnit')]//span[@class='a-price a-text-price a-size-base']/span[@class='a-offscreen']",
            ".//span[@class='a-size-small a-color-secondary aok-align-center basisPrice']//span[@class='a-price a-text-price']/span[@class='a-offscreen']",
        );

        return $this->xpathScalar($paths);
    }

    public function parseManufacturer()
    {
        $brand = $this->xpathScalar(".//a[@id='brand']");
        if (!$brand)
            $brand = $this->xpathScalar(".//*[@id='byline']//*[contains(@class, 'contributorNameID')]");
        return $brand;
    }

    public function parseImg()
    {
        $paths = array(
            ".//img[@id='miniATF_image']/@src",
            ".//img[@id='landingImage']/@data-old-hires",
            ".//img[@id='landingImage']/@data-a-dynamic-image",
            ".//img[@id='landingImage']/@src",
            ".//img[@id='ebooksImgBlkFront']/@src",
            ".//*[@id='fine-art-landingImage']/@src",
            ".//*[@class='dv-dp-packshot js-hide-on-play-left']//img/@src",
            ".//*[@id='main-image']/@src",
            ".//div[@id='mainImageContainer']/img/@src",
            ".//img[@id='imgBlkFront' and not(contains(@src, 'data:image'))]/@src",
            ".//div[@id='imgTagWrapperId']//img/@src",
        );

        $img = $this->xpathScalar($paths);

        if (preg_match('/^data:image/', $img))
            $img = '';

        if (preg_match('/"(https:\/\/.+?)"/', $img, $matches))
            $img = $matches[1];

        if (!$img)
        {
            $dynamic = $this->xpathScalar(".//img[@id='landingImage' or @id='imgBlkFront']/@data-a-dynamic-image");
            if (preg_match('/"(https:\/\/.+?)"/', $dynamic, $matches))
                $img = $matches[1];
        }
        if (!$img)
        {
            $img = $this->xpathScalar(".//img[@id='imgBlkFront']/@src");
            if (preg_match('/^data:image/', $img))
                $img = '';
        }

        if (!$img)
        {
            $img = $this->xpathScalar(".//*[contains(@class, 'imageThumb thumb')]/img/@src");
            $img = preg_replace('/\._.+?\_.jpg/', '.jpg', $img);
        }

        $img = str_replace('._SL1500_.', '._AC_SL520_.', $img);
        $img = str_replace('._SL1200_.', '._AC_SL520_.', $img);
        $img = str_replace('._SL1000_.', '._AC_SL520_.', $img);
        $img = str_replace('._AC_SL1500_.', '._AC_SL520_.', $img);
        $img = str_replace('._AC_UL1192_.', '._AC_SL520_.', $img);
        $img = str_replace('._AC_SL1000_.', '._AC_SL520_.', $img);

        return $img;
    }

    public function isInStock()
    {
        $xpath = array(
            ".//div[@id='availability']/span/text()",
            ".//div[@id='outOfStock']//span[contains(@class, 'a-color-price')]",
        );

        if (!$availability = $this->xpathScalar($xpath))
            return true;

        $availability = trim((string) $this->xpathScalar($availability));

        if (in_array($availability, array('Currently unavailable.', 'Şu anda mevcut değil.', 'Attualmente non disponibile.', 'Momenteel niet verkrijgbaar.', 'Al momento non disponibile.')))
            return false;

        return true;
    }

    public function parseExtra()
    {
        $extra['categoryPath'] = $this->xpathArray(".//div[@id='wayfinding-breadcrumbs_feature_div']//li//a");;

        $extra['comments'] = array();
        $review_titles = array();
        $comments = $this->xpathArray(".//*[contains(@class, 'reviews-content')]//*[contains(@data-hook, 'review-body')]//div[@data-hook]", true);
        if ($comments)
        {
            $users = $this->xpathArray(".//*[contains(@class, 'reviews-content')]//*[@class='a-profile-name']");
            $dates = $this->xpathArray(".//*[contains(@class, 'reviews-content')]//*[@data-hook='review-date']");
            $ratings = $this->xpathArray(".//*[contains(@class, 'reviews-content')]//*[@data-hook='review-star-rating' or @data-hook='cmps-review-star-rating']");
            $review_titles = $this->xpathArray(".//*[contains(@class, 'reviews-content')]//a[@data-hook='review-title']//span[2]");
        }
        else
        {
            $comments = $this->xpathArray(".//*[@id='revMH']//*[contains(@id, 'revData-dpReviewsMostHelpful')]/div[@class='a-section']", true);
            $users = $this->xpathArray(".//*[@id='revMH']//a[@class='noTextDecoration']");
            $dates = $this->xpathArray(".//*[@id='revMH']//span[@class='a-color-secondary']/span[@class='a-color-secondary']");
            $ratings = $this->xpathArray(".//*[@id='revMH']//span[@class='a-icon-alt']");
        }

        for ($i = 0; $i < count($comments); $i++)
        {
            if (isset($users[$i]))
                $comment['name'] = sanitize_text_field($users[$i]);

            $date = $dates[$i];
            if (preg_match('/Reviewed in .+? on (.+)/', $date, $matches))
                $date = $matches[1];
            elseif (preg_match('/(\d.+)/', $date, $matches))
                $date = $matches[1];
            $comment['date'] = strtotime($date);

            $comment['comment'] = '';
            if (isset($review_titles[$i]))
            {
                $review_title = sanitize_text_field($review_titles[$i]);
                $review_title = trim($review_title, ',.!? ');
                $review_title = $review_title . '.';
                $comment['comment'] .= $review_title . ' ';
            }

            $comment['comment'] .= sanitize_text_field($comments[$i]);
            $comment['comment'] = preg_replace('/Read\smore$/', '', $comment['comment']);
            if (isset($ratings[$i]))
                $comment['rating'] = $this->prepareRating($ratings[$i]);
            $extra['comments'][] = $comment;
        }

        preg_match("/\/dp\/(.+?)\//msi", $this->getUrl(), $match);
        $extra['item_id'] = isset($match[1]) ? $match[1] : '';

        $extra['images'] = $this->_parseImages();

        $extra['rating'] = $this->prepareRating($this->xpathScalar(".//*[@id='summaryStars']//i/span"));
        if (!$extra['rating'])
            $extra['rating'] = $this->prepareRating((float) $this->xpathScalar(".//*[@id='acrPopover']//i[contains(@class, 'a-icon a-icon-star')]"));

        $extra['ratingDecimal'] = (float) str_replace(',', '.', $this->xpathScalar(".//*[@id='acrPopover']//i[contains(@class, 'a-icon a-icon-star')]"));
        $extra['ratingCount'] = (int) str_replace(',', '', $this->xpathScalar(".//*[@id='acrCustomerReviewText']"));

        if ($asin = self::parseAsinFromUrl($this->getUrl()))
        {
            $url_parts = parse_url($this->getUrl());
            $extra['reviewUrl'] = $url_parts['scheme'] . '://' . $url_parts['host'] . '/product-reviews/' . $asin . '/';
        }

        if ($description = $this->xpathScalar(".//div[@id='productDescription']", true))
            $extra['product_description'] = TextHelper::sanitizeHtml($description);

        return $extra;
    }

    protected function _parseImages()
    {
        $images = array();
        $results = $this->xpathArray(".//div[@id='altImages']//ul/li[position() > 1]//img[contains(@src, '.jpg') and not(contains(@src, 'play-icon-overlay'))]/@src");

        foreach ($results as $img)
        {
            if (strstr($img, 'play-button'))
                continue;

            $img = preg_replace('/,\d+_\.jpg/', '.jpg', $img);
            $img = preg_replace('/\._.+?_\.jpg/', '.jpg', $img);
            $img = preg_replace('/\._SX\d+_SY\d+_.+?\.jpg/', '.jpg', $img);

            $images[] = $img;
        }
        return $images;
    }

    public function getFeaturesXpath()
    {
        return array(
            array(
                'name' => ".//div[@id='prodDetails']//table//th",
                'value' => ".//div[@id='prodDetails']//table//td",
            ),
            array(
                'name' => ".//div[@id='productOverview_feature_div']//table//td[1]",
                'value' => ".//div[@id='productOverview_feature_div']//table//td[2]",
            ),
            array(
                'name' => ".//table[contains(@id, 'productDetails_techSpec_section')]//th",
                'value' => ".//table[contains(@id, 'productDetails_techSpec_section')]//td",
            ),
            array(
                'name' => ".//table[contains(@id, 'technicalSpecifications_section')]//th",
                'value' => ".//table[contains(@id, 'technicalSpecifications_section')]//td",
            ),
            array(
                'name' => ".//table[contains(@id, 'productDetails_detailBullets_sections')]//th",
                'value' => ".//table[contains(@id, 'productDetails_detailBullets_sections')]//td",
            ),
            array(
                'name-value' => ".//*[@id='productDetailsTable']//li[not(@id) and not(@class)]",
                'separator' => ":",
            ),
            array(
                'name' => ".//*[@id='prodDetails']//td[@class='label']",
                'value' => ".//*[@id='prodDetails']//td[@class='value']",
            ),
            array(
                'name' => ".//*[contains(@id, 'technicalSpecifications_section')]//th",
                'value' => ".//*[contains(@id, 'technicalSpecifications_section')]//td",
            ),
            array(
                'name-value' => ".//div[@id='technical-data']//li",
                'separator' => ":",
            ),
            array(
                'name-value' => ".//div[@id='detail-bullets']//li",
                'separator' => ":",
            ),
            array(
                'name' => ".//div[@id='detailBullets_feature_div']//li/span/span[1]",
                'value' => ".//div[@id='detailBullets_feature_div']//li/span/span[2]",
            ),
            array(
                'name' => ".//div[@id='tech']//table//td[1]",
                'value' => ".//div[@id='tech']//table//td[2]",
            ),
        );
    }

    private function prepareRating($rating_str)
    {
        $rating_parts = explode(' ', (string) $rating_str);
        return TextHelper::ratingPrepare($rating_parts[0]);
    }

    public function getCurrency()
    {
        if (strstr($this->parsePrice(), 'USD'))
            return 'USD';

        if (strstr($this->parsePrice(), 'AUD'))
            return 'AUD';

        if (strstr($this->parsePrice(), 'DKK'))
            return 'DKK';

        if (strstr($this->parsePrice(), 'ILS'))
            return 'ILS';

        if (strstr($this->parsePrice(), 'kr'))
            return 'SEK';

        return AmazonLocales::getCurrencyCode($this->locale);
    }

    public function parseFeatures()
    {
        if (!$xpaths = $this->getFeaturesXpath())
            return array();

        if (isset($xpaths['name']) || isset($xpaths['name-value']))
            $xpaths = array($xpaths);

        foreach ($xpaths as $xpath)
        {
            $names = $values = array();

            if (isset($xpath['name-value']))
            {
                if (!$name_values = $this->xpathArray($xpath['name-value']))
                    continue;

                if (isset($xpath['separator']))
                    $separator = $xpath['separator'];
                else
                    $separator = ':';

                foreach ($name_values as $name_value)
                {
                    $parts = explode($separator, $name_value, 2);
                    if (count($parts) !== 2)
                        continue;

                    $names[] = $parts[0];
                    $values[] = $parts[1];
                }
            }
            elseif (isset($xpath['name']) && isset($xpath['value']))
            {
                $names = $this->xpathArray($xpath['name']);
                $values = $this->xpathArray($xpath['value']);
            }

            if (!$names || !$values || count($names) != count($values))
                continue;

            $features = array();
            for ($i = 0; $i < count($names); $i++)
            {
                $feature = array();
                $names[$i] = preg_replace("/[^\pL\s\d\-\.\+_]+/ui", '', $names[$i]);
                $feature['name'] = ucfirst(\sanitize_text_field(trim($names[$i], " \r\n:-")));
                $feature['value'] = trim(\sanitize_text_field($values[$i]), " \r\n:-");
                $feature['value'] = preg_replace('/(\x{200e}|\x{200f})/u', '', $feature['value']);
                $feature['value'] = str_replace('See more', '', $feature['value']);
                if (in_array($feature['name'], array('Condition', 'Customer Reviews', 'Best Sellers Rank', 'Amazon Best Sellers Rank', 'Average Customer Review', 'Warranty', 'Item #')))
                    continue;
                $features[] = $feature;
            }

            if ($features)
                return $features;
        }

        return array();
    }

    public function parsePromo()
    {
        $paths = array(
            ".//label[contains(@id, 'couponText')]/text()",
            ".//span[@data-csa-c-owner='PromotionsDiscovery']//div[@class='a-alert-content']/text()",
        );

        return $this->xpathScalar($paths);
    }
}
