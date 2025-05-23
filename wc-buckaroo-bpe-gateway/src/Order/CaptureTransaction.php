<?php

namespace Buckaroo\Woocommerce\Order;

use WC_Order;
use WC_Order_Factory;

/**
 * Core class for order capture
 * php version 7.2
 *
 * @category  Payment_Gateways
 *
 * @author    Buckaroo <support@buckaroo.nl>
 * @copyright 2021 Copyright (c) Buckaroo B.V.
 * @license   MIT https://tldrlegal.com/license/mit-license
 *
 * @version   GIT: 2.25.0
 *
 * @link      https://www.buckaroo.eu/
 */
class CaptureTransaction
{
    protected $data = [];

    /**
     * @var OrderItem[]
     */
    protected $items = [];

    /**
     * @var WC_Order
     */
    protected $order;

    protected $item_ids = [];

    public function __construct(array $data, WC_Order $order)
    {
        $this->data = $data;
        $this->order = $order;
        $this->init_items();
    }

    /**
     * Init order items from item ids
     *
     * @return void
     */
    private function init_items()
    {
        if (! isset($this->data['line_item_totals'])) {
            return;
        }
        $this->item_ids = array_keys(json_decode($this->data['line_item_totals'], true));
        $items = array_map(
            function ($itemId) {
                return $this->get_item($itemId);
            },
            $this->item_ids
        );

        $this->items = array_filter(
            $items,
            function ($item) {
                return $item !== null;
            }
        );
    }

    /**
     * Get order item by id
     *
     *
     * @return OrderItem|null
     */
    protected function get_item(int $item_id)
    {
        $item = WC_Order_Factory::get_order_item($item_id);

        if ($item === false) {
            return;
        }

        return new OrderItem(
            $item,
            $this->order
        );
    }

    public function get_id()
    {
        return $this->data['id'];
    }

    public function get_total_amount()
    {
        return $this->data['amount'];
    }

    public function has_item(int $item_id)
    {
        return in_array($item_id, $this->item_ids);
    }

    public function get_currency()
    {
        return $this->data['currency'];
    }

    public function get_transaction_id()
    {
        return $this->data['transaction_id'] ?? null;
    }

    /**
     * Get item qty
     *
     *
     * @return int
     */
    public function get_qty(int $item_id)
    {
        $qty = $this->get_item_value('line_item_qtys', $item_id);
        if ($qty === null) {
            $qty = 1;
        }

        return (int) $qty;
    }

    /**
     * Get qty/totals/tax value for item with item id,
     *
     * @param  array  $item_list
     * @return float|null
     */
    private function get_item_value(string $item_list, int $item_id)
    {
        if (isset($this->data[$item_list])) {
            $data = json_decode($this->data[$item_list], true);

            return $data[$item_id] ?? null;
        }
    }

    /**
     * Get item total
     *
     *
     * @return float
     */
    public function get_total(int $item_id)
    {
        return (float) $this->get_item_value('line_item_totals', $item_id);
    }

    /**
     * Get item tax total
     *
     *
     * @return array|null
     */
    public function get_tax_totals(int $item_id)
    {
        return $this->get_item_value('line_item_tax_totals', $item_id);
    }

    /**
     * Get items
     *
     * @return OrderItem[]
     */
    public function get_items()
    {
        return $this->items;
    }

    /**
     * Get woo order
     *
     * @return WC_Order
     */
    public function get_order()
    {
        return $this->order;
    }
}
