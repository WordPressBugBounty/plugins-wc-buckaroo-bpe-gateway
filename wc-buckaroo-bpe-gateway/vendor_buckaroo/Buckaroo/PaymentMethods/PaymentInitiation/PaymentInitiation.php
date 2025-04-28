<?php

namespace BuckarooDeps\Buckaroo\PaymentMethods\PaymentInitiation;

use BuckarooDeps\Buckaroo\Exceptions\BuckarooException;
use BuckarooDeps\Buckaroo\Models\Model;
use BuckarooDeps\Buckaroo\PaymentMethods\PayablePaymentMethod;
use BuckarooDeps\Buckaroo\PaymentMethods\PaymentInitiation\Models\Pay;
use BuckarooDeps\Buckaroo\Services\TraitHelpers\HasIssuers;
use BuckarooDeps\Buckaroo\Transaction\Response\TransactionResponse;

class PaymentInitiation extends PayablePaymentMethod
{
    use HasIssuers {
        issuers as traitIssuers;
    }
    protected string $paymentName = 'PayByBank';
    protected array $requiredConfigFields = ['currency', 'returnURL', 'returnURLCancel', 'pushURL'];

    /**
     * @param Model|null $model
     * @return TransactionResponse
     */
    public function pay(?Model $model = null): TransactionResponse
    {
        return parent::pay($model ?? new Pay($this->payload));
    }

    /**
     * @return array
     * @throws BuckarooException
     */
    public function issuers(): array
    {
        $this->serviceVersion = 1;

        return $this->traitIssuers();
    }
}
