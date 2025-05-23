<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * It is available through the world-wide-web at this URL:
 * https://tldrlegal.com/license/mit-license
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@buckaroo.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@buckaroo.nl for more information.
 *
 * @copyright Copyright (c) BuckarooDeps\Buckaroo B.V.
 * @license   https://tldrlegal.com/license/mit-license
 */

namespace BuckarooDeps\Buckaroo\PaymentMethods\SEPA\Models;

use BuckarooDeps\Buckaroo\Models\Person;
use BuckarooDeps\Buckaroo\Models\ServiceParameter;
use BuckarooDeps\Buckaroo\PaymentMethods\SEPA\Service\ParameterKeys\CustomerAdapter;

class Pay extends ServiceParameter
{
    /**
     * @var CustomerAdapter
     */
    protected CustomerAdapter $customer;

    /**
     * @var string
     */
    protected string $bic;
    /**
     * @var string
     */
    protected string $iban;
    /**
     * @var string
     */
    protected string $collectdate;
    /**
     * @var string
     */
    protected string $mandateReference;
    /**
     * @var string
     */
    protected string $mandateDate;

    /**
     * @param $customer
     * @return CustomerAdapter
     */
    public function customer($customer = null)
    {
        if (is_array($customer))
        {
            $this->customer = new CustomerAdapter(new Person($customer));
        }

        return $this->customer;
    }
}
