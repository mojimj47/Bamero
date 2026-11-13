<?php
declare(strict_types=1);

namespace Bamero\Core\Admin;

use Bamero\Core\Messaging\SmsProviderFactory;

final class SmsAdminPage
{
    public static function register(): void
    {
        add_options_page(
            'پیامک بامرو',
            'پیامک بامرو',
            'manage_options',
            'bamero-sms',
            [self::class, 'render']
        );
    }

    public static function render(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('شما اجازهٔ دسترسی به این صفحه را ندارید.', 'bamero-core'));
        }

        $configured = SmsProviderFactory::isConfigured();
        ?>
        <div class="wrap" dir="rtl">
            <h1>پنل پیامکی بامرو</h1>
            <p>ارائه‌دهنده: <strong>ملی‌پیامک</strong></p>
            <p>وضعیت پیکربندی:
                <strong><?php echo $configured ? 'اطلاعات اتصال تنظیم شده است' : 'نیازمند پیکربندی'; ?></strong>
            </p>
            <p>اطلاعات محرمانه در این صفحه نمایش داده یا در گزینه‌های WordPress ذخیره نمی‌شوند.</p>
            <h2>تنظیم امن اتصال</h2>
            <p>مقادیر زیر را در متغیرهای محیطی PHP یا فایل پیکربندی خارج از مخزن تعریف کنید:</p>
            <ul>
                <li><code>BAMERO_MELIPAYAMAK_USERNAME</code> — نام کاربری پنل</li>
                <li><code>BAMERO_MELIPAYAMAK_API_KEY</code> — کلید API وب‌سرویس (نه رمز ورود عادی پنل)</li>
                <li><code>BAMERO_MELIPAYAMAK_SENDER</code> — شمارهٔ خط مجاز ارسال</li>
            </ul>
            <p>پس از تنظیم، PHP/WordPress را reload کنید. این صفحه فقط وجود تنظیمات را بررسی می‌کند و هیچ پیامکی ارسال نمی‌کند.</p>
            <p>ارسال آزمایشی تا زمان تأیید شمارهٔ فرستنده، اعتبار پنل و محیط staging غیرفعال است.</p>
        </div>
        <?php
    }
}
