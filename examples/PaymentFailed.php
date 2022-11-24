<?php

namespace App\Imports;

class PaymentFailed extends \Exception
{
    public const BAD_BILLING_ADDRESS = 1;

    public static function badBillingAddress(): self
    {
        return new self('This is a message', self::BAD_BILLING_ADDRESS);
    }
}
