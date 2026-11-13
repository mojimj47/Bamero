# فهرست افزونه‌های پیشنهادی Bamero

این فهرست بر اساس گزارش فنی بامرو و وضعیت فعلی فهرست رسمی WordPress.org در ۱۴ نوامبر ۲۰۲۶ تنظیم شده است. هیچ افزونهٔ پولی خریداری یا فعال نشده و تا اتصال staging، نصب روی سایت واقعی انجام نشده است.

## هسته و فروشگاه

| افزونه | تصمیم | نکته |
|---|---|---|
| WooCommerce | ضروری | مرجع محصولات، قیمت، موجودی، سبد، سفارش و مشتری. نسخهٔ فعلی مخزن رسمی 11.2.2 با WordPress 7.1.4 آزموده شده است. |
| ووکامرس فارسی (persian-woocommerce) | پیشنهاد مشروط | فارسی‌سازی، شمسی‌سازی، تومان/ریال و شهرهای ایران. نسخهٔ فهرست رسمی 10.0.7؛ صفحهٔ رسمی در زمان بررسی Tested up to 7.1.3 اعلام کرده، پس با WordPress 7.1.4 و PHP 8.5 باید در staging تست شود. |
| Zibal Gateway for WooCommerce | ضروری برای پرداخت منتخب، اما فعلاً مسدود | صفحهٔ رسمی افزونه نسخهٔ 2.1 و Tested up to 6.9.11 نشان می‌دهد؛ پیش از فعال‌سازی با WordPress 7.1.4/PHP 8.5، callback، verify، مبلغ/واحد پول، چند trackId، تکرار callback و idempotency تست شود. تا آن زمان پرداخت واقعی فعال نشود. |

## سئو، امنیت و کارایی

| افزونه | تصمیم | نکته |
|---|---|---|
| Rank Math SEO | پیشنهاد | فقط یکی از Rank Math یا Yoast فعال باشد. نسخهٔ 1.0.281 در WordPress.org تا 7.1.4 آزموده شده است. Schema محصول باید با واحد پول IRR و قیمت ریالی استاندارد شود؛ تومان کد ISO ندارد. |
| Wordfence Security | پیشنهاد | Firewall، اسکن بدافزار و Login Security. نسخهٔ جاری را از WordPress.org بگیرید؛ بعد از فعال‌سازی WAF و مصرف منابع روی هاست واقعی سنجیده شود. |
| Two Factor | پیشنهاد برای مدیران | MFA مدیران/اپراتورها؛ برای ورود مشتریان استفاده نشود. نسخهٔ 0.17.0 تا WordPress 7.1.4 آزموده شده است. |
| LiteSpeed Cache | فقط روی LiteSpeed | روی Nginx/Apache غیر LiteSpeed نصب نکنید. سبد، checkout، My Account و callback درگاه باید از کش مستثنا شوند. نسخهٔ 7.9.3 تا WordPress 7.1.4 آزموده شده است. |
| UpdraftPlus | پیشنهاد برای بکاپ فایل/WordPress | نسخهٔ 1.26.14؛ پس از رخداد امنیتی اکتبر ۲۰۲۶ فقط آخرین patch را نصب کنید. مقصد بکاپ باید خارج از سرور اصلی و قابل‌بازیابی باشد. اسکریپت dump دیتابیس Bamero مکمل آن است، نه جایگزین تست restore. |

## مواردی که نصب نمی‌شوند

- WSMS و افزونه‌های پیامک عمومی: استفاده نمی‌شوند؛ پیامک از Bamero Core و ملی‌پیامک پیاده می‌شود.
- افزونهٔ WooCommerce SMS نسخهٔ 7.3.3: گزارش فنی CVE باز XSS/SQL Injection با شناسه CVE-2026-22352 ثبت کرده؛ در پروژه ممنوع است.
- افزونه‌های تکراری سئو، کش، امنیت، فرم‌ساز یا چند درگاه هم‌زمان: بدون نیاز روشن نصب نشوند.
- افزونهٔ پولی یا دارای ارتقای اجباری: بدون تأیید هزینه نصب نشود.

## ترتیب نصب پیشنهادی

1. WooCommerce و ووکامرس فارسی (staging و تست واحد پول/تاریخ).
2. Rank Math (پس از حذف/عدم نصب Yoast).
3. Wordfence و Two Factor برای مدیران.
4. LiteSpeed Cache فقط پس از تأیید نوع وب‌سرور و اعمال cache exclusions.
5. UpdraftPlus و تنظیم مقصد امن خارج از سرور و آزمون restore.
6. Zibal فقط پس از تست سازگاری و آزمون end-to-end درگاه روی staging.

## شواهد رسمی

- WooCommerce: https://wordpress.org/plugins/woocommerce/
- ووکامرس فارسی: https://wordpress.org/plugins/persian-woocommerce/
- Rank Math: https://wordpress.org/plugins/seo-by-rank-math/
- Wordfence: https://wordpress.org/plugins/wordfence/
- Two Factor: https://wordpress.org/plugins/two-factor/
- LiteSpeed Cache: https://wordpress.org/plugins/litespeed-cache/
- UpdraftPlus: https://wordpress.org/plugins/updraftplus/
- Zibal: https://wordpress.org/plugins/zibal-payment-gateway-for-woocommerce/
