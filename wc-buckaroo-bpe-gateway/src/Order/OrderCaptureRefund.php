<?php

namespace Buckaroo\Woocommerce\Order;

use Buckaroo\Woocommerce\Core\PaymentGatewayRegistry;
use Buckaroo\Woocommerce\PaymentProcessors\Actions\RefundAction;
use Buckaroo\Woocommerce\Services\Request;
use WP_Error;

/**
 * Core class for order refund
 * php version 7.2
 *
 * @category  Payment_Gateways
 *
 * @author    Buckaroo <support@buckaroo.nl>
 * @copyright 2021 Copyright (c) Buckaroo B.V.
 * @license   MIT https://tldrlegal.com/license/mit-license
 *
 * @version   GIT: 3.3.0
 *
 * @link      https://www.buckaroo.eu/
 */
class OrderCaptureRefund
{
    public function __construct()
    {
        add_action('wp_ajax_bl_refund_capture', [$this, 'refund_capture']);
    }

    /**
     * Refund a capture
     *
     * @return void
     */
    public function refund_capture()
    {
        $request = new Request();
        if ($request->input('order_id') === null) {
            wp_send_json(
                [
                    'error' => __('A order id is required', 'wc-buckaroo-bpe-gateway'),
                ]
            );
        }
        if ($request->input('capture_id') === null) {
            wp_send_json(
                [
                    'error' => __('A capture id is required', 'wc-buckaroo-bpe-gateway'),
                ]
            );
        }

        $order_id = absint($request->input('order_id'));
        $capture_id = $request->input('capture_id');
        $capture = $this->get_capture_transaction_by_id($order_id, $capture_id);

        $successful_refund = false;

        if ($capture !== null && isset($capture['transaction_id'])) {
            $paymentMethod = get_post_meta($order_id, '_wc_order_selected_payment_method', true);
            $gateway = (new PaymentGatewayRegistry())->newGatewayInstance($paymentMethod);
            $successful_refund =
                (new RefundAction(
                    $gateway->newRefundProcessorInstance($order_id, $capture['amount'], ''),
                    $order_id,
                    $capture['transaction_id']
                ))
                    ->process();
        }

        if (is_object($successful_refund) && $successful_refund instanceof WP_Error) {
            wp_send_json(
                [
                    'error' => $successful_refund->get_error_message(),
                ]
            );
        }

        if ($successful_refund !== true) {
            wp_send_json(
                [
                    'error' => __('Cannot process refund', 'wc-buckaroo-bpe-gateway'),
                ]
            );
        }

        $this->refund_in_woocommerce($request, $order_id, $capture);
        $this->set_refunded_capture($order_id, $capture_id);
    }

    /**
     * Get a stored capture by its id
     *
     *
     * @return array|null
     */
    protected function get_capture_transaction_by_id(int $order_id, string $id)
    {
        $captures = get_post_meta($order_id, '_wc_order_captures');
        foreach ($captures as $capture) {
            if ($capture['id'] == $id) {
                return $capture;
            }
        }
    }

    /**
     * Refund items in woocommerce
     *
     *
     * @return void
     */
    protected function refund_in_woocommerce(Request $request, int $order_id, array $capture)
    {
        $order = wc_get_order($order_id);
        $capture_transaction = new CaptureTransaction($capture, $order);
        wc_create_refund(
            [
                'amount' => $capture_transaction->get_total_amount(),
                'reason' => $request->input('reason') ?? '',
                'order_id' => $order_id,
                'line_items' => $this->get_refund_items($capture_transaction),
                'refund_payment' => false,
                'restock_items' => $request->input('restock') == 'true',
            ]
        );
    }

    protected function get_refund_items(CaptureTransaction $capture_transaction)
    {
        $items_for_refund = [
            'qty' => [],
            'refund_total' => [],
            'refund_tax' => [],
        ];
        foreach ($capture_transaction->get_items() as $item) {
            $qty = $capture_transaction->get_qty($item->get_line_item_id());
            $items_for_refund['qty'][$item->get_line_item_id()] = $qty;
            $items_for_refund['refund_total'][$item->get_line_item_id()] = $this->get_refund_item_total($item, $qty);
            $items_for_refund['refund_tax'][$item->get_line_item_id()] = $this->get_refund_item_tax_total($item, $qty);
        }

        return $items_for_refund;
    }

    protected function get_refund_item_total(OrderItem $item, $qty)
    {
        return $item->get_unit_price(false) * $qty;
    }

    public function get_refund_item_tax_total(OrderItem $item, $qty)
    {
        $item_tax_total = [];

        $taxes = $item->get_taxes();

        if (isset($taxes['total'])) {
            foreach ($taxes['total'] as $tax_id => $tax) {
                $item_tax_total[$tax_id] = $tax / $item->get_quantity() * $qty;
            }
        }

        return $item_tax_total;
    }

    public function set_refunded_capture(int $order_id, string $capture_id)
    {
        $refunded_captures = $this->get_refunded_captures($order_id);
        array_push($refunded_captures, $capture_id);

        return update_post_meta(
            $order_id,
            'buckaroo_captures_refunded',
            json_encode($refunded_captures)
        );
    }

    /**
     * Get refunded captures for $order_id
     *
     *
     * @return array
     */
    protected function get_refunded_captures(int $order_id)
    {
        $refunded_captures = get_post_meta($order_id, 'buckaroo_captures_refunded', true);
        if (is_string($refunded_captures)) {
            $refunded_captures_decoded = json_decode($refunded_captures);
            if (is_array($refunded_captures_decoded)) {
                return $refunded_captures_decoded;
            }
        }

        return [];
    }
}
