<?php
if ( ! class_exists( 'Kento_Woo_Orders', false ) ) {
	/**
	 * Get Orders details of woocommerce
     *
	 */
    include( plugin_dir_path( dirname(__FILE__) ) . 'includes/kento-woo-products.php' );

	class Kento_Woo_Orders extends Kento_Woo_Products{

        public function getOrderDetails($order_id) {
            $order      = wc_get_order( $order_id );
            $order_data = array(
                'order_id'                  => $order->get_id(),
                'order_number'              => $order->get_order_number(),
                'wc_status'                 => $order->get_status(),
                'currency'                  => $order->get_currency(),
                'version'                   => $order->get_version(),
                'prices_include_tax'        => $order->get_prices_include_tax(),
                'date_created'              => $this->format_datetime( $order->get_date_created() ? $order->get_date_created()->getTimestamp() : 0, false, true ),
                'date_modified'             => $this->format_datetime( $order->get_date_modified() ? $order->get_date_modified()->getTimestamp() : 0, false, false ),
                'discount_total'            => wc_format_decimal( $order->get_discount_total(), 2 ),
                'discount_tax'              => wc_format_decimal( $order->get_discount_tax(), 2 ),
                'shipping_total'            => wc_format_decimal( $order->get_shipping_total(), 2 ),
                'shipping_tax'              => wc_format_decimal( $order->get_shipping_tax(), 2 ),
                'total'                     => wc_format_decimal( $order->get_total(), 2 ),
                'formatted_total'           => $order->get_formatted_order_total(),
                'total_tax'                 => wc_format_decimal( $order->get_total_tax(), 2 ),
                'cart_tax'                  => wc_format_decimal( $order->get_cart_tax(), 2 ),
                'customer_id'               => $order->get_user_id(),
                'order_key'                 => $order->get_order_key(),
                'billing' => array(
                    'first_name' => $order->get_billing_first_name(),
                    'last_name'  => $order->get_billing_last_name(),
                    'company'    => $order->get_billing_company(),
                    'address_1'  => $order->get_billing_address_1(),
                    'address_2'  => $order->get_billing_address_2(),
                    'city'       => $order->get_billing_city(),
                    'state'      => $order->get_billing_state(),
                    'postcode'   => $order->get_billing_postcode(),
                    'country'    => $order->get_billing_country(),
                    'email'      => $order->get_billing_email(),
                    'phone'      => $order->get_billing_phone(),
                ),
                'shipping' => array(
                    'first_name' => $order->get_shipping_first_name(),
                    'last_name'  => $order->get_shipping_last_name(),
                    'company'    => $order->get_shipping_company(),
                    'address_1'  => $order->get_shipping_address_1(),
                    'address_2'  => $order->get_shipping_address_2(),
                    'city'       => $order->get_shipping_city(),
                    'state'      => $order->get_shipping_state(),
                    'postcode'   => $order->get_shipping_postcode(),
                    'country'    => $order->get_shipping_country(),
                ),
                'payment_method'            => $order->get_payment_method(),
                'payment_method_title'      => $order->get_payment_method_title(),
                'transaction_id'            => $order->get_transaction_id(),
                'created_via'               => $order->get_created_via(),
                'customer_note'             => $order->get_customer_note(),
                'date_completed'            => $order->get_date_completed() != null ? $this->format_datetime( $order->get_date_completed() ? $order->get_date_completed()->getTimestamp() : 0, false, false ) : null,
                'date_paid'                 => $order->get_date_paid() != null ? $this->format_datetime( $order->get_date_paid() ? $order->get_date_paid()->getTimestamp() : 0, false, false ) : null,
                'meta_data'                 => $order->get_meta_data(),
                'line_items'                => array(),
                'shipping_lines'            => array(),
                'tax_lines'                 => array(),
                'fee_lines'                 => array(),
                'coupon_lines'              => array(),
            );
            // add line items
            foreach ( $order->get_items() as $item_id => $item ) {
                $product                    = $item->get_product();
                $order_data['line_items'][] = array(
                    'order_id'      => $order_id,
                    'product_id'    => $item_id,
                    'name'          => $item->get_name(),
                    'sku'           => is_object( $product ) ? $product->get_sku() : null,
                    'variation_id'  => $item->get_variation_id(),
                    'quantity'      => $item->get_quantity(),
                    'tax_class'     => $item->get_tax_class(),
                    'subtotal'      => wc_format_decimal( $item->get_subtotal(), 2 ),
                    'subtotal_tax'  => wc_format_decimal( $item->get_subtotal_tax(), 2 ),
                    'total'         => wc_format_decimal( $item->get_total(), 2 ),
                    'total_tax'     => wc_format_decimal( $item->get_total_tax(), 2 ),
                );
            }
            $refundItems = [];
            $order_data['refunds'] = [];
            if ($order->get_refunds()) {
                $order_data['refunds'] = $this->get_refunds($order);
            }
            
            // payment json
            $payments = [
                'payment_method' => $order->get_payment_method(),
                'payment_method_title' => $order->get_payment_method_title(),
                'transaction_id'=> $order->get_transaction_id(),
            ];
            if ($order->get_payment_method() == 'ppec_paypal') {
                $payments = array_merge($payments , $order->get_meta( '_woo_pp_txnData' ));
                $payments['status'] = $order->get_meta( '_paypal_status' );
                $payments['transaction_fee'] = $order->get_meta( '_paypal_transaction_fee' );
            }
            $order_data['payments'] = $payments;
    
            // add shipping
            $order_data['shipping_lines'] = $this->get_shipping_lines($order);
    
            // add taxes
            $order_data['tax_lines'] = $this->get_tax_lines($order);
    
            // add fees
            $order_data['fee_lines'] = $this->get_fee_lines($order);
    
            // add coupons
            $order_data['coupon_lines'] = $this->get_coupon_lines($order);
    
            return $order_data;
        }

        public function get_shipping_lines($shipping) {
            $shipping_lines = [];
            foreach ( $shipping->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
                $shipping_lines[] = array(
                    'id'           => $shipping_item_id,
                    'method_id'    => $shipping_item->get_method_id(),
                    'method_title' => $shipping_item->get_name(),
                    'total'        => wc_format_decimal( $shipping_item->get_total(), 2 ),
                );
            }
            return $shipping_lines;
        }

        public function get_tax_lines($tax) {
            $tax_lines = [];
            foreach ( $tax->get_tax_totals() as $tax_code => $tax ) {
                $tax_lines[] = array(
                    'code'     => $tax_code,
                    'title'    => $tax->label,
                    'total'    => wc_format_decimal( $tax->amount, 2 ),
                    'compound' => (bool) $tax->is_compound,
                );
            }
            return $tax_lines;
        }

        public function get_fee_lines($fee) {
            $fee_lines = [];
            foreach ( $fee->get_fees() as $fee_item_id => $fee_item ) {
                $fee_lines[] = array(
                    'id'        => $fee_item_id,
                    'title'     => $fee_item->get_name(),
                    'tax_class' => $fee_item->get_tax_class(),
                    'total'     => wc_format_decimal( $fee->get_line_total( $fee_item ), 2 ),
                    'total_tax' => wc_format_decimal( $fee->get_line_tax( $fee_item ), 2 ),
                );
            }
            return $fee_lines;
        }

        public function get_coupon_lines($coupon) {
            $coupon_lines = [];
            foreach ( $coupon->get_items( 'coupon' ) as $coupon_item_id => $coupon_item ) {
                $coupon_lines[] = array(
                    'id'     => $coupon_item_id,
                    'code'   => $coupon_item->get_code(),
                    'amount' => wc_format_decimal( $coupon_item->get_discount(), 2 ),
                );
            }
            return $coupon_lines;
        }

        public function get_refunds($order) {
            $refundItems = [];
            foreach ( $order->get_refunds() as $refund ) {
                $refundItemsObj = [
                    'id'                    => $refund->get_id(),
                    'status'                => $refund->get_status(),
                    'currency'              => $refund->get_currency(),
                    'prices_include_tax'    => $refund->get_prices_include_tax(),
                    'date_created'          => $this->format_datetime( $refund->get_date_created() ? $refund->get_date_created()->getTimestamp() : 0, false, true ),
                    'discount_total'        => wc_format_decimal( $refund->get_discount_total(), 2 ),
                    'discount_tax'          => wc_format_decimal( $refund->get_discount_tax(), 2 ),
                    'shipping_total'        => wc_format_decimal( $refund->get_shipping_total(), 2 ),
                    'shipping_tax'          => wc_format_decimal( $refund->get_shipping_tax(), 2 ),
                    'total'                 => wc_format_decimal( $refund->get_total(), 2 ),
                    'total_tax'             => wc_format_decimal( $refund->get_total_tax(), 2 ),
                    'cart_tax'              => wc_format_decimal( $refund->get_cart_tax(), 2 ),
                    'amount'                => wc_format_decimal( $refund->get_amount(), 2 ),
                    'reason'                => $refund->get_reason() ? $refund->get_reason() : '',
                    'refunded_by'           => $refund->get_refunded_by(),
                    'refunded_payment'      => $refund->get_refunded_payment(),
                    'meta_data'             => $refund->get_meta_data(),
                ];

                // add shipping
                $refundItemsObj['shipping_lines'] = $this->get_shipping_lines($refund);

                // add taxes
                $refundItemsObj['tax_lines'] = $this->get_tax_lines($refund);

                // // add fees
                $refundItemsObj['fee_lines'] = $this->get_fee_lines($refund);

                // // add coupons
                $refundItemsObj['coupon_lines'] = $this->get_coupon_lines($refund);

                foreach ( $refund->get_items('line_item') as $refunded_item ) {
                    $item_id = absint( $refunded_item->get_meta( '_refunded_item_id' ) );

                    if($order->get_qty_refunded_for_item($item_id) != 0) {
                        $refundItemsObj['line_items'][] = [
                            'product_id'    => $item_id,
                            'quantity'      => $order->get_qty_refunded_for_item($item_id),
                            'total'         => $order->get_total_refunded_for_item($item_id),
                            'tax_lines'     => $tax_lines,
                        ];
                    }
                }
                $refundItems[] = $refundItemsObj;
            }
            return $refundItems;
        }
    }
}