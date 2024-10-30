<?php
/**
 * Call Api requests for woocommerce
 */
include( plugin_dir_path( dirname(__FILE__) ) . 'includes/kento-woo-orders.php' );

class Kento_Woo_Rest_Api extends Kento_Woo_Orders{

    function init() {
        register_rest_route( 'woocommerce/kento/v1', '/get/orders', [
          'methods' => 'POST',
          'callback' => [$this, 'GetWCOrders']
        ]);

        register_rest_route( 'woocommerce/kento/v1', '/get/data', [
            'methods' => 'POST',
            'callback' => [$this, 'GetDataByCondition']
        ]);
    }

    // Get woocommerce orders
    function GetWCOrders( $request ) {
        $params = wp_parse_args( $request->get_params());
        $params = array_map('sanitize_text_field',$params);
        
        $orders = $this->GetOrders($params);
        
        return $orders;
    }

    function GetOrders($params) {
        if (@$params['seller_uuid'] == get_option( 'kt_seller_uuid' )) {
            global $wpdb;

            $orders = [];

            $paginationQuery = $this->paginationQuery($params);

            $where = " post_type = 'shop_order' ";
            $parameters = [];
            $count = false;
            if(@$params['count']) {
                $count = $params['count']; 
            }

            if (array_key_exists('sync_start', $params) || array_key_exists('end_date', $params)) {
                if (array_key_exists('sync_start', $params)) {
                    $where .= " AND DATE(post_date) >= '%s'";
                    $parameters[] = date("Y-m-d", strtotime($params['sync_start']));
                }

                if (array_key_exists('end_date', $params)) {
                    $where .= " AND DATE(post_date) <= '%s'";
                    $parameters[] = date("Y-m-d", strtotime($params['end_date']));
                }

                if (array_key_exists('latest_order_id', $params)) {
                    $where .= " AND ID > %d";
                    $parameters[] = $params['latest_order_id'];
                }

                $query = $this->prepareQuery($where, $parameters, $paginationQuery);
               
            } elseif (array_key_exists('updated_sync_start', $params)) {
                $updated_start_from = date("Y-m-d H:i:s", strtotime($params['updated_sync_start']));

                $where .= " AND post_modified >= '%s'";
                $parameters[] = $updated_start_from;

                if (array_key_exists('latest_order_id', $params)) {
                    $where .= ' AND ID <= %d';
                    $parameters[] = $params['latest_order_id'];
                }

                $query = $this->prepareQuery($where, $parameters, $paginationQuery);

            } elseif (array_key_exists('from_order_id', $params)) {
                $where .= " AND ID >= %d";
                $parameters[] = $params['from_order_id'];

                $query = $this->prepareQuery($where, $parameters, $paginationQuery);

            } elseif (array_key_exists('id', $params)) {
                $orders[] = $this->getOrderDetails($params['id']);
                return $orders;
            } else {
                $query = $this->prepareQuery($where, $parameters, $paginationQuery);
            }

            $orderIds = $wpdb->get_results( $query );
            
            if($count) {
                return ['records' => count($orderIds)];
            }

            foreach ($orderIds as $order) {
                $orders[] = $this->getOrderDetails($order->ID);
            }
            return $orders;
        }

        return [
            'error' => 404,
            'msg' => 'Seller is not exists.'
        ];
    }

    function prepareQuery($where, $parameters, $paginationQuery) {
        global $wpdb;

        $query = $wpdb->prepare(
            "
                SELECT ID
                FROM $wpdb->posts
                WHERE $where order by ID ASC $paginationQuery
            ", $parameters);
        
        return $query;
    }

    function GetProducts($params) {
        if (@$params['seller_uuid'] == get_option( 'kt_seller_uuid' )) {
            global $wpdb;

            $products = [];
            $count = false;
            if(@$params['count']) {
                $count = $params['count']; 
            }
            
            $paginationQuery = $this->paginationQuery($params);
            
            $where = " post_type = 'product' ";
            $parameters = [];

            if (array_key_exists('id', $params)) {
                $product[] = $this->getProductDetails($params['id']);
                return $product;
            }  else {
                $query = $this->prepareQuery($where, $parameters, $paginationQuery);
            }

            $productIds = $wpdb->get_results( $query );

            if($count) {
                return ['records' => count($productIds)];
            }

            foreach ($productIds as $product) {
                $products[] = $this->getProductDetails($product->ID);
            }
            return $products;
        }

        return [
            'error' => 404,
            'msg' => 'Seller is not exists.'
        ];
    }

    function paginationQuery($params) {
        $return = '';
        if(@$params['limit']) {
            $limit = $params['limit'];
            $offset = 0;
            if(@$params['page'] && $params['page'] != 0) {
                $offset = $limit * ($params['page'] - 1);
            }
            $return = " LIMIT $limit OFFSET $offset";
        }
        return $return;
    }
    
    public function format_datetime( $timestamp, $convert_to_utc = false, $convert_to_gmt = false ) {
        if ( $convert_to_gmt ) {
            if ( is_numeric( $timestamp ) ) {
                $timestamp = date( 'Y-m-d H:i:s', $timestamp );
            }

            $timestamp = get_gmt_from_date( $timestamp );
        }

        if ( $convert_to_utc ) {
            $timezone = new DateTimeZone( wc_timezone_string() );
        } else {
            $timezone = new DateTimeZone( 'UTC' );
        }

        try {

            if ( is_numeric( $timestamp ) ) {
                $date = new DateTime( "@{$timestamp}" );
            } else {
                $date = new DateTime( $timestamp, $timezone );
            }

            // convert to UTC by adjusting the time based on the offset of the site's timezone
            if ( $convert_to_utc ) {
                $date->modify( -1 * $date->getOffset() . ' seconds' );
            }
        } catch ( Exception $e ) {

            $date = new DateTime( '@0' );
        }

        return $date->format( 'Y-m-d H:i:s' );
    }

    function GetDataByCondition( $request ) {
        $params = wp_parse_args( $request->get_params());
        $params = array_map('sanitize_text_field',$params);
        $data = [];
      
        if(@$params['type'] == 'raw-query') {
            if (@$params['seller_uuid'] == get_option( 'kt_seller_uuid' )) {
                global $wpdb;
                $rawQuery = str_replace("{", $wpdb->prefix, str_replace("}", "", $params['query']));
                $query = $wpdb->prepare($rawQuery);
                $data = $wpdb->get_results( $query );
            } else {
                return [
                    'error' => 404,
                    'msg' => 'Seller is not exists.'
                ];
            }
        } else {
            if(@$params['type'] == 'order') {
                if(@$params['start_date']) {
                    $params['sync_start'] = $params['start_date'];
                }
                
                $data = $this->GetOrders($params);
            } else if (@$params['type'] == 'product') {
                $data = $this->GetProducts($params);
            }
        }
        return $data;
    }
}