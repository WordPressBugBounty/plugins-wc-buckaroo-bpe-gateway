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

namespace BuckarooDeps\Buckaroo\PaymentMethods\Subscriptions\Models;

use BuckarooDeps\Buckaroo\Models\Address;
use BuckarooDeps\Buckaroo\Models\BankAccount;
use BuckarooDeps\Buckaroo\Models\Company;
use BuckarooDeps\Buckaroo\Models\Debtor;
use BuckarooDeps\Buckaroo\Models\Email;
use BuckarooDeps\Buckaroo\Models\Person;
use BuckarooDeps\Buckaroo\Models\Phone;
use BuckarooDeps\Buckaroo\Models\ServiceParameter;
use BuckarooDeps\Buckaroo\PaymentMethods\Subscriptions\Service\ParameterKeys\BankAccountAdapter;
use BuckarooDeps\Buckaroo\PaymentMethods\Subscriptions\Service\ParameterKeys\AddressAdapter;
use BuckarooDeps\Buckaroo\PaymentMethods\Subscriptions\Service\ParameterKeys\CompanyAdapter;

class Subscription extends ServiceParameter
{
    /**
     * @var bool
     */
    protected bool $includeTransaction;
    /**
     * @var float
     */
    protected float $transactionVatPercentage;
    /**
     * @var string
     */
    protected string $configurationCode;
    /**
     * @var string
     */
    protected ?string $subscriptionGuid;
    /**
     * @var int
     */
    protected int $termStartDay;
    /**
     * @var int
     */
    protected int $termStartMonth;
    /**
     * @var int
     */
    protected int $billingTiming;

    /**
     * @var string
     */
    protected string $termStartWeek;
    /**
     * @var string
     */
    protected string $b2b;
    /**
     * @var string
     */
    protected string $mandateReference;
    /**
     * @var string
     */
    protected string $allowedServices;

    /**
     * @var Debtor
     */
    protected Debtor $debtor;
    /**
     * @var BankAccountAdapter
     */
    protected BankAccountAdapter $bankAccount;
    /**
     * @var Email
     */
    protected Email $email;
    /**
     * @var Phone
     */
    protected Phone $phone;
    /**
     * @var AddressAdapter
     */
    protected AddressAdapter $address;
    /**
     * @var Person
     */
    protected Person $person;

    /**
     * @var Company
     */
    protected CompanyAdapter $company;

    /**
     * @var Configuration
     */
    protected Configuration $configuration;

    /**
     * @var RatePlan
     */
    protected RatePlan $addRatePlan;
    /**
     * @var RatePlan
     */
    protected RatePlan $updateRatePlan;
    /**
     * @var RatePlan
     */
    protected RatePlan $disableRatePlan;

    /**
     * @var RatePlanCharge
     */
    protected RatePlanCharge $addRatePlanCharge;

    /**
     * @var string
     */
    protected string $customerIBAN;
    /**
     * @var string
     */
    protected string $customerAccountName;
    /**
     * @var string
     */
    protected string $customerBIC;
    /**
     * @var array|\string[][]
     */
    protected array $groupData = [
        'debtor' => [
            'groupType' => 'Debtor',
        ],
        'person' => [
            'groupType' => 'Person',
        ],
        'email' => [
            'groupType' => 'Email',
        ],
        'address' => [
            'groupType' => 'Address',
        ],
        'company' => [
            'groupType' => 'Company',
        ],
        'phone' => [
            'groupType' => 'Phone',
        ],
        'addRatePlan' => [
            'groupType' => 'AddRatePlan',
        ],
        'configuration' => [
            'groupType' => 'AddConfiguration',
        ],
        'updateRatePlan' => [
            'groupType' => 'UpdateRatePlan',
        ],
        'disableRatePlan' => [
            'groupType' => 'DisableRatePlan',
        ],
        'addRatePlanCharge' => [
            'groupType' => 'AddRatePlanCharge',
        ],
        'updateRatePlanCharge' => [
            'groupType' => 'UpdateRatePlanCharge',
        ],
        'disableRatePlanCharge' => [
            'groupType' => 'DisableRatePlanCharge',
        ],
    ];

    /**
     * @param $debtor
     * @return Debtor
     */
    public function debtor($debtor = null)
    {
        if (is_array($debtor))
        {
            $this->debtor = new Debtor($debtor);
        }

        return $this->debtor;
    }

    /**
     * @param $bankAccount
     * @return BankAccount
     */
    public function bankAccount($bankAccount = null)
    {
        if (is_array($bankAccount))
        {
            $this->bankAccount = new BankAccountAdapter(new BankAccount($bankAccount));
        }

        return $this->bankAccount;
    }

    /**
     * @param $email
     * @return Email
     */
    public function email($email = null)
    {
        if (is_string($email))
        {
            $this->email = new Email($email);
        }

        return $this->email;
    }

    /**
     * @param $phone
     * @return Phone
     */
    public function phone($phone = null)
    {
        if (is_array($phone))
        {
            $this->phone = new Phone($phone);
        }

        return $this->phone;
    }

    /**
     * @param $address
     * @return AddressAdapter
     */
    public function address($address = null)
    {
        if (is_array($address))
        {
            $this->address = new AddressAdapter(new Address($address));
        }

        return $this->address;
    }

    /**
     * @param $person
     * @return Person
     */
    public function person($person = null)
    {
        if (is_array($person))
        {
            $this->person = new Person($person);
        }

        return $this->person;
    }

    /**
     * @param $company
     * @return Company|CompanyAdapter
     */
    public function company($company = null)
    {
        if (is_array($company))
        {
            $this->company = new CompanyAdapter(new Company($company));
        }

        return $this->company;
    }

    /**
     * @param $configuration
     * @return Configuration
     */
    public function configuration($configuration = null)
    {
        if (is_array($configuration))
        {
            $this->configuration = new Configuration($configuration);
        }

        return $this->configuration;
    }

    /**
     * @param $rate_plans
     * @return $this
     */
    public function ratePlans($rate_plans = null)
    {
        if (is_array($rate_plans))
        {
            foreach ($rate_plans as $type => $rate_plan)
            {
                $property = $type . 'RatePlan';

                $this->$property = new RatePlan($rate_plan);
            }
        }

        return $this;
    }

    /**
     * @param $rate_plan_charges
     * @return $this
     */
    public function ratePlanCharges($rate_plan_charges = null)
    {
        if (is_array($rate_plan_charges))
        {
            foreach ($rate_plan_charges as $type => $rate_plan_charge)
            {
                $property = $type . 'RatePlanCharge';

                $this->$property = new RatePlanCharge($rate_plan_charge);
            }
        }

        return $this;
    }
}
