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

namespace BuckarooDeps\Buckaroo\PaymentMethods\AfterpayDigiAccept\Models;

use BuckarooDeps\Buckaroo\Models\Address;
use BuckarooDeps\Buckaroo\Models\Company;
use BuckarooDeps\Buckaroo\Models\Email;
use BuckarooDeps\Buckaroo\Models\Phone;
use BuckarooDeps\Buckaroo\Models\ServiceParameter;
use BuckarooDeps\Buckaroo\PaymentMethods\Afterpay\Models\Person;
use BuckarooDeps\Buckaroo\PaymentMethods\AfterpayDigiAccept\Service\ParameterKeys\AddressAdapter;
use BuckarooDeps\Buckaroo\PaymentMethods\AfterpayDigiAccept\Service\ParameterKeys\EmailAdapter;
use BuckarooDeps\Buckaroo\PaymentMethods\AfterpayDigiAccept\Service\ParameterKeys\PhoneAdapter;
use BuckarooDeps\Buckaroo\PaymentMethods\AfterpayDigiAccept\Service\ParameterKeys\RecipientAdapter;

class Recipient extends ServiceParameter
{
    /**
     * @var string
     */
    private string $type;

    /**
     * @var RecipientAdapter
     */
    protected RecipientAdapter $recipient;
    /**
     * @var AddressAdapter
     */
    protected AddressAdapter $address;
    /**
     * @var PhoneAdapter
     */
    protected PhoneAdapter $phone;
    /**
     * @var EmailAdapter
     */
    protected EmailAdapter $email;

    /**
     * @param string $type
     * @param array|null $values
     */
    public function __construct(string $type, ?array $values = null)
    {
        $this->type = $type;

        parent::__construct($values);
    }

    /**
     * @param $recipient
     * @return RecipientAdapter
     */
    public function recipient($recipient = null)
    {
        if (is_array($recipient)) {
            $this->recipient = $this->getRecipientObject($recipient);
        }

        return $this->recipient;
    }

    /**
     * @param $address
     * @return AddressAdapter
     */
    public function address($address = null)
    {
        if (is_array($address)) {
            $this->address = new AddressAdapter($this->type, new Address($address));
        }

        return $this->address;
    }

    /**
     * @param $phone
     * @return PhoneAdapter
     */
    public function phone($phone = null)
    {
        if (is_array($phone)) {
            $this->phone = new PhoneAdapter($this->type, new Phone($phone));
        }

        return $this->phone;
    }

    /**
     * @param $email
     * @return EmailAdapter
     */
    public function email($email = null)
    {
        if (is_string($email)) {
            $this->email = new EmailAdapter($this->type, new Email($email));
        }

        return $this->email;
    }

    /**
     * @param array $recipient
     * @return RecipientAdapter
     */
    private function getRecipientObject(array $recipient) : RecipientAdapter
    {
        $model = new Person($recipient);

        if (($recipient['companyName'] ?? null) ||
            ($recipient['chamberOfCommerce'] ?? null) ||
            ($recipient['vatNumber'] ?? null)
        ) {
            $model = new Company($recipient);
        }

        return new RecipientAdapter($this->type, $model);
    }
}
