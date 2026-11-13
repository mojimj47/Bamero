# خط مبنای نسخه‌های اجرایی Bamero

**تاریخ ممیزی:** ۱۴ نوامبر ۲۰۲۶  
**وضعیت:** نسخه‌های پایدار انتخاب‌شده برای محیط توسعه و CI؛ تأیید نهایی production منوط به آزمون staging است.

| جزء | نسخه هدف | مبنای انتخاب |
|---|---:|---|
| WordPress | 7.1.4 | آخرین نسخه پایدار در تاریخ ممیزی؛ image رسمی Docker موجود است. |
| PHP | 8.5 | شاخه فعال و پشتیبانی‌شده؛ نسخه 8.3 از پایان ۲۰۲۵ فقط پشتیبانی امنیتی دریافت می‌کند. |
| WooCommerce | 11.2.2 | آخرین نسخه پایدار در تاریخ ممیزی؛ باید روی staging با WordPress/PHP هدف آزمون شود. |
| MariaDB | 11.8.9 | آخرین patch پایدار از شاخه LTS 11.8 در تاریخ ممیزی. |
| Node.js | 24 | شاخه LTS فعال برای ابزارهای CI. |
| Bamero Core | 0.1.0 | نسخه داخلی اولیه؛ هنوز ورود OTP و درگاه پرداخت در آن پیاده‌سازی نشده‌اند. |

## سیاست نگه‌داری

- نسخه‌های WordPress و WooCommerce باید به‌صورت منظم به آخرین patch پایدار ارتقا یابند.
- برای PHP، نسخه فعال پشتیبانی‌شده هدف است؛ در تاریخ این سند PHP 8.5 انتخاب شده است.
- برای پایگاه داده، شاخه LTS پایدار انتخاب می‌شود و ارتقای major فقط پس از backup و آزمون migration انجام می‌گیرد.
- نسخه‌های Docker image در Compose صریح هستند؛ برای محیط production باید digest ایمیج‌ها نیز ثبت و pin شود.
- ابزارهای GitHub Actions و GitLab CI باید به نسخه‌های پشتیبانی‌شده ارتقا یابند و بازبینی امنیتی شوند.
- نصب WooCommerce، افزونه Zibal، WSMS و سایر افزونه‌های شخص ثالث هنوز در مخزن انجام نشده است. نسخه و سازگاری آن‌ها باید هنگام نصب از منبع رسمی دوباره بررسی شود.
- نسخه هدف به معنی تأیید سازگاری عملیاتی نیست؛ تا زمان تکمیل آزمون staging، انتشار production ممنوع است.

## نکته مهم برای محیط محلی

Compose از image جدید WordPress/PHP و MariaDB استفاده می‌کند و افزونه Bamero Core را نیز به مسیر WordPress mount می‌کند. اگر volume دیتابیس از اجرای قبلی با MariaDB 11.4 باقی مانده باشد، قبل از اجرای image جدید 11.8.9 از دیتابیس backup بگیرید و فرآیند ارتقای سازگار را طی کنید؛ حذف volume برای دور زدن migration مجاز نیست.

## منابع رسمی

- WordPress Docker tags: https://hub.docker.com/_/wordpress
- WordPress hosting requirements: https://wordpress.org/about/requirements/
- WordPress hosting handbook / PHP support: https://make.wordpress.org/hosting/handbook/server-environment/
- WooCommerce release: https://wordpress.org/plugins/woocommerce/
- WooCommerce PHP/WordPress compatibility: https://woocommerce.com/document/update-php-wordpress/
- MariaDB releases: https://mariadb.org/mariadb/all-releases/
- MariaDB maintenance periods: https://mariadb.org/about/
- Node.js release schedule: https://nodejs.org/en/about/previous-releases
