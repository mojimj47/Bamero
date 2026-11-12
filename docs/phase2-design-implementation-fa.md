# گزارش مرحلهٔ دوم: نمونه‌سازی طراحی فروشگاه رنگ و چسب بامرو

**وضعیت در زمان تدوین اولیهٔ این گزارش (۱۲ نوامبر ۲۰۲۶):** نمونهٔ غیرزنده در شاخهٔ محلی `feature/bamero-design-phase2` آمادهٔ بازبینی بود و هنوز به GitHub push نشده بود؛ این عبارت سابقهٔ زمانی است، نه وضعیت جاری شاخه. مالک بعداً صریحاً ادغام به `main` را مشروط به مستندات معتبر، بازبینی و موفقیت آزمون‌ها مجاز کرد. این مجوز طراحی/پوسته، درگاه یا پیامک live را شامل نمی‌شود.

## آنچه در این مرحله انجام شد

صفحهٔ خانه اکنون با نام «رنگ و چسب بامرو» و مسیر «کارگاه دقیق و قابل‌اعتماد» نمونه‌سازی شده است. مسیرهای فروشگاه و محصولات پویا و وابسته به کاتالوگ WooCommerce باقی مانده‌اند؛ کالا، قیمت، دسته یا ویژگی فنی ساختگی اضافه نشده است. تصویر قهرمان با شکل‌های CSS ساخته شده و کالای واقعی یا نمونهٔ رنگ فروشی را نمایش نمی‌دهد. دسته‌بندی‌ها همچنان از taxonomy فروشگاه خوانده می‌شوند.[4]

فونت Vazirmatn نسخهٔ متغیر WOFF2 به‌صورت محلی در پوسته قرار گرفته و مجوز SIL OFL 1.1 آن نیز کنار فایل حفظ شده است. نسخهٔ upstream که در صفحهٔ رسمی Releases فعلاً آخرین نسخه نشان داده می‌شود `v33.003` با تاریخ انتشار ۲۲ ژوئن ۲۰۲۲ است؛ بنابراین آن را یک فونت فارسی پایدار و دارای مجوز روشن معرفی می‌کنم، نه یک ترند تازهٔ ۲۰۲۶. فایل CSS رسمی دامنهٔ وزن ۱۰۰ تا ۹۰۰ را اعلام می‌کند.[5] [6] [7]

فونت برای فرانت‌اند و ویرایشگر بلوکی ثبت شده است. تنظیم سراسری پوسته در `theme.json` نگهداری می‌شود و از CDN فونت شخص ثالث استفاده نمی‌شود.[3] قالب برای `lang` و `dir` از ویژگی‌های WordPress استفاده می‌کند؛ در نصب واقعی باید زبان سایت روی فارسی تنظیم و متن‌های ترکیبی فارسی/لاتین، مانند SKU و کد محصول، جداگانه آزموده شوند.[2]

بازبینی مستقل اندازهٔ دکمهٔ منو و جست‌وجوی موبایل را کوچک تشخیص داد. اندازهٔ هدف این دو کنترل و Mini Cart اکنون دست‌کم ۴۴px CSS است. WCAG 2.2 سطح AA در معیار حداقل اندازهٔ هدف، ۲۴×۲۴px را با استثناهای تعریف‌شده مقرر می‌کند؛ ۴۴px در این طرح انتخابی راحت‌تر برای لمس است، نه ادعای گواهی کامل WCAG.[1]

## کنترل‌های انجام‌شده و محدودیت آزمون

ده فایل PHP پوسته با parser ایستای PHP و دستور زبان PHP 8.1 parse شدند. `theme.json`، YAMLهای Compose و GitHub Actions، JavaScript و آزمون رگرسیون Escape/skip-link و قرارداد wrapper WooCommerce اعتبارسنجی شدند. امضای WOFF2، متن مجوز OFL، مسیر asset و `git diff --check` تأیید شد. SHA-256 فونت محلی (`4e3fa217d38fdafc1fea4414ceb58ca5e662cf0ab5fa735a8c8c20e8b42cad92`) با فایل تگ رسمی `v33.003` بایت‌به‌بایت تطبیق داده شد. نسبت کنتراست جفت‌رنگ‌های اصلیِ اندازه‌گیری‌شده از ۴٫۵:۱ بیشتر بود؛ این اندازه‌گیری به‌تنهایی ممیزی دسترس‌پذیری همهٔ صفحه نیست.

یک preview موقتِ CSS-only در اندازه‌های تصویری ۳۹۰×۸۴۴ و ۱۴۴۰×۱۱۰۰ بررسی شد. این preview WordPress یا WooCommerce واقعی اجرا نمی‌کند و محتوای نمونهٔ آن کاتالوگ بامرو نیست. در یک اجرای headless با viewport گزارش‌شدهٔ ۵۰۰px، اندازه‌های محاسبه‌شدهٔ منو ۴۴×۴۶، دکمهٔ جست‌وجو ۶۶×۴۴ و Mini Cart برابر ۹۲×۵۰px بودند؛ Chromium در همان اجرای DOM اندازهٔ درخواستی ۳۹۰px را به‌صورت پایدار گزارش نکرد. در تصویر ۳۹۰px اسکرول افقی آشکاری دیده نشد.

در محیط حاضر PHP CLI، Docker و نصب قابل اجرای WordPress/MySQL در دسترس نبودند. بنابراین `php -l` واقعی، اجرای سفارش، checkout، سازگاری افزونه‌ها یا بازیابی backup انجام نشده است. مستندات رسمی WooCommerce روش پایدارِ جایگزینی wrapper با hook را توصیه می‌کنند؛ قالب‌های نسخهٔ 10.8.1 نیز hookهای شروع/پایان main را برای shop archive و single product صدا می‌زنند. Bamero اکنون wrapper پیش‌فرض را با همان کلاس‌های layout و `main-content` فوکوس‌پذیر جایگزین می‌کند، بدون کپی‌کردن قالب‌های افزونه.[15] [16] [17] [18] [19] ماتریس جاری WooCommerce 10.8+ حداقل WordPress 6.9 و PHP 8.3 را می‌خواهد؛ Compose محلی با WordPress 7.1.4/PHP 8.3 و MariaDB 11.4.13 پین شده است، نه به‌عنوان اثبات production.[8] [12] [13] [14] آزمون Node و Chromium headless با CSS/JavaScript پوسته در 390px و 1280px گذشت: انتقال فوکوس skip link، بستن منو با Escape و حفظ/بازگشت فوکوس. این harness نصب WordPress یا screen reader نیست. CI همان PR باید PHP 8.3، hook contract و بررسی‌های نحوی/دارایی را پاس کند؛ نتیجهٔ آن اثبات runtime یا checkout نیست. آزمون runtime، پرداخت sandbox/live و بازیابی backup همچنان روی staging واقعی لازم‌اند.[8] [11] [12] [13] [14]

ممیزی مستقل این گام یک مسیر خطای `get_term_link()` یافت که اکنون `WP_Error` را پیش از `esc_url()` رد می‌کند؛ خروجی عنوان آرشیو نیز escape می‌شود. دو نقص دسترس‌پذیری رفع شدند: هر هفت main focusable هستند و Escape فقط در منوی موبایلِ باز/نمایان و هنگام فوکوس داخل منو یا دکمه عمل می‌کند. نقص اصلی WooCommerce نیز با hookهای رسمی wrapper رفع شده تا shop archive و product page هدف `#main-content` داشته باشند؛ آزمون Node قرارداد hook را بررسی می‌کند. این بررسی ثابت نمی‌کند hook در نصب واقعی WooCommerce اجرا شده است.

## پیشنهادهای بعدی با اولویت

1. **کاتالوگ واقعی قبل از هر ادعای فروش:** فایل SKU شامل برند، نام استاندارد، گونه/حجم، واحد قیمت، موجودی، وزن و ابعاد هر بسته تهیه شود. برای هر ادعای کاربرد، سازگاری، پوشش‌دهی، زمان خشک‌شدن یا هشدار، سند همان SKU از سازنده ثبت شود. دادهٔ فاقد سند منتشر نشود.

2. **جست‌وجوی فارسی بر اساس دادهٔ فروشگاه:** تطبیق «ی/ي» و «ک/ك»، رقم فارسی/لاتین، نیم‌فاصله، SKU و نام برند را در staging آزمون کنید. عبارت‌های بی‌نتیجه را به شکل تجمیعی و بدون ذخیرهٔ غیرضروری اطلاعات شخصی بررسی کنید؛ پیشنهاد تایپی را فقط اگر خطاهای واقعی جست‌وجو نشان دادند توسعه دهید.[11]

3. **نمایش رنگ و کالا بدون القای دقت نادرست:** تصویر واقعی بسته‌بندی و کد سازنده را با اجازهٔ استفاده بگیرید. اگر نمونهٔ رنگ دیجیتال ارائه شد، تفاوت نمایشگر و نور با رنگ واقعی روشن باشد؛ رنگ نمایشی را بدون تطبیق معتبر به‌عنوان نتیجهٔ دقیق فروش جا نزنید.

4. **هزینهٔ تحویل پیش از پرداخت:** پس از دریافت وزن/ابعاد و مناطق ارسال، روش محاسبه و هزینهٔ حمل کالاهای حجیم را در سبد و پیش از تعهد پرداخت نمایش دهید. اطلاعات فروشنده، پرداخت و تحویل، مرجوعی و شرایط خدمت باید با دادهٔ واقعی کسب‌وکار و بررسی حقوقی مالک نهایی شود؛ این گزارش نظر حقوقی صادر نمی‌کند.[10]

5. **پرداخت و پیامک فقط روی staging:** زیبال، نام دقیق پنل پیامک و نیاز به اعلان سفارش یا OTP باید جداگانه از مالک تأیید شود. نسخهٔ افزونه و قرارداد رسمی API، واحد ریال/تومان، لغو، callback، تکرار درخواست و نگهداری داده پیش از کدنویسی یا live راستی‌آزمایی شود. مرزهای نامعلوم در گزارش مرحلهٔ اول با `[CRITICAL DEPENDENCY / UNVERIFIED SPEC]` مشخص شده‌اند؛ هیچ endpoint حدس زده یا سرویس فعالی متصل نشده است.

6. **عملکرد و کشف‌پذیری با دادهٔ واقعی:** تصاویر اصلی کالا با ابعاد و فرمت مناسب تحویل شوند و شاخص‌های LCP، INP و CLS روی staging و پس از راه‌اندازی با اندازه‌گیری واقعی ثبت شوند. نتیجهٔ Core Web Vitals یا رتبهٔ جست‌وجو از روی این نمونه قابل ادعا نیست.[9]

## گام بعدی

برای ادغام فعلی، اجازهٔ صریح مالک دریافت شده است؛ merge به `main` فقط پس از سبزشدن CI همان Pull Request و پایان بازبینی‌های فنی انجام می‌شود. این مجوز انتشار production نیست. مرحلهٔ فنی/staging به کاتالوگ و مدارک سازنده، لوگو و رنگ برند مصوب، مناطق و قواعد تحویل، نشانی staging و پیکربندی واقعی میزبان نیاز دارد. هویت دقیق پنل پیامک و وضعیت sandbox زیبال نیز باید تأیید شود؛ credential را در Git یا پیام عادی نفرستید. آزمون پرداخت live و انتشار production gate جداگانه می‌مانند.

## References

[1]: https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html "W3C WAI: Target Size Minimum"
[2]: https://www.w3.org/International/questions/qa-html-dir "W3C Internationalization: Structural markup and right-to-left text"
[3]: https://developer.wordpress.org/themes/global-settings-and-styles/introduction-to-theme-json/ "WordPress Theme Handbook: theme.json"
[4]: https://woocommerce.com/document/managing-product-taxonomies/ "WooCommerce: Managing product categories, tags and attributes"
[5]: https://github.com/rastikerdar/vazirmatn/releases/tag/v33.003 "Vazirmatn official release v33.003"
[6]: https://raw.githubusercontent.com/rastikerdar/vazirmatn/v33.003/Vazirmatn-Variable-font-face.css "Vazirmatn v33.003 variable font CSS"
[7]: https://raw.githubusercontent.com/rastikerdar/vazirmatn/v33.003/OFL.txt "Vazirmatn SIL Open Font License 1.1"
[8]: https://woocommerce.com/document/server-requirements/ "WooCommerce server requirements"
[9]: https://developers.google.com/search/docs/appearance/core-web-vitals "Google Search Central: Core Web Vitals"
[10]: https://qavanin.ir/Law/TreeText/?IDS=15700719202226051269 "قانون تجارت الکترونیکی"
[11]: https://www.nngroup.com/reports/ecommerce-ux-search-including-faceted-search/ "Nielsen Norman Group: Ecommerce Search UX"
[12]: https://wordpress.org/download/releases/ "WordPress.org release archive"
[13]: https://woocommerce.com/document/server-requirements/ "WooCommerce server requirements"
[14]: https://developer.woocommerce.com/2026/05/26/woocommerce-10-8-0-release/ "WooCommerce 10.8 release notes"
[15]: https://developer.woocommerce.com/docs/theming/theme-development/classic-theme-developer-handbook/ "WooCommerce classic theme integration hooks"
[16]: https://github.com/woocommerce/woocommerce/blob/10.8.1/plugins/woocommerce/templates/archive-product.php "WooCommerce 10.8.1 product archive template"
[17]: https://github.com/woocommerce/woocommerce/blob/10.8.1/plugins/woocommerce/templates/single-product.php "WooCommerce 10.8.1 single-product template"
[18]: https://github.com/woocommerce/woocommerce/blob/10.8.1/plugins/woocommerce/templates/global/wrapper-start.php "WooCommerce 10.8.1 wrapper start"
[19]: https://github.com/woocommerce/woocommerce/blob/10.8.1/plugins/woocommerce/templates/global/wrapper-end.php "WooCommerce 10.8.1 wrapper end"
