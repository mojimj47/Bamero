<?php
declare(strict_types=1);

namespace Bamero\Core\Identity;

enum OtpPurpose: string
{
    case CustomerLogin = 'customer_login';
    case CustomerRegistration = 'customer_registration';
}
