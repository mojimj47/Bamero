# خط مبنای نسخه‌ها

آخرین ممیزی خط مبنا: ۱۴ نوامبر ۲۰۲۶

| جزء | نسخه/هدف | وضعیت |
|---|---:|---|
| WordPress | 7.1.4 | خط مبنای پروژه؛ staging compatibility الزامی است. |
| PHP | 8.5 | خط مبنای runtime و CI. |
| WooCommerce | 11.2.2 | هدف ثبت‌شده؛ قبل از production باید روی staging تأیید شود. |
| MariaDB | 11.8.9 | شاخه LTS مورد استفاده در Compose. |
| Node.js | 24 | شاخه LTS ابزارهای CI. |
| Bamero Core | 0.8.0 | Messaging، OTP primitives، ذخیره challenge، rate-limit bucket و HMAC هویت، کاتالوگ و demo fixture. |

## سیاست نگه‌داری

- نسخه‌های EOL، deprecated یا فاقد پشتیبانی امنیتی پذیرفته نیستند.
- patchهای امنیتی پایدار باید در بازه نگه‌داری بررسی و اعمال شوند.
- نسخه‌های production باید pin شوند و image digest پیش از انتشار ثبت شود.
- ارتقای دیتابیس با backup و migration کنترل‌شده انجام می‌شود؛ volume دیتابیس حذف نمی‌شود.
- خط مبنا جایگزین آزمون سازگاری staging نیست.
