<?php

namespace Buckaroo\Woocommerce\Gateways\CreditCard;

use Buckaroo\Woocommerce\Core\Plugin;
use Buckaroo\Woocommerce\Gateways\AbstractPaymentGateway;
use Buckaroo\Woocommerce\Services\Helper;
use WC_Order;

class CreditCardGateway extends AbstractPaymentGateway
{
    public const PAYMENT_CLASS = CreditCardProcessor::class;

    public const REFUND_CLASS = CreditCardRefundProcessor::class;

    public const SHOW_IN_CHECKOUT_FIELD = 'show_in_checkout';

    public $creditCardProvider = [];

    protected $creditcardmethod;

    protected $creditcardpayauthorize;

    public bool $capturable = true;

    protected array $supportedCurrencies = [
        'ARS',
        'AUD',
        'BRL',
        'CAD',
        'CHF',
        'CNY',
        'CZK',
        'DKK',
        'EUR',
        'GBP',
        'HRK',
        'ISK',
        'JPY',
        'LTL',
        'LVL',
        'MXN',
        'NOK',
        'NZD',
        'PLN',
        'RUB',
        'SEK',
        'TRY',
        'USD',
        'ZAR',
    ];

    public static array $cards = [
        'amex_creditcard' => ['gateway_class' => Cards\AmexGateway::class],
        'cartebancaire_creditcard' => ['gateway_class' => Cards\CarteBancaireGateway::class],
        'cartebleuevisa_creditcard' => ['gateway_class' => Cards\CarteBleueVisaGateway::class],
        'dankort_creditcard' => ['gateway_class' => Cards\DankortGateway::class],
        'maestro_creditcard' => ['gateway_class' => Cards\MaestroGateway::class],
        'mastercard_creditcard' => ['gateway_class' => Cards\MastercardGateway::class],
        'nexi_creditcard' => ['gateway_class' => Cards\NexiGateway::class],
        'postepay_creditcard' => ['gateway_class' => Cards\PostePayGateway::class],
        'visa_creditcard' => ['gateway_class' => Cards\VisaGateway::class],
        'visaelectron_creditcard' => ['gateway_class' => Cards\VisaElectronGateway::class],
        'vpay_creditcard' => ['gateway_class' => Cards\VpayGateway::class],
    ];

    public function __construct()
    {
        $this->setParameters();
        $this->setCreditcardIcon();
        $this->has_fields = true;

        parent::__construct();

        $this->addRefundSupport();
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
            $this->registerControllers();
        }
    }

    private function registerControllers()
    {
        $namespace = 'woocommerce_api_wc_gateway_buckaroo_creditcard';

        add_action("{$namespace}-hosted-fields-token", [HostedFieldsController::class, 'getToken']);
    }

    /**
     * Validate fields
     *
     * @return void;
     */
    public function validate_fields()
    {
        parent::validate_fields();

        $creditCardMethod = $this->get_option('creditcardmethod');

        if ($creditCardMethod == 'encrypt' && $this->isSecure()) {
            $encryptedData = $this->request->input($this->id . '-encrypted-data');
            $issuer = $this->request->input($this->id . '-creditcard-issuer');

            if (empty($issuer) || $issuer === null) {
                wc_add_notice(__('Select a credit or debit card.', 'wc-buckaroo-bpe-gateway'), 'error');
            }

            if (empty($encryptedData) || $encryptedData === null) {
                wc_add_notice(__('Please complete the card form before proceeding.', 'wc-buckaroo-bpe-gateway'), 'error');

                return;
            }
        } else {
            $issuer = $this->request->input($this->id . '-creditcard-issuer');

            if ($issuer === null) {
                wc_add_notice(__('Select a credit or debit card.', 'wc-buckaroo-bpe-gateway'), 'error');
            }

            if (! in_array($issuer, array_keys($this->getAllCards()))) {
                wc_add_notice(__('A valid credit card is required.', 'wc-buckaroo-bpe-gateway'), 'error');
            }
        }
    }

    /**
     * Set gateway parameters
     *
     * @return void
     */
    public function setParameters()
    {
        $this->id = 'buckaroo_creditcard';
        $this->title = 'Credit and debit card';
        $this->method_title = 'Buckaroo Credit and debit card';
    }

    /**
     * Set credicard icon
     *
     * @return void
     */
    public function setCreditcardIcon()
    {
        $this->setIcon('svg/creditcards.svg');
    }

    /**
     * Returns true if secure (https), false if not (http)
     */
    public function isSecure()
    {
        return (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443;
    }

    /**
     * Process payment
     *
     * @param  int  $order_id
     * @return callable fn_buckaroo_process_response()
     */
    public function process_payment($order_id)
    {
        $processedPayment = parent::process_payment($order_id);

        if ($processedPayment['result'] == 'success' && $this->creditcardpayauthorize == 'authorize') {
            update_post_meta($order_id, '_wc_order_authorized', 'yes');
            $this->set_order_capture($order_id, 'Creditcard', $this->request->input($this->id . '-creditcard-issuer'));
        }

        return $processedPayment;
    }

    public function getAllCards()
    {
        return [
            'amex' => 'American Express',
            'cartebancaire' => 'Carte Bancaire',
            'cartebleuevisa' => 'Carte Bleue',
            'dankort' => 'Dankort',
            'maestro' => 'Maestro',
            'mastercard' => 'Mastercard',
            'nexi' => 'Nexi',
            'postepay' => 'PostePay',
            'visa' => 'Visa',
            'visaelectron' => 'Visa Electron',
            'vpay' => 'Vpay',
        ];
    }

    public function getCardsList()
    {
        $cards = [];
        $cardsDesc = $this->getAllCards();

        if (is_array($this->creditCardProvider)) {
            foreach ($this->creditCardProvider as $value) {
                if (isset($cardsDesc[$value])) {
                    $cards[] = [
                        'servicename' => $value,
                        'displayname' => $cardsDesc[$value],
                    ];
                }
            }
        }

        return $cards;
    }

    /**
     * Add fields to the form_fields() array, specific to this page.
     */
    public function init_form_fields()
    {
        parent::init_form_fields();

        $this->form_fields['creditcardmethod'] = [
            'title' => __('Credit and debit card method', 'wc-buckaroo-bpe-gateway'),
            'type' => 'select',
            'description' => __('Redirect user to Buckaroo or enter credit or debit card information (directly) inline in the checkout. SSL is required to enable inline credit or debit card information.', 'wc-buckaroo-bpe-gateway'),
            'options' => [
                'redirect' => 'Redirect',
                'encrypt' => 'Inline (Hosted Fields)',
            ],
            'default' => 'redirect',
            'desc_tip' => __('Check with Buckaroo whether Client Side Encryption is enabled, otherwise transactions will fail. If in doubt, please contact us.', 'wc-buckaroo-bpe-gateway'),

        ];
        $this->form_fields['creditcardpayauthorize'] = [
            'title' => __('Credit and debit card flow', 'wc-buckaroo-bpe-gateway'),
            'type' => 'select',
            'description' => __('Choose to execute Pay or Capture call', 'wc-buckaroo-bpe-gateway'),
            'options' => [
                'pay' => 'Pay',
                'authorize' => 'Authorize',
            ],
            'default' => 'pay',
        ];
        $this->form_fields['hosted_fields_client_id'] = [
            'title' => __('Buckaroo Hosted Fields Client ID', 'wc-buckaroo-bpe-gateway'),
            'type' => 'password',
            'description' => __('Enter your Buckaroo Hosted Fields Client ID, obtainable from the Buckaroo Plaza at -> Settings -> Token registration.', 'wc-buckaroo-bpe-gateway'),
        ];
        $this->form_fields['hosted_fields_client_secret'] = [
            'title' => __('Buckaroo Hosted Fields Client Secret', 'wc-buckaroo-bpe-gateway'),
            'type' => 'password',
            'description' => __('Enter your Buckaroo Hosted Fields Client Secret, obtainable from the Buckaroo Plaza at -> Settings -> Token registration.', 'wc-buckaroo-bpe-gateway'),
        ];
        $this->form_fields['AllowedProvider'] = [
            'title' => __('Allowed provider', 'wc-buckaroo-bpe-gateway'),
            'type' => 'multiselect',
            'options' => $this->getAllCards(),
            'description' => __('Select which credit or debit card providers will be visible to customer', 'wc-buckaroo-bpe-gateway'),
            'default' => array_keys($this->getCardsList()),
        ];
        $this->form_fields[self::SHOW_IN_CHECKOUT_FIELD] = [
            'title' => __('Show separate in checkout', 'wc-buckaroo-bpe-gateway'),
            'type' => 'multiselect',
            'options' => array_merge(['' => __('None', 'wc-buckaroo-bpe-gateway')], $this->getAllCards()),
            'description' => __('Select which credit or debit card providers will be shown separately in the checkout', 'wc-buckaroo-bpe-gateway'),
            'default' => [],
        ];
    }

    public function enqueue_scripts()
    {
        if (class_exists('WC_Order') && is_checkout()) {
            wp_enqueue_script(
                'buckaroo_hosted_fields',
                'https://hostedfields-externalapi.prod-pci.buckaroo.io/v1/sdk',
                [],
                Plugin::VERSION,
                true
            );
        }
    }

    /** {@inheritDoc} */
    public function process_admin_options()
    {
        parent::process_admin_options();
        $this->after_admin_options_update();
    }

    /**
     * Do code after admin options update
     *
     * @return void
     */
    public function after_admin_options_update()
    {
        set_transient('buckaroo_credicard_updated', true);
    }

    /**
     * Save only creditcards that are allowed
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function validate_show_in_checkout_field($key, $value)
    {
        $allowed = $this->settings['AllowedProvider'];
        if (is_array($value)) {
            return array_filter(
                $value,
                function ($provider) use ($allowed) {
                    return in_array($provider, $allowed);
                }
            );
        }

        return $value;
    }

    /**  {@inheritDoc} */
    protected function setProperties()
    {
        parent::setProperties();
        $this->creditCardProvider = $this->get_option('AllowedProvider', []);
        $this->creditcardmethod = $this->get_option('creditcardmethod', 'redirect');
        $this->creditcardpayauthorize = $this->get_option('creditcardpayauthorize', 'Pay');
    }

    public function canShowCaptureForm($order): bool
    {
        $order = Helper::resolveOrder($order);

        if (! $order instanceof WC_Order) {
            return false;
        }

        return $this->creditcardpayauthorize == 'authorize' && get_post_meta($order->get_id(), '_wc_order_authorized', true) == 'yes';
    }
}
