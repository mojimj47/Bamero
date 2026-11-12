# ممیزی آمادگی انتشار Bamero

**حکم مستقیم: آمادهٔ انتشار تولیدی نیست؛ ادغام به‌تنهایی فروشگاه زنده یا آمادهٔ دریافت وجه ایجاد نمی‌کند.** مخزن، پوستهٔ WordPress/WooCommerce و آزمون‌های ایستای آن را شامل می‌شود، نه نصب و پیکربندی عملیاتی وردپرس. وضعیت Git ثبت‌شده: شاخهٔ `feature/bamero-design-phase2` دو commit جلوتر از `origin/main` است. پس از اصلاح قرارداد CSS گرید، آزمون Node محلی و lint نحوی PHP 8.3 دوباره موفق شدند؛ اجرای WordPress/WooCommerce، درگاه، پیامک و checkout انجام نشده و هیچ بررسی زنده‌ای ادعا نمی‌شود. GitHub CLI توکن نامعتبر گزارش کرده و GitHub API پاسخ 403 با پیام suspension داده؛ بنابراین push/merge راه‌دور و وضعیت CI بررسی نشد. [README](../README.md#L3-L4) [بازبینی فنی](technical-review-fa.md#L15-L21)

## وضعیت ادغام و شواهد مخزن

- وضعیت گزارش‌شدهٔ شاخه: feature دو commit جلوتر از `origin/main`. این را نباید با آزمون CI موفق روی SHA جاری یا ادغام‌شدن به `main` یکی دانست؛ فقط checkهای موفق همان SHA جاری و بازبینی لازم، گیت ادغام‌اند. [بازبینی فنی، گیت ادغام](technical-review-fa.md#L20-L21)
- تست Node محلی برای قراردادهای دسترس‌پذیری/markup و قاعدهٔ ستون‌های WooCommerce گذشت. lint نحوی همهٔ فایل‌های PHP قالب با CLI PHP 8.3 نیز بدون خطا گذشت. CI تعریف شده است، اما این آزمون‌های منبعی اثبات‌کنندهٔ اجرای WordPress/WooCommerce یا رفتار runtime نیستند. وضعیت CI راه‌دور برای SHA جاری بررسی‌پذیر نبود، چون GitHub API 403 داد. [README](../README.md#L24-L29) [بازبینی فنی](technical-review-fa.md#L15-L21)
- مخزن قالب، Compose توسعهٔ محلی، CI و مستندات را دارد؛ نصب تولید، دامنه/میزبان، دادهٔ واقعی کالا و فروشنده، پیکربندی افزونه‌ها و کلیدهای درگاه را ندارد. Compose محلی نیز برای production نیست. [README](../README.md#L3-L4) [README](../README.md#L20-L26)
- ماتریس ثبت‌شده برای WooCommerce 10.8+: WordPress 6.9+، PHP 8.3+، MySQL 8.0+ یا MariaDB 10.6+، HTTPS و حداقل 256MB حافظهٔ WordPress است؛ مستندات فروشگاه، `mbstring` را نیز برای فارسی لازم می‌داند. این حداقل سازگاری است، نه گواه ظرفیت یا پیکربندی میزبان. پیش از استقرار نسخه‌ها و نیازمندی‌های جاری دوباره بررسی شوند. [README](../README.md#L5-L8) [WooCommerce server requirements](https://woocommerce.com/document/server-requirements/)

## افزونه‌ها و یکپارچه‌سازی‌ها

**درگاه Zibal:** هیچ افزونهٔ پرداخت، پیکربندی WordPress/WooCommerce یا کلید/حساب پذیرنده در مخزن نیست؛ همهٔ گیت‌های پرداخت در چک‌لیست بازند. مستندات رسمی Zibal درخواست HTTPS/JSON، مبلغ به ریال و callback را شرح می‌دهد و تأکید می‌کند callback به‌تنهایی اثبات پرداخت نیست: سرور فروشگاه باید `/v1/verify` را فراخوانی و نتیجه را بررسی کند. نتیجهٔ 100 تأیید و 201 پرداخت قبلاً تأییدشده است. [README](../README.md#L3-L4) [چک‌لیست](launch-checklist-fa.md#L23-L30) [مستندات IPG زیبال](https://help.zibal.ir/ipg/)

یک گزینهٔ فهرست‌شده در WordPress.org، **Gateway Zibal for WooCommerce** از ناشر `zibal`، نسخهٔ 2.1، از فهرست رسمی/مخزن افزونه دارای شواهدی از verify سمت سرور، تطبیق سفارش و مبلغ، تکرار callback، قفل تأیید و تبدیل تومان به ریال است. این بررسی فقط کد عمومی/فهرست را پشتیبانی می‌کند: نصب، سازگاری با نسخه‌های واقعی فروشگاه، آزمون end-to-end و امنیت ذخیرهٔ PIN در این پروژه اثبات نشده است؛ پشتیبانی بازپرداخت نیز تأیید نشده. انتخاب افزونهٔ تأییدشده برای همین استقرار انجام نشده است. پیش از تصمیم، نسخه و کد کامل روی staging بازبینی و سناریوهای موفق/ناموفق، لغو، callback تکراری/دستکاری‌شده و تطبیق مبلغ/واحد آزموده شوند. کلید زنده فقط پس از تأیید پذیرنده و آزمون sandbox وارد تنظیمات امن شود؛ بازپرداخت تا زمان اثبات قابلیت، نیازمندی باز جداگانه است. [فهرست رسمی زیبال در WordPress.org](https://wordpress.org/plugins/zibal-payment-gateway-for-woocommerce/) [کد درگاه زیبال](https://plugins.trac.wordpress.org/browser/zibal-payment-gateway-for-woocommerce/trunk/class-wc-gateway-zibal.php?format=txt) [راهنمای افزونه](https://plugins.trac.wordpress.org/browser/zibal-payment-gateway-for-woocommerce/trunk/readme.txt?format=txt)

**OTP پیامکی و MeliPayamak:** OTP پیاده‌سازی یا پیکربندی نشده است. API رسمی MeliPayamak ارسال پیامک و REST/Auth Token را توضیح می‌دهد، اما چرخهٔ کامل OTP وردپرس را مستند/تأیید نمی‌کند. فهرست WSMS می‌گوید OTP/2FA و WooCommerce در افزونهٔ پولی All-in-One است و MeliPayamak یکی از درگاه‌های آن است؛ این ادعای فروشنده، اثبات کارکرد end-to-end در نسخه/مجوز موردنیاز نیست. افزونهٔ FmeAddonsِ WooCommerce ارائه‌دهندگان Firebase، MSG91، Twilio و WhatsApp را فهرست می‌کند، نه MeliPayamak. فهرست افزونهٔ «OTP Login With Phone Number» نام MelliPayamak را در درگاه‌های Pro می‌آورد، اما مسیر OTP جاری برای این فروشگاه مستقلاً تأیید نشده است. بنابراین **هیچ انتخاب افزونه‌ای برای MeliPayamak اعتبارسنجی نشده** و نامزدها توصیهٔ قطعی نیستند. [API رسمی MeliPayamak](https://www.melipayamak.com/api/) [WSMS/افزونه](https://wordpress.org/plugins/wp-sms/) [درگاه‌های WSMS](https://wsms.io/gateways) [قیمت WSMS](https://wsms.io/pricing/) [OTP Login With Phone Number](https://wordpress.org/plugins/login-with-phone-number/) [افزونهٔ WooCommerce از FmeAddons](https://woocommerce.com/products/registration-login-with-mobile-phone-number/)

برای انتخاب، فروشنده باید پشتیبانی MeliPayamak را در tier مجاز، شیوهٔ احراز هویت/API فعلی، قالب و sender، سازگاری نسخه‌ها و مسیر ورود/ثبت‌نام WooCommerce را تأیید کند؛ سپس ارسال و اعتبارسنجی OTP برای کاربر جدید و موجود روی staging آزموده شود. کنترل‌های لازم شامل انقضای کوتاه سمت سرور، یک‌بارمصرف و purpose-bound بودن کد، محدودیت تلاش و ارسال به‌ازای شماره/IP، سقف هزینه و هشدار، پاسخ عمومی ضد enumeration، عدم ثبت کد/secret در log و آزمون CAPTCHA/ریسک است. SMS در برابر SIM swap، بازیافت شماره و رهگیری اپراتور مقاوم به phishing نیست؛ مدیر باید روش بازیابی غیرپیامکی و MFA قوی‌تر مانند passkey/authenticator داشته باشد.

**SEO:** برای staging یک گزینهٔ اولیه، **Rank Math SEO** است؛ صفحهٔ رسمی آن پشتیبانی از WooCommerce، metadata و schema محصول و XML sitemap را فهرست می‌کند. این فقط نامزد ارزیابی است، نه انتخاب یا نصب انجام‌شده. پیش از فعال‌سازی، با WooCommerce core/theme هرگونه schema، canonical، sitemap و breadcrumb تکراری را حذف کنید و خروجی خام HTML را روی صفحهٔ محصول، آرشیو و 404 بسنجید. جایگزین معتبر **Yoast SEO** است؛ اگر نیاز به WooCommerce-specific schema/attributes و کنترل sitemap فروشگاهی دارید، راهنمای Yoast آن‌ها را در افزونهٔ پولی WooCommerce SEO مستند می‌کند. هم‌زمان Rank Math و Yoast را برای SEO فعال نکنید؛ یک مالک برای metadata/schema/sitemap انتخاب شود. هیچ‌کدام تضمین رتبه نیستند. [Rank Math در WordPress.org](https://wordpress.org/plugins/seo-by-rank-math/) [راهنمای WooCommerce SEO از Yoast](https://yoast.com/help/configuration-guide-for-yoast-woocommerce-seo/) [Yoast در WordPress.org](https://wordpress.org/plugins/wordpress-seo/)

**Cache:** افزونهٔ cache را تا تعیین وب‌سرور انتخاب/فعال نکنید. اگر میزبان واقعاً LiteSpeed/OpenLiteSpeed/QUIC.cloud دارد، **LiteSpeed Cache** گزینهٔ مشروط است؛ cache صفحه‌ای آن به موتور cache LiteSpeed نیاز دارد و مستنداتش سبد، checkout و My Account را پیش‌فرض خارج می‌کنند. اگر وب‌سرور LiteSpeed نیست، این افزونه cache صفحه را فراهم نمی‌کند؛ در آن حالت **WP Super Cache** نامزد سادهٔ staging است، چون مستند WooCommerce می‌گوید با WooCommerce سازگار است و به‌طور پیش‌فرض همان صفحات حساس را خارج می‌کند. در هر دو حالت پس از فعال‌سازی با دو نشست/کاربر جدا، cart isolation، coupon، login/logout و checkout را تست کنید؛ session/cookieهای WooCommerce را از cache اشتراکی مستثنا کنید. از هم‌زمانی چند page-cache/minify plugin پرهیز کنید. افزونه‌ای در این مخزن نصب یا تنظیم نشده است. [مستند cache WooCommerce](https://developer.woocommerce.com/docs/best-practices/performance/configuring-caching-plugins/) [پرسش‌های LiteSpeed Cache](https://docs.litespeedtech.com/lscache/lscwp/faq/)

## OTP، پرداخت و مرز اعتماد

حساب تجاری، تأیید دامنهٔ callback، PIN/کلید live، sender/template پیامک، نصب runtime و secret store در اختیار این بررسی نیستند؛ هیچ پرداخت یا OTP زنده تأیید نشده. در callback، صرفاً پارامتر موفقیت کافی نیست: باید verify سمت سرور، تطبیق order/track ID، مبلغ و واحد پول، و رفتار تکرار/همزمانی با مستندات ارائه‌دهنده آزموده شود. مبلغ Zibal ریال است؛ تبدیل با تنظیم واحد فروشگاه و موارد تخفیف، حمل، مالیات و مبلغ صفر/نامعتبر باید کنترل شود. [مستندات Zibal](https://help.zibal.ir/ipg/) [چک‌لیست پرداخت](launch-checklist-fa.md#L23-L30)

## ظرفیت و کارایی

**عدد ۱٬۰۰۰ MAU تضمین ظرفیت نیست.** کاربران فعال ماهانه، concurrency، نرخ درخواست اوج یا تمرکز checkout را مشخص نمی‌کند؛ با دادهٔ موجود هیچ ادعای ظرفیت برای این فروشگاه پشتیبانی‌پذیر نیست. پیش از انتخاب منابع باید ترافیک واقعی/پیش‌بینی‌شده، ترکیب بازدید ناشناس و ورودشده، مدت نشست، نرخ اوج، کمپین‌ها، حجم کاتالوگ و سفارش اندازه‌گیری شود. [README](../README.md#L24-L26) [WooCommerce server requirements](https://woocommerce.com/document/server-requirements/)

پس از تأمین staging مشابه تولید، منابع PHP-FPM/CPU/RAM و پایگاه داده براساس آزمون انتخاب شوند؛ OPcache فعال و cache پایدار شیء فقط با توجیه اندازه‌گیری‌شده بررسی شود. صفحات عمومی کاتالوگ را می‌توان cache کرد، اما سبد، checkout، My Account و پاسخ‌های وابسته به نشست/کوکی/مشتری باید در همهٔ لایه‌ها از cache اشتراکی مستثنا باشند. تصاویر محصول در اندازهٔ مناسب و دارایی ایستا با CDN قابل بررسی‌اند. آزمون بار باید ترکیب بازدید، جست‌وجو، ورود، سبد و checkout را با ramp و burst تا اوج پیش‌بینی‌شده پوشش دهد و p50/p95، خطا، مصرف منابع، worker/اتصال DB، slow query، cache-hit و تکمیل checkout ثبت کند؛ تا پیش از این داده‌ها ظرفیت تضمین نمی‌شود. [WordPress cache](https://developer.wordpress.org/advanced-administration/performance/cache/) [بهینه‌سازی WordPress](https://developer.wordpress.org/advanced-administration/performance/optimization/) [راهنمای کارایی WooCommerce](https://developer.woocommerce.com/docs/best-practices/performance/performance-best-practices/) [عیب‌یابی سایت کند WooCommerce](https://woocommerce.com/document/troubleshooting-a-slow-site/) [صفحات WooCommerce و cache](https://woocommerce.com/document/pages-not-displaying/)

## امنیت

شواهد مخزن، کنترل‌های تولیدی مانند TLS، MFA مدیر، به‌روزرسانی، پشتیبان/بازیابی، WAF، محدودسازی نرخ، ثبت رویداد، مدیریت اسرار یا طرح پاسخ‌گویی رخداد را اثبات نمی‌کنند؛ موارد مربوط در چک‌لیست بازند. آزمون Node موفق، ممیزی امنیتی یا پذیرش پرداخت زنده نیست. [چک‌لیست میزبانی](launch-checklist-fa.md#L5-L13) [راهنمای سخت‌سازی WordPress](https://wordpress.org/documentation/article/hardening-wordpress/) [امنیت WooCommerce](https://woocommerce.com/document/woocommerce-security/)

پیش از عرضه، TLS معتبر و redirect HTTPS، حساب‌های کم‌دسترسی و MFA، فرایند به‌روزرسانی، پشتیبان آفلاین/خارج از میزبان و restore آزموده‌شده فراهم شود. secret در Git/قالب قرار نگیرد؛ دسترسی به تنظیمات، DB و backup محدود و logها از اطلاعات حساس پاک باشند. WAF/rate-limit برای login، checkout و callback همراه هشدار تنظیم شود. دامنهٔ PCI DSS باید با acquiring bank/ارائه‌دهنده تعیین شود؛ استفاده از درگاه hosted ممکن است مواجهه را کاهش دهد، اما به‌خودی‌خود اثبات انطباق نیست. [SSL و HTTPS در WooCommerce](https://woocommerce.com/document/ssl-and-https/) [PCI در WooCommerce](https://woocommerce.com/document/pci-dss-compliance-and-woocommerce/) [PCI SSC: بازرگانان](https://www.pcisecuritystandards.org/merchants/) [استانداردهای PCI SSC](https://www.pcisecuritystandards.org/standards/)

## فروشگاه، SEO و RTL

در سطح منبع قالب، title-tag، پشتیبانی WooCommerce، `wp_head()`، 404 سفارشی، RTL و breakpointهای responsive وجود دارد؛ CSS اکنون کلاس‌های `columns-1` تا `columns-4` را در گرید عریض رعایت می‌کند و در اندازه‌های زیر ۹۰۰ پیکسل گرید چندستونه را به دو ستون کاهش می‌دهد. این اصلاح با مستند قالب کلاسیک WooCommerce و تست قرارداد منبع هم‌راستاست، اما status واقعی HTTP، rendering مرورگر یا دسترس‌پذیری روی دستگاه را اثبات نمی‌کند. چک‌لیست آزمون واقعی موبایل/تبلت/دسکتاپ و RTL، صفحات محصول، سبد و checkout را باز می‌گذارد. [WooCommerce classic theme handbook](https://developer.woocommerce.com/docs/theming/theme-development/classic-theme-developer-handbook/) [README](../README.md#L24-L29) [چک‌لیست storefront](launch-checklist-fa.md#L32-L40) [بازبینی فنی](technical-review-fa.md#L7-L19)

robots، sitemap، canonical، متا، schema نهایی، Site Icon، دامنهٔ مرجع و محصولات واقعی باید در HTML و پاسخ‌های محیط staging/production سنجیده شوند؛ وجود رفتار هستهٔ WordPress جای بررسی افزونه‌ها و تنظیمات را نمی‌گیرد. URL ناموجود باید واقعاً 404 باشد، نه soft-404. یک مالک SEO/schema تعیین و از canonical، sitemap و structured data تکراری/متناقض جلوگیری شود. دادهٔ LocalBusiness فقط با هویت و نشانی واقعی و واجد شرایط درج شود. [چک‌لیست SEO](launch-checklist-fa.md#L37-L52) [WordPress robots](https://developer.wordpress.org/reference/functions/do_robots/) [XML Sitemap در WordPress](https://make.wordpress.org/core/2020/07/22/new-xml-sitemaps-functionality-in-wordpress-5-5/) [Google: ساخت sitemap](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap) [Google: canonical](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls) [Google: LocalBusiness](https://developers.google.com/search/docs/appearance/structured-data/local-business)

`llms.txt` موردی **اختیاری** است؛ در صورت درخواست مالک می‌توان فایل factual در ریشهٔ دامنه منتشر کرد، اما آن را عامل رتبه‌بندی یا تضمین دیده‌شدن در موتور جست‌وجو/هوش مصنوعی معرفی نکنید. [چک‌لیست](launch-checklist-fa.md#L49-L52) [Google: قابلیت‌های AI در جست‌وجو](https://developers.google.com/search/docs/appearance/ai-features)

## چک‌لیست اولویت‌دار گیت عرضه

1. **گیت ادغام:** push/بازبینی و تأیید همهٔ checkهای SHA جاری؛ وضعیت branch دو commit جلوتر از `origin/main` جایگزین این گیت نیست.
2. **گیت زیرساخت:** provision میزبان و دامنه؛ تطبیق نسخه‌های WordPress/WooCommerce/PHP/DB، `mbstring`، HTTPS، منابع، staging محدود و تنظیم پشتیبان؛ restore را عملاً تمرین کنید.
3. **گیت فروشگاه:** نصب واقعی WordPress/WooCommerce و قالب؛ تنظیم هویت، صفحات، کالا/قیمت/موجودی، ارز و واحد، مالیات، ارسال، سیاست‌ها و ایمیل؛ آزمون مرورگر/دستگاه و RTL.
4. **گیت پذیرش پرداخت:** بررسی افزونهٔ Zibal و سازگاری آن با stack واقعی؛ sandbox و سناریوهای موفق، شکست، لغو، مهمان، کوپن، ارسال، callback تکراری/دستکاری‌شده، تطبیق مبلغ/واحد و سفارش. تأیید حساب/دامنه و فقط سپس انتقال کنترل‌شده به live. بازپرداخت را تا اثبات پشتیبانی، دستی/فرایند مستقل و آزموده‌شده تلقی کنید.
5. **گیت OTP (اگر الزام کسب‌وکار است):** انتخاب فقط پس از اثبات MeliPayamak در tier موردنظر؛ آزمون end-to-end ثبت‌نام/ورود، امنیت سوءاستفاده، هزینه، بازیابی مدیر و مسیر غیر SMS. در غیر این‌صورت OTP را قابلیت ارائه‌نشده ثبت کنید.
6. **گیت امنیت/SEO:** MFA و least privilege، secret امن، WAF/rate-limit، log/alert، تعیین PCI با ارائه‌دهنده؛ بررسی HTML/HTTP و robots، sitemap، canonical، متا/schema، 404 واقعی، Site Icon و محتوای واقعی روی دامنهٔ مرجع.
7. **گیت ظرفیت و پذیرش نهایی:** آزمون بار نمایندهٔ اوج، اصلاح bottleneck و تکرار آزمون؛ تأیید smoke test سفارش/ایمیل/پرداخت، TLS، بازیابی backup، پایش و مسئولیت عملیاتی. لاگ شواهد و sign-off مالک قبل از بازکردن فروش عمومی.

چک‌لیست انتشار پروژه به‌صراحت می‌گوید گیت‌های staging، پرداخت، داده‌های تجاری و آزمون live هنوز نیازمند ورودی و اجرا هستند؛ گزارش حاضر این موارد را انجام‌شده تلقی نمی‌کند. [چک‌لیست انتشار](launch-checklist-fa.md#L55-L64) [README](../README.md#L33-L35)

## منابع

- [README پروژه Bamero](../README.md)
- [چک‌لیست راه‌اندازی فروشگاه Bamero](launch-checklist-fa.md)
- [بازبینی فنی پیش از ادغام Bamero](technical-review-fa.md)
- [مستندات رسمی درگاه پرداخت زیبال](https://help.zibal.ir/ipg/)
- [Gateway Zibal for WooCommerce — WordPress.org](https://wordpress.org/plugins/zibal-payment-gateway-for-woocommerce/)
- [کد عمومی درگاه Zibal — WordPress.org Trac](https://plugins.trac.wordpress.org/browser/zibal-payment-gateway-for-woocommerce/trunk/class-wc-gateway-zibal.php?format=txt)
- [راهنمای افزونهٔ Zibal — WordPress.org Trac](https://plugins.trac.wordpress.org/browser/zibal-payment-gateway-for-woocommerce/trunk/readme.txt?format=txt)
- [API پیامکی MeliPayamak](https://www.melipayamak.com/api/)
- [WSMS — افزونهٔ WP SMS در WordPress.org](https://wordpress.org/plugins/wp-sms/)
- [WSMS — درگاه‌های پشتیبانی‌شده](https://wsms.io/gateways)
- [WSMS — پلن‌ها و قیمت](https://wsms.io/pricing/)
- [OTP Login With Phone Number — WordPress.org](https://wordpress.org/plugins/login-with-phone-number/)
- [Registration & Login With Mobile Phone Number — WooCommerce.com](https://woocommerce.com/products/registration-login-with-mobile-phone-number/)
- [نیازمندی‌های سرور WooCommerce](https://woocommerce.com/document/server-requirements/)
- [راهنمای کندی سایت WooCommerce](https://woocommerce.com/document/troubleshooting-a-slow-site/)
- [صفحه‌های WooCommerce که نباید cache شوند](https://woocommerce.com/document/pages-not-displaying/)
- [راهنمای cache در WordPress](https://developer.wordpress.org/advanced-administration/performance/cache/)
- [راهنمای بهینه‌سازی WordPress](https://developer.wordpress.org/advanced-administration/performance/optimization/)
- [بهترین‌روش‌های کارایی WooCommerce](https://developer.woocommerce.com/docs/best-practices/performance/performance-best-practices/)
- [سخت‌سازی WordPress](https://wordpress.org/documentation/article/hardening-wordpress/)
- [امنیت WooCommerce](https://woocommerce.com/document/woocommerce-security/)
- [SSL و HTTPS در WooCommerce](https://woocommerce.com/document/ssl-and-https/)
- [PCI DSS و WooCommerce](https://woocommerce.com/document/pci-dss-compliance-and-woocommerce/)
- [PCI SSC — منابع بازرگانان](https://www.pcisecuritystandards.org/merchants/)
- [PCI SSC — استانداردها](https://www.pcisecuritystandards.org/standards/)
- [WordPress robots.txt](https://developer.wordpress.org/reference/functions/do_robots/)
- [قابلیت XML Sitemap در WordPress](https://make.wordpress.org/core/2020/07/22/new-xml-sitemaps-functionality-in-wordpress-5-5/)
- [Google Search Central — ساخت sitemap](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)
- [Google Search Central — canonical و URL تکراری](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls)
- [Google Search Central — دادهٔ ساختاریافتهٔ LocalBusiness](https://developers.google.com/search/docs/appearance/structured-data/local-business)
- [Google Search Central — قابلیت‌های AI در جست‌وجو](https://developers.google.com/search/docs/appearance/ai-features)
