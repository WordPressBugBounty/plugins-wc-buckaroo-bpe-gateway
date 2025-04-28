<?php

namespace BuckarooDeps\Buckaroo\PaymentMethods\PaymentInitiation\Models;

use BuckarooDeps\Buckaroo\Models\ServiceParameter;

class Pay extends ServiceParameter
{
    protected string $issuer;

    protected string $countryCode;
}
