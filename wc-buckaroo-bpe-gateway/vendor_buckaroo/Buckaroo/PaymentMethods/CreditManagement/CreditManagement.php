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

namespace BuckarooDeps\Buckaroo\PaymentMethods\CreditManagement;

use BuckarooDeps\Buckaroo\Models\Payload\DataRequestPayload;
use BuckarooDeps\Buckaroo\Models\Payload\PayPayload;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditManagement\Models\AddOrUpdateProductLines;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditManagement\Models\CreditNote;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditManagement\Models\Debtor;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditManagement\Models\DebtorFile;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditManagement\Models\DebtorInfo;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditManagement\Models\Invoice;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditManagement\Models\MultipleInvoiceInfo;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditManagement\Models\PaymentPlan;
use BuckarooDeps\Buckaroo\PaymentMethods\Interfaces\Combinable;
use BuckarooDeps\Buckaroo\PaymentMethods\PaymentMethod;

class CreditManagement extends PaymentMethod implements Combinable
{
    protected string $paymentName = 'CreditManagement3';
    protected array $requiredConfigFields = ['currency'];

    public function createInvoice()
    {
        $invoice = new Invoice($this->payload);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        $this->setServiceList('CreateInvoice', $invoice);

        return $this->dataRequest();
    }

    public function createCombinedInvoice()
    {
        $invoice = new Invoice($this->payload);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        $this->setServiceList('CreateCombinedInvoice', $invoice);

        return $this->dataRequest();
    }

    public function createCreditNote()
    {
        $creditNote = new CreditNote($this->payload);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        $this->setServiceList('CreateCreditNote', $creditNote);

        return $this->dataRequest();
    }

    public function addOrUpdateDebtor()
    {
        $debtor = new Debtor($this->payload);

        $this->setServiceList('AddOrUpdateDebtor', $debtor);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    public function createPaymentPlan()
    {
        $paymentPlan = new PaymentPlan($this->payload);

        $this->setServiceList('CreatePaymentPlan', $paymentPlan);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        $this->request->setData('Description', $this->payload['description'] ?? null);

        return $this->dataRequest();
    }

    public function terminatePaymentPlan()
    {
        $paymentPlan = new PaymentPlan($this->payload);

        $this->setServiceList('TerminatePaymentPlan', $paymentPlan);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    public function pauseInvoice()
    {
        $this->request->setData('Invoice', $this->payload['invoice'] ?? null);

        $this->setServiceList('PauseInvoice');

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    public function unpauseInvoice()
    {
        $this->request->setData('Invoice', $this->payload['invoice'] ?? null);

        $this->setServiceList('UnPauseInvoice');

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    public function invoiceInfo()
    {
        $multipleInvoices = new MultipleInvoiceInfo($this->payload);

        $this->request->setData('Invoice', $this->payload['invoice'] ?? null);

        $this->setServiceList('InvoiceInfo', $multipleInvoices);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    public function debtorInfo()
    {
        $debtorInfo = new DebtorInfo($this->payload);

        $this->setServiceList('DebtorInfo', $debtorInfo);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    public function addOrUpdateProductLines()
    {
        $addOrUpdateProductLines = new AddOrUpdateProductLines($this->payload);

        $this->setServiceList('AddOrUpdateProductLines', $addOrUpdateProductLines);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    public function resumeDebtorFile()
    {
        $debtor_file = new DebtorFile($this->payload);

        $this->setServiceList('ResumeDebtorFile', $debtor_file);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    public function pauseDebtorFile()
    {
        $debtor_file = new DebtorFile($this->payload);

        $this->setServiceList('PauseDebtorFile', $debtor_file);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }
}
