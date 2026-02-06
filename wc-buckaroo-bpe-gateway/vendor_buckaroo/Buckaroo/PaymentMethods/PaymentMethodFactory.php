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

namespace BuckarooDeps\Buckaroo\PaymentMethods;

use BuckarooDeps\Buckaroo\Exceptions\BuckarooException;
use BuckarooDeps\Buckaroo\PaymentMethods\Afterpay\Afterpay;
use BuckarooDeps\Buckaroo\PaymentMethods\AfterpayDigiAccept\AfterpayDigiAccept;
use BuckarooDeps\Buckaroo\PaymentMethods\Alipay\Alipay;
use BuckarooDeps\Buckaroo\PaymentMethods\ApplePay\ApplePay;
use BuckarooDeps\Buckaroo\PaymentMethods\Bancontact\Bancontact;
use BuckarooDeps\Buckaroo\PaymentMethods\BankTransfer\BankTransfer;
use BuckarooDeps\Buckaroo\PaymentMethods\Belfius\Belfius;
use BuckarooDeps\Buckaroo\PaymentMethods\Billink\Billink;
use BuckarooDeps\Buckaroo\PaymentMethods\Bizum\Bizum;
use BuckarooDeps\Buckaroo\PaymentMethods\Blik\Blik;
use BuckarooDeps\Buckaroo\PaymentMethods\BuckarooVoucher\BuckarooVoucher;
use BuckarooDeps\Buckaroo\PaymentMethods\BuckarooWallet\BuckarooWallet;
use BuckarooDeps\Buckaroo\PaymentMethods\ClickToPay\ClickToPay;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditCard\CreditCard;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditManagement\CreditManagement;
use BuckarooDeps\Buckaroo\PaymentMethods\Emandates\Emandates;
use BuckarooDeps\Buckaroo\PaymentMethods\EPS\EPS;
use BuckarooDeps\Buckaroo\PaymentMethods\ExternalPayment\ExternalPayment;
use BuckarooDeps\Buckaroo\PaymentMethods\GiftCard\GiftCard;
use BuckarooDeps\Buckaroo\PaymentMethods\GooglePay\GooglePay;
use BuckarooDeps\Buckaroo\PaymentMethods\iDeal\iDeal;
use BuckarooDeps\Buckaroo\PaymentMethods\iDealProcessing\iDealProcessing;
use BuckarooDeps\Buckaroo\PaymentMethods\iDealQR\iDealQR;
use BuckarooDeps\Buckaroo\PaymentMethods\iDin\iDin;
use BuckarooDeps\Buckaroo\PaymentMethods\In3\In3;
use BuckarooDeps\Buckaroo\PaymentMethods\In3Old\In3Old;
use BuckarooDeps\Buckaroo\PaymentMethods\KBC\KBC;
use BuckarooDeps\Buckaroo\PaymentMethods\KlarnaKP\KlarnaKP;
use BuckarooDeps\Buckaroo\PaymentMethods\KlarnaPay\KlarnaPay;
use BuckarooDeps\Buckaroo\PaymentMethods\KnakenPay\KnakenPay;
use BuckarooDeps\Buckaroo\PaymentMethods\Marketplaces\Marketplaces;
use BuckarooDeps\Buckaroo\PaymentMethods\MBWay\MBWay;
use BuckarooDeps\Buckaroo\PaymentMethods\Multibanco\Multibanco;
use BuckarooDeps\Buckaroo\PaymentMethods\NoServiceSpecifiedPayment\NoServiceSpecifiedPayment;
use BuckarooDeps\Buckaroo\PaymentMethods\Payconiq\Payconiq;
use BuckarooDeps\Buckaroo\PaymentMethods\PaymentInitiation\PaymentInitiation;
use BuckarooDeps\Buckaroo\PaymentMethods\Paypal\Paypal;
use BuckarooDeps\Buckaroo\PaymentMethods\PayPerEmail\PayPerEmail;
use BuckarooDeps\Buckaroo\PaymentMethods\PointOfSale\PointOfSale;
use BuckarooDeps\Buckaroo\PaymentMethods\Przelewy24\Przelewy24;
use BuckarooDeps\Buckaroo\PaymentMethods\SEPA\SEPA;
use BuckarooDeps\Buckaroo\PaymentMethods\Subscriptions\Subscriptions;
use BuckarooDeps\Buckaroo\PaymentMethods\Surepay\Surepay;
use BuckarooDeps\Buckaroo\PaymentMethods\Swish\Swish;
use BuckarooDeps\Buckaroo\PaymentMethods\Thunes\Thunes;
use BuckarooDeps\Buckaroo\PaymentMethods\Trustly\Trustly;
use BuckarooDeps\Buckaroo\PaymentMethods\Twint\Twint;
use BuckarooDeps\Buckaroo\PaymentMethods\WeChatPay\WeChatPay;
use BuckarooDeps\Buckaroo\PaymentMethods\Wero\Wero;
use BuckarooDeps\Buckaroo\Transaction\Client;

class PaymentMethodFactory
{
    /**
     * @var array|\string[][]
     */
    private static array $payments = [
        ApplePay::class => ['applepay'],
        GooglePay::class => ['googlepay'],
        Alipay::class => ['alipay'],
        Afterpay::class => ['afterpay'],
        AfterpayDigiAccept::class => ['afterpaydigiaccept'],
        Bancontact::class => ['bancontact', 'bancontactmrcash'],
        Bizum::class => ['bizum'],
        Billink::class => ['billink'],
        Blik::class => ['blik'],
        Belfius::class => ['belfius'],
        BuckarooWallet::class => ['buckaroo_wallet'],
        ClickToPay::class => ['clicktopay'],
        CreditCard::class =>
            [
                'creditcard', 'mastercard', 'visa',
                'amex', 'vpay', 'maestro',
                'visaelectron', 'cartebleuevisa',
                'cartebancaire', 'dankort', 'nexi',
                'postepay',
            ],
        CreditManagement::class => ['credit_management'],
        iDeal::class => ['ideal'],
        iDealProcessing::class => ['idealprocessing'],
        iDealQR::class => ['ideal_qr'],
        iDin::class => ['idin'],
        In3::class => ['in3'],
        In3Old::class => ['in3old'],
        KlarnaPay::class => ['klarna', 'klarnain'],
        KlarnaKP::class => ['klarnakp'],
        KnakenPay::class => ['knaken', 'knakenpay'],
        Multibanco::class => ['multibanco'],
        MBWay::class => ['mbway'],
        Surepay::class => ['surepay'],
        Swish::class => ['swish'],
        Subscriptions::class => ['subscriptions'],
        SEPA::class => ['sepadirectdebit', 'sepa'],
        KBC::class => ['kbc', 'kbcpaymentbutton'],
        Paypal::class => ['paypal'],
        PayPerEmail::class => ['payperemail'],
        PaymentInitiation::class => ['paymentinitiation','paybybank'],
        EPS::class => ['eps'],
        ExternalPayment::class => ['externalpayment'],
        Emandates::class => ['emandates'],
        Marketplaces::class => ['marketplaces'],
        NoServiceSpecifiedPayment::class => ['noservice'],
        Payconiq::class => ['payconiq'],
        Przelewy24::class => ['przelewy24'],
        PointOfSale::class => ['pospayment'],
        GiftCard::class => [
            'giftcard', 'westlandbon', 'babygiftcard', 'babyparkgiftcard',
            'beautywellness', 'boekenbon', 'boekenvoordeel',
            'designshopsgiftcard', 'fashioncheque', 'fashionucadeaukaart',
            'fijncadeau', 'koffiecadeau', 'kokenzo',
            'kookcadeau', 'nationaleentertainmentcard', 'naturesgift',
            'podiumcadeaukaart', 'shoesaccessories', 'webshopgiftcard',
            'wijncadeau', 'wonenzo', 'yourgift',
            'vvvgiftcard', 'parfumcadeaukaart',
        ],
        Thunes::class => [
            'thunes', 'monizzemealvoucher', 'monizzeecovoucher', 'monizzegiftvoucher',
            'sodexomealvoucher', 'sodexoecovoucher', 'sodexogiftvoucher',
        ],
        Trustly::class => ['trustly'],
        Twint::class => ['twint'],
        BankTransfer::class => ['transfer'],
        WeChatPay::class => ['wechatpay'],
        BuckarooVoucher::class => ['buckaroovoucher'],
        Wero::class => ['wero'],
    ];

    /**
     * @var Client
     */
    private Client $client;
    /**
     * @var string
     */
    private ?string $paymentMethod;

    /**
     * @param Client $client
     * @param string|null $paymentMethod
     */
    public function __construct(Client $client, ?string $paymentMethod)
    {
        $this->client = $client;
        $this->paymentMethod = ($paymentMethod)? strtolower($paymentMethod) : null;
    }

    /**
     * @return PaymentMethod
     * @throws BuckarooException
     */
    public function getPaymentMethod(): PaymentMethod
    {
        if ($this->paymentMethod) {
            foreach (self::$payments as $class => $alias) {
                if (in_array($this->paymentMethod, $alias)) {
                    return new $class($this->client, $this->paymentMethod);
                }
            }

            throw new BuckarooException(
                $this->client->config()->getLogger(),
                "Wrong payment method code has been given"
            );
        }

        return new NoServiceSpecifiedPayment($this->client, $this->paymentMethod);
    }

    /**
     * @param Client $client
     * @param string|null $paymentMethod
     * @return PaymentMethod
     * @throws BuckarooException
     */
    public static function get(Client $client, ?string $paymentMethod): PaymentMethod
    {
        $factory = new self($client, $paymentMethod);

        return $factory->getPaymentMethod();
    }
}
