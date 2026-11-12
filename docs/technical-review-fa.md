# بازبینی فنی پیش از ادغام Bamero

**تاریخ:** ۱۲ نوامبر ۲۰۲۶. این گزارش snapshot تغییرهای محلی را پیش از commit/push ثبت می‌کند. در آن زمان این تغییرها هنوز داخل head راه‌دور PR شمارهٔ ۱ نبودند؛ بنابراین هیچ نتیجهٔ CI قبلیِ آن PR، این diff را پوشش نمی‌دهد و این گزارش ادعای سبزشدن CI برای آن ندارد. پس از push، فقط checkهای موفقِ همان head جدید می‌توانند گیت merge باشند. هیچ آزمون یا پیکربندی production ادعا نمی‌شود.

## اصلاحات این بازبینی

بازبینی خودکار PR دو مشکل در HTML کشف کرد. `get_custom_logo()` خودش لینک خانه تولید می‌کند؛ بنابراین قالب اکنون آن را بدون لینک بیرونیِ تودرتو در wrapper grid قرار می‌دهد و فقط متن جایگزین لوگو را پیوند می‌کند. [3]

`get_the_archive_title()` در برخی زمینه‌ها HTML امنی مانند `<span class="archive-title">` دارد. خروجی آرشیو با `wp_kses_post()` پاک‌سازی می‌شود تا markup مجاز باقی بماند و خروجی خام چاپ نشود. [1] [2]

صفحهٔ فهرست نوشته‌ها اکنون عنوان صفحهٔ اختصاصی را از تنظیم `page_for_posts` می‌گیرد؛ در نبود آن نام سایت را نشان می‌دهد. کارت هر نوشته H2 است، نه H1 تکراری. Regression test یک H1 ثابت در هر قالب سفارشی را کنترل می‌کند.

WooCommerce 10.8.1 breadcrumb را در `nav` با نام پیش‌فرض `Breadcrumb` خروجی می‌دهد. پوسته از فیلتر رسمی defaults تنها رشتهٔ دقیق نام پیش‌فرض را به «مسیر راهنما» تبدیل می‌کند، مقدار فارسی را escape می‌کند و markup سفارشی افزونه را بازنویسی نمی‌کند. Breadcrumb دوم یا حذف hook اصلی به قالب اضافه نشده است. [4] [5] [6]

## آزمون‌های انجام‌شده

آزمون Node بدون وابستگی و syntax check برای Node.js 22 گذشت. آزمون رفتار Escape و skip-link، مقصد main قابل‌فوکوس، wrapper و breadcrumb WooCommerce، پشتیبانی title-tag، سلسله‌مراتب heading، 404 سفارشی، لوگو و خروجی آرشیو را بررسی می‌کند. تمام فایل‌های PHP پوسته با parser ایستا و grammar PHP 8.1 parse شدند؛ این معادل اجرای `php -l` یا تست WordPress نیست.

فایل‌های YAML مربوط به Compose و GitHub Actions parse شدند. `theme.json`، checksum و امضای WOFF2، مجوز OFL و ارجاع‌های گزارش SEO اعتبارسنجی شدند؛ `git diff --check` نیز گذشت. محیط حاضر Docker و PHP CLI اجرایی ندارد. در نتیجه Compose بالا نیامد و WooCommerce واقعی، checkout، افزونه‌ها، ایمیل، پرداخت، پیامک و HTML نهایی sitemap/robots/schema/canonical در آن اجرا نشدند.

GitHub Actions تعریف شده است تا برای PR بررسی نحوی PHP 8.3، Compose، JSON/JavaScript، regression Node و checksum فونت را اجرا کند. نتیجهٔ فعلی/قبلی روی SHA دیگر قابل تعمیم به این diff نیست. این‌ها گیت‌های کد هستند، نه گواهی عملکرد runtime. **گیت ادغام:** تغییرها را commit و push کنید؛ سپس آخرین head PR شمارهٔ ۱ و همهٔ checkها و بازخوردها را بخوانید و فقط در صورت موفقیت checkهای همان SHA و نبود بازخورد اصلاح‌نشده، آن را به `main` ادغام کنید. مجوز مالک برای ادغام این تغییرها قبلاً داده شده است.

## گیت‌های باقی‌ماندهٔ انتشار

دامنه و DNS، TLS و میزبان WordPress، افزونهٔ SEO، Site Icon مصوب، اطلاعات معتبر فروشنده و محصولات، پرداخت/ارسال/مالیات، تنظیمات robots و sitemap، structured data نهایی، status واقعی 404، staging، پشتیبان‌گیری و بازیابی هنوز نیازمند ورودی مالک و آزمون محیط واقعی‌اند. `llms.txt` اختیاری است و به‌عنوان عامل تضمین‌شدهٔ SEO ارائه نشده است. فهرست مرحله‌ای در [چک‌لیست go-live](launch-checklist-fa.md) و تصمیم منبع‌محور برای ۱۴ مورد SEO در [بازبینی SEO](seo-review-fa.md) است. ادغام به‌تنهایی سایت را deploy یا آمادهٔ دریافت پول نمی‌کند.

## منابع رسمی

[1]: https://developer.wordpress.org/reference/functions/get_the_archive_title/ "WordPress: get_the_archive_title()"
[2]: https://developer.wordpress.org/reference/functions/wp_kses_post/ "WordPress: wp_kses_post()"
[3]: https://developer.wordpress.org/reference/functions/get_custom_logo/ "WordPress: get_custom_logo()"
[4]: https://developer.woocommerce.com/docs/theming/theme-development/classic-theme-developer-handbook/ "WooCommerce classic theme integration"
[5]: https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.1/plugins/woocommerce/includes/wc-template-functions.php "WooCommerce 10.8.1 breadcrumb defaults"
[6]: https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.1/plugins/woocommerce/templates/global/breadcrumb.php "WooCommerce 10.8.1 breadcrumb template"
