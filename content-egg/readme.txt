=== Content Egg Pro ===
Contributors: keywordrush.com

All in one solution for creating affiliate websites.

== Installation ==

**Requirements**
* PHP 7.4+
* WordPress 5.9+

This section describes how to install the plugin and get it working.
1. Upload `content-egg` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure plugin settings.
4. You can find manual how to configure each module - [Content Egg User Guide](https://ce-docs.keywordrush.com/)

== Changelog ==

= Version 18.7.0 =
* New: Feed module — added support for FTP and FTPS feed URLs.
* New: Feed module — added support for GZIP archive format.
* New: Feed module — added XML processor option.

= Version 18.6.0 =
* New: Safe placeholders to use for SubIDs/deeplinks.
* Improved: Feed modules are now used for automatic price comparison for Import tools.

= Version 18.5.1 =
* Fix: Content module clicks were excluded from statistics.

= Version 18.5.0 =
* New: Clicks Statistics dashboard.
* New: Track Clicks With Redirect - record aggregated clicks for local redirect links.
* New: Track Clicks Without Redirect - count clicks on direct affiliate links.
* New: Local redirect links with human-readable product-title slugs.
* New: Click Stats Retention (days) setting.
* New: Redirect Status Code setting for local redirects.
* New: “All Products” admin table - shows total clicks and last 30 days.
* New: Post edit screen — per-product total and last 30 days clicks.
* New: Deeplinks - support for advanced placeholders.
* Improved: Added extra parameters to GA4 events.
* Fix: Multiple price history charts now display correct data.

= Version 18.1.0 =
* New: Added Coupang module.

= Version 18.0.0 =
* New: Bridge Pages import from the post editor.
* New: Preset option — make imported Bridge Pages canonical.
* New: Frontend setting — link destination preference (affiliate vs bridge).
* New: Shortcode parameter — link_target: affiliate|bridge|auto.
* New: Products block option — Link Destination.
* New: Products block option — Source Post ID.
* New: Setting — Bridge Button Text.

= Version 17.4.1 =
* Deprecated: ShareASale is shutting down and is fully transitioning to the Awin platform.

= Version 17.4.0 =
* New: Added `group_pick` shortcode parameter to select one product per group.

= Version 17.3.4 =
* Fix: Importing to the selected category.
* Fix: Unable to disable "Skip import if product already exists" option.

= Version 17.3.1 =
* Improvement: Enhanced compatibility with older MySQL versions.

= Version 17.3.0 =
* New: Added "Generate Search Keyword" option to import presets.
* New: Import presets now support any number of custom fields.

= Version 17.2.0 =
* New: Feed module - added "additional image link" field for gallery images.
* New: Added support for GPT-5 models.
* New: Content modules - auto-update by keyword.
* New: Content modules - support for the keyword shortcode parameter.

= Version 17.1.0 =
* New: Bol.com module – added product category option.
* Fix: Bol.com module – gallery images.

= Version 17.0.0 =
* New: Search and import products by keywords.
* New: Bulk import using multiple keywords or product URLs.
* New: Feed Import – import all products from a feed with one click.
* New: Auto Import – scheduled imports based on keywords.
* New: Import Presets – reusable settings for import configuration.
* New: Import Queue – background task manager for all imports.
* New: Bolcom module – added gallery images support.
* New: WooCommerce sync gallery option.
* New: External gallery image support via direct image URLs.
* New: WP Media Library gallery scheduler.
* New: WooCommerce Brand taxonomy support for product sync.
* Deprecated: Autoblogging feature.

= Version 16.3.0 =
* New: Added Logo Source option with support for Clearbit, Brandfetch, and Logodev.
* New: Purge Cached Logos link.
* New: Added Logo Hotlinking option.
* New: Added support for attribute placeholders %PRODUCT.ATTRIBUTE.attribute-name% in prefill custom fields.
* Improved: Prefill settings are now automatically saved and restored.

= Version 16.2.0 =
* New: AI-powered field mapping in the Feed module.
* New: Feed sync interval setting to control how often product feeds are re-synced with the local database.
* New: "Reload feed data" button in the Feed module settings.
* New: Subtitle field added to the Feed module.
* Improved: "All Products" page now ignore trashed posts.
* Improved: Autoblogging now supports Draft and Private post statuses.
* Improved: Significantly reduced memory usage when processing large ZIP-compressed feeds.

= Version 16.1.0 =
* New: Rakuten module – migrated to the new API version.
* New: Rakuten module – added support for price updates.
* New: Price Drops widget – added ability to include or exclude specific module IDs.
* New: Amazon API module – added ability to search using category URLs.
* Improvement: Tradetracker module – improved duplicate filtering with stricter logic.

= Version 16.0.0 =
* New: Prefill tool to add products to existing posts, with AI-powered product selection.
* New: Prefill now runs in the background via WP-Cron.
* New: Prefill button on the post edit page to automatically add product offers.
* New: Export/Import plugin settings to a file.
* New: Export/Import module settings to a file.

= Version 15.11.0 =
* Improvement: Enhanced EAN search in the Tradetracker module.
* Improvement: Added support for WordPress 6.8.
* Fix: Security patch.

= Version 15.10.1 =
* Deprecated: The Udemy module is now deprecated as Udemy has closed their API.

= Version 15.10.0 =
* New: Bol.com module – create bestseller lists using a category URL.
* New: AI prompts to generate concise bullet points and product subtitles.

= Version 15.9.0 =
* New: Added support for Amazon.ie.

= Version 15.8.0 =
* New: Clone option added for all affiliate modules.
* New: Feed modules can now be deleted along with all associated data.
* New: Support for OpenRouter, a unified interface for LLMs.
* New: Added AI models – GPT-4.5-preview and Claude-3.7-Sonnet-latest.

= Version 15.7.0 =
* New: Udemy module – Full course description option.

= Version 15.6.0 =
* New: Added local proxy support for external images.

= Version 15.5.0 =
*  Improvement: Updated Shopee module to support s.shopee link format.

= Version 15.4.0 =
* New: WooCommerce Settings: Added a shortcode for Single Product Pages.
* New: WooCommerce Settings: Added a shortcode for Archive Pages.

= Version 15.3.0 =
* New: Added a new template: Review Box.
* New: Added a new template: Top Listings with "Show More" Button.
* New: Introduced a Feed Module supporting regex syntax for extracting product data from feed fields.
* Improvements: Added a Products IDs Filter to the CE block.
* Improvements: Autoupdate existing Shopee links to the new format.

= Version 15.2.0 =
* New: Bol.com module now supports search by product ID or product URL.

= Version 15.1.0 =
* New: Added a new template, "Sorted offers list with store logos and buttons".

= Version 15.0.2 =
* Improvement: Price history charts: dates follow WordPress date settings.
* Improvement: Price history charts: prices follow locale settings.

= Version 15.0.1 =
* Improvement: Added support for Claude 3.5 haiku.

= Version 15.0.0 =
* New: Introduced Gutenberg product blocks for product display.
* New: Enabled attribute mapping support in Feed modules.
* Improvement: Enhanced template designs.
* Deprecated: Discontinued support for the Pepperjam module.

= Version 14.2.0 =
* New: `hide=coupon_reveal` shortcode parameter added.
* Improvement: Avantlink module - merchant-to-domain filter added.
* Improvement: Frontend search - shortcode parameters applied.

= Version 14.1.1 =
* Fix: Price alert JS included for backward compatibility with Rehub templates.

= Version 14.1.0 =
* New: Added full Bootstrap 5 CSS for custom templates.

= Version 14.0.0 =
* New: Complete redesign and improvement of all templates.
* New: Dark mode added, which can be enabled via global settings or shortcode parameter.
* New: Affiliate link tracking integrated with GA4 custom events for better analytics.
* New: Price history charts now show the lowest prices from multiple merchants.
* New: Reveal coupon feature added.
* New: Affiliate disclaimer option for posts.
* New: Affiliate disclaimer feature for individual product blocks.
* New: Product badge label and badge color fields.
* New: Badge icons available.
* New: Added product subtitle field for additional details.
* New: Product rating field.
* New: Product order number field added.
* New: Button variants added, customizable through global options or shortcode parameters.
* New: Option to display Amazon price update date.
* New: `exclude_modules` shortcode parameter.
* New: `tabs_type` shortcode parameter.
* New: `border` and `border_color` shortcode parameters.
* New: `start_number` shortcode parameter.
* New: `title_tag` shortcode parameter.
* New: `img_ratio` shortcode parameter.
* New: Shortcode parameters: `cols_sm`, `cols_md`, `cols_lg`, `cols_xl`, `cols_xxl`.
* New: Show/hide values: `price_update` and `disclaimer`.
* New: Show/hide values: `cols_order`, `shipping_cost`, `new_used_price`, `logo`, `prime`.
* New: Show/hide values: `button`, `percentageSaved`, `priceOld`, `badge`, `merchant`.
* New: CSS purge optimization for faster load times and cleaner code.
* New: Default sorting by product badge and module priority for improved user experience.
* New: Amazon module: Prime prices.
* Deprecated: "Title" module option removed.
* Deprecated: "Show stock status" option removed.
* Deprecated: Shop info > Popup type option removed.
* Deprecated: `btn_color` and `btn_class` shortcode parameters.
* Deprecated: QwantImages module.
* Deprecated: Twitter module.

= Version 13.3.0 =
* New: Shipping cost field for the Awin module.
* New: Added Deeplink settings to the CJ Products module.

= Version 13.2.0 =
* New: Smart Groups added to categorize products with AI.

= Version 13.1.0 =
* New: Added Deeplink settings to the NoAPI Amazon module.

= Version 13.0.0 =
* New: AI features to generate unique product content.
* New: Clear, compact interface for CE metabox.
* New: Amazon NoAPI module: Promocodes support.
* New: 'Description only' template added.
* Improvement: Feed module limit increased to 50 individual feed modules.
* Improvement: Amazon module: Product features now uses as product description.

= Version 12.16.0 =
* New: ChatGTP Template Creator: https://ce-docs.keywordrush.com/custom-templates/chatgtp-template-creator

= Version 12.15.5 =
* Fix: AmazonNoAPI module for IN locale.

= Version 12.15.2 =
* Improvement: Enhanced grid templates.

= Version 12.15.0 =
* New: Sovrn (Viglink) module updated with the new Sovrn Product API.

= Version 12.14.0 =
* New: Greenshift template: Grid template added.

= Version 12.13.0 =
* New: AE:Amazon modules now support search by ASIN.
* Fix: Grid template fix.

= Version 12.12.0 =
* New: Kieskeurignl module - Migrated to the new API.

= Version 12.11.0 =
* New: The Admitad coupons module can now return all coupons by advertiser ID.

= Version 12.10.0 =
* New: Added large image format to the Pixabay module.

= Version 12.9.0 =
* New: Preparation for integration with the GreenShift plugin.

= Version 12.8.0 =
* New: Added an option in the Amazon NoAPI module to delegate the task of updating prices to the Amazon module via API.
* New: Amazon NoAPI module: option to hide outdated prices.
* New: Amazon module: option to hide prices.

= Version 12.7.0 =
* New: Bol.com module - Migrated to the new Marketing Catalog API.

= Version 12.6.0 =
* New: Support for Scrapeowl added to the AmazonNoAPI module.

= Version 12.5.0 =
* Improvement: The AmazonNoAPI module now functions as a web scraper.
* Improvement: The AmazonNoAPI module now supports all Amazon locales.

= Version 12.4.0 =
* New: Added the option to hide prices in the AmazonNoAPI module.

= Version 12.3.0 =
* Added: Shopee module now supports searching by product URLs or product ID.
* Added: Added Sub ID option to the Shopee module.
* Added: Introduced a price update feature in the Shopee module.

= Version 12.2.0 =
* Added: Shipping cost field mapping for the Feed module.
* Added: New template "Sorted list with store logos and shipping price".
* Added: New template "Sorted list with store logos and shipping price + group pills".
* Added: Sorting parameter by total_price.
* Deprecated: The Viglink module is now deprecated.

= 12.1.6 =
* Improvement: Added 'img+url' show parameter for the "Customizable" template.

= 12.1.1 =
* Improvement: Price history charts.

= 12.1.0 =
* New: Added "Merchant name" configuration for the eBay module.
* New: Implemented WP-CLI command to trigger updates by keyword.

= 12.0.4 =
* New: Kieskeurignl module now includes an option to exclude specific domains.
* New: Added a button to copy product IDs for use in Too Much Niche articles.
* Improvement: Implementation of module priority for selecting featured images.

= 12.0.0 =
* New: TooMuchNiche plugin compatibility.
* New: Aliexpress module: Ship to country option.

= 11.12.0 =
* New: Global autoupdate keywords.
* New: EAN synchronization for WooCommerce.
* New: ISBN synchronization for WooCommerce.
* New: Feed modules: ISBN field mapping.
* New: Autoblogging: Avoid product duplicates by EAN/ISBN.

= 11.11.0 =
* New: Shop coupons (experimental feature).

= 11.10.0 =
* New: Block shortcode parameter: remove_duplicates_by="product field name".
* New: Shop info popup type: Modal dialog or popover.
* Improvement: Shop info settings: TinyMCE editor.
* Improvement: Shop info settings: Shortcode support.

= 11.9.0 =
* New: Amazon module: Specifications template.

= 11.8.0 =
* New: Feed modules: Short description mapping.

= 11.7.0 =
* New: WooCommerce settings: Sync description (disabled by default).

= 11.6.1 =
* Improvement: More fields for schema markup.

= 11.6.0 =
* New: Module shortcodes: keyword parameter.

= 11.5.0 =
* New: Option to process out of stock products for WooCommerce.

= 11.4.0 =
* New: Trovaprezzi module.
* New: Pass-through query parameters to redirect links.
* Improvement: Update cron starts every 10 minutes.
* Improvement: Internal update limits have been increased.
* Fix: Shopee module: Locale selection.

= 11.3.0 =
* Improvement: Aliexpress module: Migration to new API platform.

= 11.2.0 =
* New: Shopee module.
* New: Custom sorting for global shortcodes.
* New: Feed module: Strict mode for full text search.
* New: Feed module: Option to search products in stock only.
* New: Shortcodes: Filtering products by EANs.
* New: Product tags can be used for Deeplink settings.
* Improvement: Feed modules: Price filter is applied to EAN search.

= 11.1.0 =
* Improvement: Kieskeurignl module: Price update feature.
* Improvement: Frontend search: Support for the latest WP version.

= 11.0.0 =
* New: Bestbuy module.
* New: Kieskeurignl module.
* Improvement: Option for Brand taxonomy synchronization.

= 10.12.0 =
* New: Brand taxonomy synchronization for Rehub theme.

= 10.11.0 =
* New: "Update prices" button on post edit pages.
* New: "Refresh listings" button on post edit pages.
* New: Displaying EAN codes on post edit pages.

= 10.10.0 =
* New: Option to add Product/AggregateOffer markup to posts.
* New: Feed modules: XML mapping: xPath syntax is allowed.

= 10.9.0 =
* Improvement: Block shortcodes: Sorting is applied before limit/offset.

= 10.8.2 =
* Improvement: Extra product data for for autoblogging tags.

= 10.8.0 =
* New: Amazon module: Amazon.com.be support.
* New: Feed module: Exact phrase search.

= 10.7.0 =
* New: Ebay module: Search by URL/product ID.
* Fix: Ebay module: local redirects.

= 10.6.0 =
* New: Block template: Button with price comparison popup.
* New: Block template: Product card with price comparison popup.

= 10.5.0 =
* Improvement: Group matching by keyword search: keyword -> group name.

= 10.4.0 =
* Improvement: Bolcom module: New API authentication method.

= 10.3.0 =
* New: Option to add AggregateOffer to product structured data.

= 10.2.0 =
* New: Autoblog: Slug template.

= 10.1.0 =
* New: Feed module: JSON feed format.

= 10.0.2 =
* Fix: Tradetracker Products: Reference.

= 10.0.0 =
* New: Multiple keyword search.
* Improvement: Alt tag for featured images.
* Improvement: Feed modules: Update by page view.

= 9.9.2 =
* New: Amazon module: Amazon.eg support.

= 9.9.0 =
* New: Ebay module: Added support for AT and CH locales.
* New: Bolcom module: Description type.

= 9.8.1 =
* New: REST API: Return woo synced offer only.

= 9.8.0 =
* New: REST API for module data.
* New: Module shortcodes: sort parameter.

= 9.7.0 =
* New: Ebay module: Image size option.
* Improvement: Tradedoubler module: Migration to the latest API syntax.

= 9.6.0 =
* New: Ebay module: Priority listing filter.

= 9.5.0 =
* New: Offer modules: Default currency option.
* Improvement: Impactradius module: Migration to API v12.

= 9.4.1 =
* Fix: Ebay module: Local redirects.
* Fix: Feed modules: Stock status.
* Fix: Tradedoubler module: Unique IDs.

= 9.4.0 =
* New: Shortcode parameters: add_query_arg.
* Improvement: Feed modules: Feeds will be updated twice a day.
* Improvement: Offer modules: Domain editing capability.

= 9.3.0 =
* New: Ebay module through Browse API.
* New: Amazon module: Amazon.pl support.
* Deprecated: Ebay (legacy) module.

= 9.2.4 =
* Fix: Bing Images API.
* Fix: Aliexpress currency code.

= 9.2.3 =
* New: Feed module: Support for XML format.
* Fix: AMP styles.

= 9.2.2 =
* Improvement: Locale filter for block shortcodes.

= 9.2.0 =
* New: Amazon No API module.
* Improvement: Kelkoo module: Migration to new API platform.

= 9.0.0 =
* New: Merchant settings: Shop info.
* New: Feed module: Deeplink.

= 8.9.0 =
* New: Block template: Top listing.
* Improvement: Walmart module: Search by EAN.

= 8.8.3 =
* Improvement: Feed module: Feed encoding option.

= 8.8.1 =
* Improvement: Compatibility with WooCommerce 5.1.0.

= 8.8.0 =
* Improvement: Walmart module: Migration to new API platform.
* Improvement: Walmart module: Search by URL feature.

= 8.7.0 =
* New: License status notifications.
* Improvement: Ebay module: OAuth authentication.
* Fix: Feed modules: WooCommerce sync.

= 8.6.0 =
* New: Feed module to work with custom CSV feeds.
* New: Option to import all product URLs/EANs for Feed modules.
* Improvement: Optimisation for amazon images.
* Improvement: Glyphicons are no longer used on frontend.

= 8.5.0 =
* Improvement: Ebay module: New tracking link format.

= 8.3.5 =
* Fix: Impact Radius API.

= 8.3.3 =
* Improvement: Daisycon module: search by EAN.
* Improvement: Minor changes and fixes.

= 8.3.0 =
* New: Price alert email templates.
* New: Frontend text settings.
* New: Custom Amazon disclaimer.

= 8.2.0 =
* New: Price alerts option: General alert for all products in a post.
* Improvement: Amazon module: Search by multiple ASINs separated by commas.

= 8.1.0 =
* New: Amazon template: Add all to cart button.

= 8.0.0 =
* New: Amazon module: Amazon.se support.
* New: Template settings (price color, stock status, etc.)
* New: Block template: Price comparison card.
* New: Block template: Buttons row.
* New: Block template: Sorted offers list with no prices.
* New: Block template: Text links.
* New: Module template: Product card (no features).
* New: Shortcode parameter: btn_text.
* New: Youtube module: Featured image.
* Improvement: Better mobile templates.
* Improvement: Better desktop templates.
* Improvement: Better module management.
* Improvement: Better plugin settings.
* Improvement: Better widget templates.
* Improvement: Offer module: Set product status to OutOfStock if 404 error.

= 7.3.0 =
* New: Amazon module: Option to hide large logos.
* New: AE modules: Option to hide large logos.
* Deprecated: Optimisemedia module.

= 7.2.0 =
* Improvement: Autoblogging hints: Amazon keywords according to the set Site language value.

= 7.1.2 =
* Fix: Admitad Coupons: Fixed download URI.

= 7.1.0 =
* New: Possibility to create custom modules (feature for developers).

= 7.0.0 =
* New: Amazon module: Amazon.sa support.

= 6.9.1 =
* Improvement: Some basic HTML tags are allowed in product description.

= 6.9.0 =
* Improvement: CJ Products: Migration to new Product Search API.
* Fix: Aliexpress module: Some images are not saved to local server.

= 6.8.2 =
* Improvement: Amazon module: New product prices takes precedence over used products.

= 6.8.0 =
* New: Option: Rel attribute for affiliate links.
* New: Offer module: Custom merchant logos.
* New: General settings: Custom merchant logos.
* New: WooCommerce settings: Show price per unit.
* Improvement: Offer module: Auto-detection of a real domain in deeplinks.

= 6.7.5 =
* Fix: Critical error in datafeed modules.

= 6.7.2 =
* Fix: Webgains: Not all products are imported into the local database.

= 6.7.1 =
* Improvement: Datafeed modules: Better error handling.

= 6.7.0 =
* New: Webgains module via data feeds.

= 6.6.2 =
* Improvement: External featured images: Added support for product structured data.
* Improvement: External featured images: Added support for Yoast schema graph.
* Fix: Linkshare module: prices.

= 6.6.1 =
* Improvement: Links to new documentation https://ce-docs.keywordrush.com
* Improvement: Awin module: Feed export in background.

= 6.6.0 =
* New: Tradetracker Products: "Out of stock" option.
* New: Ebay module: Seller search filter.
* Fix: Ebay module: prices.

= 6.5.0 =
* New: Aliexpress module through new API. The old module was marked as "legacy".

= 6.4.0 =
* New: Awin module: Search partial URL.
* Fix: Tradetracker modules: PHP 7.3 support.
* Fix: External featured images: WP 5.4 support.

= 6.3.0 =
* New: Amazon module: Amazon.nl support.

= 6.2.6 =
* Improvement: Walmart module: tracking links fix.

= 6.2.0 =
* New: Awin module: Search by EAN code.
* New: Walmart module: Deeplink option.

= 6.1.0 =
* New: Offer Module: Global XPath and Deeplink settings.
* New: Offer Module: Multiple XPath queries.
* New: Offer Module: Display the last occurred error.

= 6.0.0 =
* New: Amazon Module: Search by filtering for Prime, Fulfillment by Amazon, Free Shipping, Amazon Global, Minimum rating.
* New: Amazon Module: Currency of preference, Languages of preference.
* New: Tradetracker Products Module: Price update (experemental feature).
* New: Product Condition option for Autoblogging task (supported by Ebay and Amazon modules).
* Improvement: Amazon Module: Migration to Product Advertising API 5.0.
* Improvement: Ebay Module: API requests through HTTPS service endpoints.
* Improvement: Lomade Module: Migration to Lomade API v3.
* Fix: Impactradius Module: Stock status for some merchants.
* Removed: Amazon Module: Custom tags are no longer supported, default tags will be used instead.

= 5.5.0 =
* New: Aliexpress module through new API. The old module was marked as "legacy".

= 5.4.5 =
* New: Multiple deeplinks for subdomains.

= 5.4.0 =
* New: Daisycon module via data feeds.
* New: Delay in seconds between each post prefill.
* New: "hide" shortcode parameter to hide some product data.
* New: "customizable" block template + "show" parameter to show only one information about a product (like price, title, button).
* New: Overwrite WooCommerce button text for external products.

= 5.3.1 =
* Improvement: Walmart module: Impact Radius support.

= 5.3.0 =
* New: External featured images by URL.

= 5.2.0 =
* New: Awin module via data feeds.

= 5.1.6 =
* New: Product filter in shortcodes: https://www.keywordrush.com/docs/content-egg/Shortcodes.html#products
* New: Cashback Tracker plugin integration: https://www.keywordrush.com/cashbacktracker

= 5.1.4 =
* Improvement: Impactradius module: API v11 migration.

= 5.1.2 =
* Improvement: Amazon disclaimer tooltips for mobile browsers.

= 5.1.0 =
* New: Shortcode parameter: disable_features for "item" template.

= 5.0.0 =
* New: Product groups in shortcodes.
* New: Block template: Sorted offers list with product images + group tabs.
* New: Block template: Sorted offers list with store logos + group pills.
* New: AE modules: Option to hide small logos.
* New: Shortcode parameter: cols - number of columns for grid templates.
* Fix: GoogleNews module results.

= 4.9.9 =
* New: Lomadee link builder. Syntax for Deeplink: [lomadee][sourceId]
* Improvement: CJ modules: Developer keys have been deprecated. Please use personal access tokens instead.

= 4.9.8 =
* Improvement: Envato module: Deeplink option.
* Deprecated: Affilinet Coupons module.
* Deprecated: Affilinet Products module.

= 4.9.0 =
* New: Avantlink Products module.
* New: Linkwise module.
* New: Product management tool.
* New: Option 'Out of Stock products' - how to deal with Out of Stock products.
* New: Accept privacy policy checkbox for price drop alert.
* New: Module priority is applied to price sorting.
* New: "Buy Now" button tags: %MERCHANT%, %DOMAIN%, %PRICE%, %STOCK_STATUS%.
* Improvement: Tracking availability of products.
* Improvement: WooCommerce synchronization: stock status.
* Improvement: Unsubscribe link in confirmation email.
* Improvement: CJ Products: Added price update feature.
* Improvement: Flipkart: Mobile tracking URLs.
* Improvement: Flipkart: Deeplink option.
* Improvement: Flipkart: Attributes filter.
* Improvement: Ability to edit merchant name and domain.
* Fix: QwantImages module search.

= 4.8.0 =
* New: GdeSlon module options: Exclude Shop ID, Parked domain.
* Fix: GdeSlon module multiple Shop ID filter.
* Fix: Impactradius module: product search.
* Fix: CJ Products module: Sorting.

= 4.7.0 =
* New: Forced links update for Amazon module.
* New: Ebay locales: HK, MY, PH, PL, SG.

= 4.6.1 =
* Fix: Kelkoo module prices.

= 4.6.0 =
* New: Added support for Amazon Australia.
* Improvement: Price conversion to one currency when product is selected for woo sync.

= 4.5.0 =
* New: Kelkoo module.
* New: Flipkart module: Search by product URL/product ID.
* New: Price alert subscription report: added delete URL and unsubscribe URL.
* New: Viglink module: Default currency code for search by URL feature.
* New: Viglink module: Deeplink setting for search by URL feature.
* Fix: Dublicate images during update by keyword.

= 4.4.4 =
* Fix: Flipkart module search.
* Fix: Currency converter to EUR.

= 4.4.2 =
* Fix: Aliexpress module: Price update fix.

= 4.4.1 =
* New: Currency shortcode parameter to convert all prices to one currency: [content-egg-block template=offers_grid currency=EUR]

= 4.4.0 =
* New: Price Movers widget.
* New: Price Movers shortcode: [content-egg-price-movers].
* New: Viglink price update for products added by direct URL.
* New: Block template: Grid with prices (3 column).
* New: Sort order (asc/desc) for block shortcodes: [content-egg-block template=offers_list order=desc].
* New: Amazon price disclaimer.
* New: Amazon module: Show small logos option.
* New: WooCommerce synchronization for Offer module.
* New: Сurrency converter for order products by price.
* New: Ability to edit Domain field for products.

= 4.3.1 =
* Improvement: Currency converter service via ECB.
* Fix: Affilinet Products: https links.

= 4.3.0 =
* New: Google Images module.
* New: Qwant Images module.
* New: Lomadee Products module.
* New: Lomadee Coupons module.
* New: Coupon module: Hide expired coupons option.
* New: Coupon module: Hide future coupons option.
* Improvement: Bing Images module: API changed from v5 to v7.
* Improvement: Related Keywords module: API changed from v5 to v7.
* Improvement: Module templates.
* Fix: Skimlinks Coupons module: Site Id option.
* Fix: Google News module results.

= 4.2.0 =
* New: Profitshare link builder. Syntax for Deeplink: [profitshare][api_user][api_key]
* Improvement: Paytm module: paytm.com converted to paytmmall.com.
* Improvement: Udemu module templates.

= 4.1.0 =
* New: Walmart module.
* New: Bolcom module.
* New: Coupon module.
* New: Show price update date for WooCommerce products.
* New: Ability to edit product attributes.
* New: Block template: Price comparison widget.
* New: Block template: Grid without price (4 column).
* New: Block template: Price history for lowest price product.
* New: Block template: Price alert for lowest price product.
* New: Regular expression in Deeplink settings: [regex][pattern][replacement].
* Improvement: Synchronize attributes automatically for synchronized product.
* Improvement: Prepopulate user email for exist user in price alert form.
* Improvement: Ebay module: Ebay.in Affilite ID.
* Improvement: Linkshare: Filter dublicates option.
* Improvement: Module templates.
* Fix: Redirect links with post_id param in shortcode.
* Deprecated: Admitad Products module.

= 4.0.0 =
* New: Skimlinks Coupons module.
* New: Viglink module: Search by product URL.

= 3.9.1 =
* Fix: Admitad module.
* Fix: Autoblogging default category.

= 3.9.0 =
* New: Viglink module.
* New: WooCommerce products synchronization.
* New: WooCommerce attributes synchronization (global and custom).
* New: WooCommerce autoblogging.
* New: Curency converter for WooCommerce synchronization.
* New: Latin slugs for WooCommerce attributes.
* New: WooCommerce reviews rating for AE modules.
* New: WooCommerce attributes filter (auto/blacklist/whitelist).
* Fix: Tradedoubler price update.
* Fix: Subscribers CSV export.
* Improvement: 'product-search' filter for Frontend search.
* Improvement: User Guide - http://www.keywordrush.com/en/docs/content-egg/

= 3.8.0 =
* New: Post type for Fill utility.
* New: Post status for Fill utility.
* New: Сustom fields for Fill utility.
* New: Replacement tags: %RANDOM%, %RANDOM(10,50)%
* New: Subscribers CSV export.
* Improvement: Amazon: Getting price for parent products.
* Improvement: Affilinet: https affilite links support.
* Fix: Keywords parser.

= 3.7.0 =
* New: Price filter in search form.
* New: Price filter for autoupdate (Amazon, Ebay, Aliexpress).
* New: Amazon.com.mx support.
* New: Search by product URL (Amazon, Aliexpress).
* New: Aliexpress search by product ID.
* New: Prefill from Arbitrary custom field keyword source.
* New: Ebay - AvailableTo filter.

= 3.6.3 =
* Fix: Price update by cron.

= 3.6.0 =
* New: Tradetracker Products module.
* New: Tradetracker Coupons module.
* New: Autoblogging: Minimum reviews required.
* New: Autoblogging: Dynamic categories.
* Improvement: Flipkart module: Tracking parameters.
* Improvement: Offer module: Old price.
* Improvement: GoogleBooks module: Country option.
* Improvement: Ability to use AE modules as Search modules.
* Improvement: NGN currency support.
* Improvement: Loco Translate ready.

= 3.5.3 =
* Improvement: Udemu module: Filter courses by subcategory.
* Fix: Frontend product search.

= 3.5.0 =
* New: Frontend product search.
* New: Buy now button text / Coupon button text options.
* Improvement: Prefix for catalog url: [catalog limit=3]
* Improvement: IW language for templates.
* Fix: Amazon JP prices.
* Fix: Amazon price update if more than 10 items on page.

= 3.4.0 =
* New: Udemy module.
* New: Envato module.
* New: Short redirect url.
* New: Custom redirect prefix.
* New: Settings for From Name and From Email.
* New: Ability to set product/catalog url as keyword for AE modules.
* Improvement: SSL ready.
* Improvement: Dates in localized format.
* Improvement: Dynamically changed Deeplinks.

= 3.3.0 =
* New: Tags for autoblogging.
* Improvement: Dynamically changed deeplinks for AE modules.

= 3.2.0 =
* New: Pepperjam module (Pepperjam Network).
* New: Block template: Price statistics.
* New: Ability to add reviews as post comments for AE modules.
* New: Rating field for Offer module.
* New: Support for AMP plugin.
* Fix: Hot trends keyword keyword tool.
* Fix: Product keyword tool.

= 3.1.0 =
* New: Deeplink setting for Ebay module.
* New: Minus words for Fill utility.

= 3.0.0 =
* New: Offer module - manually create offer from any site with price update.
* New: Impactradius module.
* New: BingImages - new Cognitive Services API.
* New: RelatedKeywords - new Cognitive Services API.
* New: Post type for autoblogging.
* New: Custom fields for autoblogging.
* New: Main product for autoblogging. You can use tags: %PRODUCT.title%, %PRODUCT.price%
* New: Separate keywords for modules - autoblogging.
* New: Search by EAN for modules: Affilinet Products, Amazon, Tradedoubler Products, Impactradius, Zanox.
* New: Search by ASIN for Amazon module.
* New: Button color option.
* New: Source language in English.

= 2.9.0 =
* New: Products update via cron job.
* New: Affiliatewindow price update.
* New: Aliexpress price update.
* New: Ebay price update.
* New: Flipkart price update.
* New: GdeSlon price update.
* New: Ozon price update.
* New: Paytm price update.
* New: Tradedoubler price update.
* New: Zanox price update.
* New: Next, offset, limit params for block shortcode [content-egg-block template=offers_list next=3].
* New: Aliexpress module options: High quality items, Language, Local currency.
* New: Merchant visible url.
* New: Merchant logo.
* New: Fill from post tags.
* New: Block template: All offers list with logos.
* Improvement: Module templates.

= 2.8.0 =
* New: Price tracker.
* New: Price alert.
* New: Title shortcode param [content-egg module=Amazon title="My Title"].
* New: Post_id shortcode param [content-egg module=Amazon post_id=123].
* Fix: Admitad Products search.
* Fix: Same Amazon ASIN in different locales.

= 2.7.0 =
* New: PayTM module.
* New: Admitad Products module.
* New: 301 local redirect for outbound affiliate links.

= 2.6.0 =
* New: Fkipkart module.
* New: Autoblogging batch creation.
* Improvement: Fill utility now works for all post types that are checked in the general CE settings.
* Improvement: Fill utility now works for post statuses: 'publish', 'future'.

= 2.5.0 =
* New: Affiliate Egg plugin integration.
* Fix: Bug Fixes.

= 2.4.0 =
* New: Admitad Coupons module.
* New: Affilitewindow module.
* New: Otimisemedia module.
* New: Tradedoubler Products module.
* New: Tradedoubler Coupons module.
* New: Ability to add modules to existing posts.
* New: Locale parameter for amazon shortcode: [content-egg module=Amazon locale=US]
* Deprecated: Freebase module. Freebase API has been officially closed.

= 2.3.0 =
* New: Amazon module: Custom associate tag.
* New: Amazon module: 50 results now available.
* New: Drag and drop for order of items.

= 2.2.0 =
* New: Pixabay module.
* New: Import/export settings.
* New: Keyword parsers for autoblogging.
* New: Keyword tools.
* New: Amazon module: Multi-locale support.
* New: Amazon module: Save images locally.
* New: Amazon module: Rewrite image urls when using https.

= 2.1.0 =
* New: Shareasale module.
* New: CityadsProducts module.
* New: Ozon module.
* Fix: Amazon decode url.
* Removed: Google Images module. Google Images Search API has been officially closed.

= 2.0.0 =
* New: Clickbank module.
* New: Related Keywords module.
* New: RSS Fetcher module.
* New: Post Types option.
* New: Filter bots option.
* New: Amazon module: lowestNewPrice & lowestUsedPrice.
* Improvement: Module templates.
* Fix: Update prices for products on single page.
* Fix: Amazon last update date display.
* Removed: Amazon customer reviews parser has become unstable and is no longer available.

= 1.9.0 =
* New: Autoblogging!
* New: Priority option for modules.
* New: "Compare" template for Amazon.
* Improvement: Module templates.

= 1.8.0 =
* New: Linkshare module.
* New: Affilinet Products module.
* New: Affilinet Coupons module.
* New: GdeSlon module.
* New: Content egg block shortcodes.
* Fix: Amazon IN/BR locale products search.

= 1.0.0 =
* Initial release.