# بازبینی SEO و گیت‌های پیش از ادغام فروشگاه Bamero

**تاریخ بررسی:** ۱۲ نوامبر ۲۰۲۶. این بازبینی برای پوستهٔ classic PHP در شاخهٔ PR شمارهٔ ۱ است. تصمیم‌های زیر بر مستندات رسمی WordPress، WooCommerce، Google و W3C تکیه دارند. نصب زنده، دامنه، افزونه‌های فعال، تنظیمات مدیر و کاتالوگ واقعی در این محیط تأیید نشده‌اند؛ بنابراین وضعیت «نیازمند آزمون زنده» با انجام‌شدن در production یکسان نیست.

## نتیجهٔ کد و حدود ادغام

دو یافتهٔ بازبینی خودکار اصلاح شد: لوگوی سفارشی WordPress دیگر داخل پیوند خانهٔ دیگری قرار نمی‌گیرد و دور آن wrapper مستقلِ ناحیهٔ grid باقی می‌ماند؛ عنوان آرشیو با `wp_kses_post()` پاک‌سازی می‌شود تا markup بی‌خطر هسته مثل `span` قابل نمایش بماند. فهرست نوشته‌ها نیز اکنون یک H1 برای صفحهٔ نوشته‌ها دارد و عنوان کارت هر نوشته H2 است. WooCommerce breadcrumb موجود را حفظ کردیم و نام landmark آن را از فیلتر رسمی به فارسیِ امن تغییر دادیم. آزمون Node این قراردادها، `title-tag`، `wp_head()`، وجود 404 سفارشی، یک H1 در هر قالب سفارشی و جلوگیری از حذف/تکرار breadcrumb را کنترل می‌کند.

هیچ دامنه، تگ canonical مطلق، فایل robots/sitemap ثانویه، JSON-LD کسب‌وکار، favicon ساختگی، توضیح متای عمومی یا فایل `llms.txt` حدسی به مخزن افزوده نشده است. مواردی که به DNS، تنظیمات WordPress، دادهٔ فروشگاه یا HTML نهایی وابسته‌اند تا staging واقعی قابل تأیید نیستند.

## ۱. Breadcrumbs

WooCommerce 10.8.1 خروجی breadcrumb را در `nav` قرار می‌دهد، نام landmark را `Breadcrumb` می‌گذارد و پیوند/متن هر crumb را با escaping مناسب چاپ می‌کند. پوسته از فیلتر رسمی `woocommerce_breadcrumb_defaults` فقط همین نام را به «مسیر راهنما» تغییر می‌دهد؛ breadcrumb دوم نمی‌سازد و wrapper اصلی WooCommerce را جداگانه مدیریت می‌کند. الگوی W3C نیز ناوبری نام‌گذاری‌شده و مسیر سلسله‌مراتبی را توصیه می‌کند. [1] [2] [24] [25]

**پیش از انتشار:** در محصول، دسته، فروشگاه و صفحهٔ عادی، دقیقاً یک breadcrumb دیداری را بررسی کنید؛ مسیر دستهٔ محصول باید از دسته‌بندی واقعی بیاید. HTML و JSON-LD را هم برای خروجی تکراری بسنجید و ترتیب RTL، صفحه‌کلید و صفحه‌خوان را آزمایش کنید.

## ۲. `robots.txt`

فایل ثابت یا سیاست حدسی اضافه نشد. WordPress می‌تواند `robots.txt` را به‌صورت پاسخ مجازی تولید کند، مگر آنکه فایل واقعی یا لایهٔ هاست/CDN پاسخ را جایگزین کند. گزینهٔ «از موتورهای جست‌وجو درخواست کن» در تنظیمات Reading تضمین محرمانگی نیست؛ `robots.txt` نیز ابزار حفاظت از محتوای خصوصی یا جایگزین `noindex` نیست. [3] [4]

**پیش از انتشار:** از دامنهٔ واقعی، پاسخ HTTP و متن `/robots.txt` را بخوانید؛ آن را با تنظیم Reading، افزونهٔ SEO و دسترسی sitemap تطبیق دهید. staging باید با کنترل دسترسی محافظت شود و برای خروجی `noindex` نیز خزنده نباید به‌وسیلهٔ robots.txt از دیدن صفحه منع شود.

## ۳. دامنهٔ سفارشی

این مورد کد پوسته نیست. رکوردها، مقصد هاست، گواهی TLS، انتخاب `www` یا بدون `www` و نشانی‌های WordPress به سرویس و حساب واقعی وابسته‌اند. مستند WordPress هنگام جابه‌جایی نشانی بر پشتیبان‌گیری و پرهیز از جایگزینی خام داده‌های سریال‌شده هشدار می‌دهد. [5] DNS نیز باید با مقصد رسمی میزبان تنظیم شود، نه با رکورد حدسی. [6]

**مسدود تا دریافت ورودی مالک:** دامنه، ارائه‌دهندهٔ DNS، میزبان و مقصدی که میزبان اعلام می‌کند، اختیار دسترسی و تصمیم URL مرجع. پس از آن باید انتشار DNS، TLS، redirect، ورود مدیر، پیوندهای یکتا و صفحات فروشگاه آزموده شوند. هیچ تغییری در DNS یا پنل انجام نشده است.

## ۴. Structured data محصولات

WooCommerce خروجی ساخت‌یافتهٔ محصول را از دادهٔ واقعی `WC_Product` می‌سازد. منبع دوم JSON-LD برای Product اضافه نشد، چون افزونهٔ SEO و HTML واقعی نصب مشخص نیست. Google می‌خواهد دادهٔ ساخت‌یافته با اطلاعات قابل مشاهده و جاری صفحه سازگار باشد؛ وجود چند نوع schema مرتبط به‌تنهایی خطا نیست، اما تعریف تکراری یا متناقض Product باید رفع شود. [7] [8]

**پیش از انتشار:** منبع هر JSON-LD را در محصول ساده و متغیر و در صفحهٔ غیرمحصول مشخص کنید؛ قیمت، ارز، موجودی، تصویر و شناسه را با محتوای واقعی تطبیق دهید و Rich Results Test/Schema Validator را اجرا کنید. آزمون معتبر، نمایش نتیجه در Google را تضمین نمی‌کند.

## ۵. Canonical tags

دامنهٔ واقعی یا تگ canonical دستی در قالب hard-code نشده است. WordPress هسته برای صفحه‌های تکی canonical می‌سازد؛ آرشیو، جست‌وجو و برخی صفحات فروشگاهی ممکن است به افزونه یا تصمیم مشخص سایت وابسته باشند. Google canonical را سیگنال می‌داند و توصیه می‌کند روش‌های معرفی URL با هم تعارض نداشته باشند. [9] [10]

**پیش از انتشار:** خروجی نهایی HTML و در صورت وجود هدر `Link` را در صفحهٔ اصلی، محصول، آرشیو، جست‌وجو و صفحه‌بندی بررسی کنید؛ مقصد باید URL عمومی و تأییدشده باشد و staging/production با هم اشتباه نشوند. تا تعیین origin، canonical مطلق حدس زده نمی‌شود.

## ۶. Meta description

هیچ توضیح متای یکسان و ساختگی به همهٔ صفحات تزریق نشده است. اگر افزونهٔ SEO انتخاب و فعال شود، همان افزونه باید مالک خروجی باشد؛ توضیح هر صفحه باید به متن واقعی آن متکی باشد. Google ممکن است snippet را از محتوای صفحه بسازد و نمایش مقدار متا را تضمین نمی‌کند. [11]

**پیش از انتشار:** تصمیم بگیرید چه سامانه‌ای متادیتا را مدیریت می‌کند؛ سپس تعداد تگ‌ها و متن صفحه‌محور را در source HTML صفحات نمونه بررسی کنید. برای صفحهٔ بدون توضیح معتبر، تگ خالی یا ادعای تجاری ساختگی تولید نشود.

## ۷. صفحهٔ 404 سفارشی

فایل `404.php` وجود دارد، پیام روشن «این صفحه پیدا نشد» نشان می‌دهد و مسیر بازگشت به فروشگاه دارد. آزمون کد این view را می‌سنجد، نه status واقعی HTTP. WordPress status را از query تعیین می‌کند و Google برای URL ناموجود پاسخ 404 واقعی را به صفحهٔ خطا با status 200 ترجیح می‌دهد. [12]

**پیش از انتشار:** یک مسیر تصادفیِ تأییدشده و ناموجود را با GET درخواست کنید؛ status، redirectها و متن پاسخ را بررسی کنید تا soft 404 یا هدایت بی‌مورد به خانه نباشد.

## ۸. Local business schema

این schema اضافه نشد. برای LocalBusiness rich result گوگل `name` و `address` لازم‌اند؛ اطلاعات واقعی نام، نشانی، نوع کسب‌وکار فیزیکی/شعبه و پذیرش مشتری در محل تأیید نشده است. شماره، ساعت کار، مختصات و محدودهٔ خدمت نیز نباید حدس زده شوند. [13]

**مسدود تا تأیید مالک:** هویت قانونی/قابل نمایش کسب‌وکار، نشانی واقعی و ماهیت محل. اگر کسب‌وکار واقعاً محل یا شعبهٔ فیزیکی ندارد، نوع schema باید بر اساس واقعیت و دستورالعمل رسمی انتخاب شود؛ دادهٔ نمونهٔ مستندات نباید جای دادهٔ فروشگاه بنشیند.

## ۹. Sitemap

فایل sitemap ثانویه به پوسته افزوده نشد. WordPress core مسیر pretty-permalink خود را در `/wp-sitemap.xml` ارائه می‌کند؛ افزونه‌ها ممکن است مسیر دیگری مانند `/sitemap_index.xml` داشته باشند. بنابراین نام درخواستی `/sitemap.xml` را نمی‌توان بدون بررسی نصب واقعی مفروض گرفت. [14]

**پیش از انتشار:** endpointهای موجود، محتوای child sitemapها، پاسخ HTTP، ارجاع sitemap در robots و تکراری‌بودن URLها را بررسی کنید؛ یک منبع sitemap را انتخاب کنید و فقط همان را در Search Console معرفی کنید.

## ۱۰. پیوندهای داخلی

پوسته locationهای منو را ثبت می‌کند، از API منوی WordPress برای منوی تخصیص‌یافته استفاده می‌کند و fallback آن از صفحهٔ خانه و پیوند فروشگاه WooCommerce استفاده می‌کند. صفحهٔ خانه دسته‌ها و محصولات را از دادهٔ WooCommerce می‌خواند؛ هیچ نام دسته، کالا یا مقصد URL ساختگی اضافه نشد. Google پیوند HTML دارای `href` و متن مرتبط با مقصد را قابل‌خزش و قابل فهم‌تر می‌داند. [15] [16]

**پیش از انتشار:** منو را در پنل تخصیص دهید؛ مقصد همهٔ دسته‌ها، محصولات، breadcrumbها و پیوندهای footer را با صفحات منتشرشده بررسی کنید و لینک شکسته، anchor خالی یا تکرار مصنوعی کلیدواژه را رفع کنید.

## ۱۱. Favicon / Site Icon

فایل لوگو یا favicon نهایی در اختیار نیست؛ به همین دلیل تصویر برند ساخته یا به‌عنوان آیکون سایت معرفی نشد. WordPress قابلیت جداگانهٔ Site Icon دارد و در صورت تنظیم، تگ‌های آیکون را از مسیر هسته در `wp_head()` تولید می‌کند. [17]

**پیش از انتشار:** پس از تأیید هویت بصری، مدیر سایت آیکون مربعی را از تنظیم Site Icon بارگذاری کند و URL و پاسخ فایل آیکون را در HTML واقعی و مرورگرهای هدف بررسی کند. تگ دستی اضافی لازم نیست مگر آزمون نصب خلاف آن را نشان دهد.

## ۱۲. عنوان‌های یکتای صفحه

پوسته `add_theme_support( 'title-tag' )` را فعال می‌کند و `header.php`، `wp_head()` را اجرا می‌کند؛ تگ `<title>` ثابت موازی ندارد. هسته بر مبنای نوع صفحه title document می‌سازد، اما یکتایی محتوایی به عنوان پست/محصول، صفحهٔ نوشته‌ها، نام سایت، تنظیم Shop و افزونهٔ SEO نیز وابسته است. [18] [19]

**پیش از انتشار:** title خروجی را در خانه، برگه، نوشته، فروشگاه، محصول، دسته، جست‌وجو، صفحه‌بندی و 404 بررسی کنید؛ در مجموعهٔ URLهای مهم عنوان خالی، تکراری یا اشتباه را اصلاح کنید.

## ۱۳. Headingهای یکتا و سلسله‌مراتب

قالب‌های سفارشی پوسته در source یک H1 صفحه‌ای دارند. نقص مشخص در `index.php` اصلاح شد: به‌جای H1 برای هر نوشتهٔ داخل حلقه، عنوان صفحهٔ نوشته‌ها H1 و عنوان‌های کارت‌ها H2 هستند. W3C بر توصیفی‌بودن و ساختار منطقی heading تأکید می‌کند؛ تعداد ثابت H1 به‌عنوان تضمین رتبه‌بندی از Google نتیجه نمی‌شود. [20] [21]

**پیش از انتشار:** DOM رندرشدهٔ صفحات فروشگاه و محصول را هم بررسی کنید، چون headingهای WooCommerce از نسخه و hookهای runtime می‌آیند. عنوان H1 باید محتوا را توصیف کند؛ آزمون source به‌تنهایی متن تولیدشدهٔ مدیر یا افزونه را اعتبارسنجی نمی‌کند.

## ۱۴. `llms.txt`

`llms.txt` به‌عنوان convention اختیاری پیشنهاد شده است، نه کنترل استاندارد crawl/index و نه عامل تضمین‌شدهٔ ranking. راهنمای جاری Google برای AI Overviews/AI Mode فایل اختصاصی جدیدی را لازم نمی‌داند؛ کنترل‌های crawl باید با مستندات هر crawler و robots/noindex معتبر همان سامانه تنظیم شوند. [22] [23]

**تصمیم این PR:** فایل ساخته نشد؛ دامنه، URLهای واقعی و خلاصهٔ factual کسب‌وکار/کاتالوگ معلوم نیست. اگر مالک در آینده آن را بخواهد، متن کوتاه باید فقط از محتوا و پیوندهای تأییدشده ساخته و در root دامنهٔ نهایی منتشر شود.

## گیت واقعی go-live

تغییرهای کد و آزمون‌های محلی این گزارش جایگزین staging نیستند. اتصال دامنه و TLS، تنظیمات Reading، تشخیص افزونه‌های SEO و schema، ثبت Site Icon، کاتالوگ و اطلاعات فروشنده، sitemap و robots واقعی، پاسخ HTTP مسیر 404، پرداخت و پیامک هنوز در محیط واقعی آزموده یا فعال نشده‌اند. ادغام PR به‌تنهایی به معنی deploy یا آمادگی دریافت سفارش نیست.

## منابع

[1]: https://www.w3.org/WAI/aria/apg/patterns/breadcrumb/ "W3C WAI-ARIA APG: Breadcrumb Pattern"
[2]: https://woocommerce.com/document/customise-the-woocommerce-breadcrumb/ "WooCommerce: Customize the breadcrumb"
[3]: https://developer.wordpress.org/reference/functions/do_robots/ "WordPress: do_robots()"
[4]: https://developers.google.com/search/docs/crawling-indexing/robots/intro "Google Search Central: Robots.txt"
[5]: https://developer.wordpress.org/advanced-administration/upgrade/migrating/ "WordPress: Migrating WordPress"
[6]: https://www.icann.org/resources/pages/dns-2022-09-13-en "ICANN: The Domain Name System"
[7]: https://woocommerce.github.io/code-reference/classes/WC-Structured-Data.html "WooCommerce: WC_Structured_Data"
[8]: https://developers.google.com/search/docs/appearance/structured-data/sd-policies "Google Search Central: Structured data policies"
[9]: https://developer.wordpress.org/reference/functions/rel_canonical/ "WordPress: rel_canonical()"
[10]: https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls "Google Search Central: Canonical URLs"
[11]: https://developers.google.com/search/docs/appearance/snippet "Google Search Central: Snippets"
[12]: https://developers.google.com/search/docs/crawling-indexing/http-network-errors "Google Search Central: HTTP status codes"
[13]: https://developers.google.com/search/docs/appearance/structured-data/local-business "Google Search Central: Local business structured data"
[14]: https://make.wordpress.org/core/2020/07/22/new-xml-sitemaps-functionality-in-wordpress-5-5/ "WordPress Core: XML sitemaps"
[15]: https://developers.google.com/search/docs/crawling-indexing/links-crawlable "Google Search Central: Link best practices"
[16]: https://developer.wordpress.org/reference/functions/wp_nav_menu/ "WordPress: wp_nav_menu()"
[17]: https://developer.wordpress.org/reference/functions/wp_site_icon/ "WordPress: wp_site_icon()"
[18]: https://developer.wordpress.org/reference/functions/add_theme_support/ "WordPress: add_theme_support()"
[19]: https://developer.wordpress.org/reference/functions/wp_get_document_title/ "WordPress: wp_get_document_title()"
[20]: https://www.w3.org/WAI/tutorials/page-structure/headings/ "W3C WAI: Headings"
[21]: https://developers.google.com/search/docs/fundamentals/seo-starter-guide "Google Search Central: SEO Starter Guide"
[22]: https://llmstxt.org/ "llms.txt proposal"
[23]: https://developers.google.com/search/docs/appearance/ai-features "Google Search Central: AI features and your website"
[24]: https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.1/plugins/woocommerce/includes/wc-template-functions.php "WooCommerce 10.8.1 breadcrumb defaults and filter"
[25]: https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.1/plugins/woocommerce/templates/global/breadcrumb.php "WooCommerce 10.8.1 breadcrumb output template"
