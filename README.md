# فروشگاه WordPress و WooCommerce بامرو

این مخزن پوستهٔ سبک و راست‌چین Bamero را به‌همراه محیط توسعهٔ محلی و کنترل‌های CI نگه می‌دارد. کاتالوگ، دسته‌ها، سبد، تسویه‌حساب و سفارش‌ها را WooCommerce مدیریت می‌کند؛ mini-cart از بلوک رسمی WooCommerce استفاده می‌کند. دادهٔ ساختگی فروشنده/کالا یا کلید پرداخت در مخزن نیست.

## ماتریس نسخهٔ بررسی‌شده

بر اساس منابع رسمی بررسی‌شده در **۱۲ نوامبر ۲۰۲۶**، WordPress پایدار 7.1.4 و تگ پایدار اعلام‌شدهٔ WooCommerce 10.8.1 بود. WooCommerce 10.8+ حداقل WordPress 6.9 و PHP 8.3 را می‌خواهد؛ مستندات سرور WooCommerce همچنین MySQL 8.0+ یا MariaDB 10.6+، HTTPS و حداقل 256MB حافظهٔ WordPress را اعلام می‌کند. برای فروشگاه فارسی، افزونهٔ `mbstring` نیز لازم است. Compose محلی با image رسمی WordPress 7.1.4/PHP 8.3 و MariaDB 11.4.13 پین شده است. پیش از هر انتشار یا به‌روزرسانی، ماتریس و مستندات ارائه‌دهنده دوباره بررسی شوند؛ این pin محلی گواهی سازگاری production نیست.

SHA-256 فونت محلی Vazirmatn v33.003: `4e3fa217d38fdafc1fea4414ceb58ca5e662cf0ab5fa735a8c8c20e8b42cad92`. workflow همین مقدار را کنترل می‌کند.

## شروع محلی

نیازمندی: Docker و Docker Compose. فایل نمونه را کپی کنید و فقط برای محیط محلی نگه دارید:

```sh
cp .env.example .env
docker compose up -d
```

سپس در `http://localhost:8080` نصب اولیهٔ WordPress را انجام دهید، زبان سایت را فارسی انتخاب کنید، افزونهٔ رسمی WooCommerce را نصب و فعال کنید و پوستهٔ Bamero را از **نمایش ← پوسته‌ها** فعال کنید. فایل پوسته داخل کانتینر mount شده؛ ویرایش کد در مخزن در پیش‌نمایش محلی دیده می‌شود. پورت محلی فقط روی loopback میزبان bind می‌شود. برای محیط عمومی از مقادیر `.env.example` یا این Compose استفاده نکنید.

## در مخزن چیست؟

- `wp-content/themes/bamero/`: قالب classic وردپرس با پشتیبانی WooCommerce، صفحهٔ خانهٔ پویا، جست‌وجوی کالا، mini-cart رسمی، breadcrumb بومی با نام دسترس‌پذیر فارسی، منوی واکنش‌گرا و RTL.
- `compose.yaml`: WordPress + MariaDB برای توسعهٔ محلی؛ دارای imageهای پین‌شده و bind محلی loopback است؛ TLS/reverse proxy و پیکربندی production ندارد.
- `.github/workflows/theme-ci.yml`: اعتبارسنجی Compose، lint نحوی PHP با PHP 8.3، بررسی JSON/JavaScript، آزمون رگرسیون دسترس‌پذیری و قراردادهای title-tag/heading/404، wrapper و breadcrumb WooCommerce، احترام CSS به کلاس‌های ستون محصول، logo markup و تطبیق hash/مجوز فونت رسمی در push و Pull Request. آزمون‌ها آمادگی runtime یا live را اثبات نمی‌کنند.
- `docs/launch-checklist-fa.md`: فهرست تنظیمات و آزمون‌های پیش از فروش واقعی.
- `tests/navigation-a11y.test.cjs`: آزمون بدون وابستگی Node برای رفتار Escape، مقصد focusable، title/heading قالب‌ها، 404، `wp_head()`, wrapper/breadcrumb بومی WooCommerce، قواعد منبعی گرید `columns-N`، logo markup و مسیرهای امن خروجی؛ رندر زنده را آزمایش نمی‌کند.
- `docs/technical-review-fa.md`: تصمیم‌ها، مستندات رسمی مبنا و مرزهای آزمون.
- `docs/phase1-design-go-live-plan-fa.md` و `docs/phase2-design-implementation-fa.md`: پیشنهاد و سابقهٔ مرحلهٔ طراحی.
- `docs/seo-review-fa.md`: تصمیم مستند برای ۱۴ مورد SEO درخواستی و گیت‌های باقی‌ماندهٔ WordPress admin، دامنه و staging.
- `docs/production-readiness-audit-fa.md`: ممیزی نهایی شواهد موجود، پیشنهادهای مشروط افزونه‌ها، آزمون‌های اجراشده و وابستگی‌های باز برای Go Live؛ این گزارش صریحاً ادغام راه‌دور یا آماده‌بودن سایت زنده را ادعا نمی‌کند.
- `docs/technical-gap-checklist-fa.md`: چک‌لیست ۲۳‌موردیِ اولویت‌دار برای تفکیک نقص کد، موک/نمونه و گیت‌های runtime؛ اصلاح ستون‌های گرید در کد اعمال شده، اما تأیید staging همچنان باز است.

## پیش از رفتن به محیط زنده

روی میزبان production نسخه‌های پشتیبانی‌شدهٔ WordPress، WooCommerce، PHP و پایگاه داده را با هم تطبیق دهید؛ TLS، پشتیبان و بازیابی، staging، دسترسی امن مدیر، ایمیل، کالاهای واقعی، قواعد ارسال/مرجوعی، مالیات و ارز را تنظیم کنید. درگاه را ابتدا با sandbox و کلیدهای نگهداری‌شده در محل امن بیازمایید. پرداخت live، دامنه، میزبان، کالا، اطلاعات حقوقی و حساب پذیرنده به داده و تأیید شما نیاز دارند. این مخزن **سایت production یا فروشگاه آمادهٔ گرفتن پول را به‌تنهایی ایجاد نمی‌کند**. جزئیات در [چک‌لیست انتشار](docs/launch-checklist-fa.md) آمده است.

## منابع رسمی

- [WooCommerce server requirements](https://woocommerce.com/document/server-requirements/)
- [WooCommerce 10.8 release notes](https://developer.woocommerce.com/2026/05/26/woocommerce-10-8-0-release/)
- [WordPress release archive](https://wordpress.org/download/releases/)
- [Docker Official WordPress image tags](https://hub.docker.com/_/wordpress)
- [Docker Official MariaDB image](https://hub.docker.com/_/mariadb)
- [WordPress Theme Handbook](https://developer.wordpress.org/themes/)
- [WooCommerce block and theme development](https://developer.woocommerce.com/docs/theming/block-theme-development/theming-woo-blocks/)
- [WooCommerce classic theme integration](https://developer.woocommerce.com/docs/theming/theme-development/classic-theme-developer-handbook/)
- [WordPress security guidance](https://developer.wordpress.org/apis/security/)
