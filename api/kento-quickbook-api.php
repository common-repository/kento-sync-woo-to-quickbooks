<?php
/**
 * Call Api requests for quickbook
 */

class Kento_Woo_Quickbook_Api {
    // api url for store settings
    protected static $apiUrl = KENTO_WOO_QB_SYNC_URL.'/api';

    // all apilisted here
    protected static $apiRequestUrl = [
        "seller_subscribe"      => '/seller/SELLER_UUID/PLAN_ID/subscribe',
        "connect_quickbook"     => '/seller/SELLER_UUID/connect/quickbook/CONNECTIONTYPE',
        "check_subcription"     => '/check/SELLER_UUID/subscription',
        "store_seller"          => '/sellers',
        "store_syncsettings"    => '/seller/SELLER_UUID/store/syncsettings',
        "get_qb_customers"      => '/seller/SELLER_UUID/quickbook/get/customers',
        "get_qb_products"       => '/seller/SELLER_UUID/quickbook/get/products',
        "create_qbAccount"      => '/seller/SELLER_UUID/quickbook/create/account',
        "create_qbProduct"      => '/seller/SELLER_UUID/quickbook/create/product',
        "create_qbCustomer"     => '/seller/SELLER_UUID/quickbook/create/customer',
        "get_dashboardDetails"  => '/seller/SELLER_UUID/get/dashboard/details',
        "send_query"            => '/seller/SELLER_UUID/send/query',
        "get_contacts"          => '/seller/SELLER_UUID/get/contacts',
        "deactivate_seller"     => '/seller/SELLER_UUID/deactivate',
        "get_sync_settings"     => '/seller/SELLER_UUID/get/sync_settings',
        "disconnect_quickbook"  => '/seller/SELLER_UUID/disconnect/quickbook',
        "get_wc_orders"         => '/seller/SELLER_UUID/get/wc_orders',
        "get_wc_products"       => '/seller/SELLER_UUID/get/wc_products',
        "update_auto_sync"      => '/seller/SELLER_UUID/update/auto_sync',
        "get_change_plans"      => '/seller/SELLER_UUID/change/plans',
        "change_plan"           => '/seller/SELLER_UUID/change/plan/PLAN_ID',
        "sync_orders"           => '/seller/SELLER_UUID/sync/wc_orders',
        "add_seller_contacts"   => '/seller/SELLER_UUID/add/contacts',
        "is_domain_changed"     => '/seller/SELLER_UUID/check/domain',
        "get_advanced_settings" => '/seller/SELLER_UUID/get/advanced_settings',
    ];

    // load css and js for woocommerce quickbook
    public function kt_quickbooks_load_css_js($hook)
    {
        $current_screen = get_current_screen();


        if ( strpos($current_screen->base, 'kt-wc-quick-books') === false) {
            return;
        } else {
            $ajaxPostPlanUrl = self::$apiUrl.self::$apiRequestUrl['seller_subscribe'];
            $quickbookConnectUrl = self::$apiUrl.self::$apiRequestUrl['connect_quickbook'];
            $api_array = array(
                'ajaxPostPlanUrl' => $ajaxPostPlanUrl,
                'quickbookConnectUrl' => $quickbookConnectUrl,
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'dashboardUrl' => admin_url( '/admin.php?page=kt-wc-quick-books-dashboard' ),
                'settingUrl' => admin_url( '/admin.php?page=kt-wc-quick-books-integration' ),
                'domain' => preg_replace( "#^[^:/.]*[:/]+#i", "", get_site_url()),
            );
            wp_enqueue_style('kt_wc_quickbook_admin_css', plugins_url('assets/css/admin.css',dirname( __FILE__ ) ));
            wp_enqueue_style('kt_quickbook_css', plugins_url('assets/css/quickbook.css',dirname( __FILE__ ) ));
            wp_enqueue_style('kt_bootstrap_css', plugins_url('assets/css/bootstrap.min.css',dirname( __FILE__ ) ));
            wp_enqueue_style('kt_bootstrap_datepicker_css', plugins_url('assets/css/bootstrap-datepicker.css',dirname( __FILE__ ) ));
            wp_enqueue_script('kt_bootstrap_script', plugins_url('assets/js/bootstrap.min.js', dirname( __FILE__ )));
            wp_enqueue_script('kt_bootstrap_datepicker_script', plugins_url('assets/js/bootstrap-datepicker.min.js', dirname( __FILE__ )));
            wp_register_script('kt_quickbook_script', plugins_url('assets/js/quickbook.js', dirname( __FILE__ )));
            wp_localize_script('kt_quickbook_script', 'quickbookVariables', $api_array);
            wp_enqueue_script('kt_quickbook_script');

            if(strpos($current_screen->base, 'kt-wc-quick-books-dashboard') != false) {
                wp_enqueue_style('kt_dashboard_css', plugins_url('assets/css/dashboard.css',dirname( __FILE__ ) ));
                wp_enqueue_script('kt_google_chart_script', plugins_url('assets/js/apex-chart.js', dirname( __FILE__ )));
                wp_enqueue_script('kt_chart_script', plugins_url('assets/js/chart.js', dirname( __FILE__ )));
            }
        }
        if(strpos($current_screen->base, 'kt-wc-quick-books-contact-us') != false) {
            do_action( 'wporg_after_settings_page_html' );
        }
    }

    // Call Post api global function with return response
    public function apiPostRequest($action, $request = null, $seller_uuid = null) {
        $requestUrl = self::$apiUrl.str_replace("SELLER_UUID", $seller_uuid, (self::$apiRequestUrl[$action] ?? $action));

        if (array_key_exists('plan_id', $request)) {
            $requestUrl = str_replace("PLAN_ID", $request['plan_id'], $requestUrl);
        }
        $options = [
            'method'      => 'POST',
            'body'        => $request,
            'timeout'     => 1000000,
        ];
        if($action == 'sync_orders') {
            // $options['blocking'] = false;
        }
        $response = wp_remote_post( $requestUrl, $options);
        //  echo "<pre>"; print_r($response); exit;
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            return "Something went wrong: $error_message";
        } else {
            if ($response['response']['code'] == 201) 
            { 
                $data = json_decode($response['body']);
                return $data;
            } else if ($response['response']['code'] == 404) {
                $this->redirect_to_setting();
            } else {
                $data = json_decode($response['body']);

                if (array_key_exists('errorcode', $data) || array_key_exists('error_code', $data)) {
                    return $data;
                } else {
                    return "Something went wrong: $data->msg";
                }
            }
        }
    }

    protected function redirect_to_setting() {
        wp_redirect(admin_url( '/admin.php?page=kt-wc-quick-books-integration' ));
    }
}