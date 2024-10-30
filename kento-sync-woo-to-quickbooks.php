<?php
/**
 * Plugin Name: KentoSync for WooCommerce to QuickBooks
 * Description: A plugin to sync Invoices, Products and Customers automatically when an order is placed in WooCommerce to Quickbooks Online.
 * Author:  Kento Technologies
 * Author URI: https://kentotech.com/
 * Version: 1.5.1
 * 
 * Copyright: © 2020 Kento Technologies.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	if ( ! class_exists( 'Kento_Woo_QB_Sync' ) ) {

        define('KENTO_WOO_QB_SYNC_URL', 'https://kentotech.com');
        define('KENTO_WOO_PLUGIN_NAME', 'kento-sync-woo-to-quickbooks');
        
		/**
		 * Localisation
		 **/
		load_plugin_textdomain( 'Kento_Woo_QB_Sync', false, dirname( plugin_basename( __FILE__ ) ) . '/' );
        
		class Kento_Woo_QB_Sync {
			public function __construct() {
                include_once('api/kento-quickbook-api.php');
                include_once('api/kento-rest-api.php');
                include_once('includes/kento-woo-deactivation.php');
                
                add_action( 'plugins_loaded', array( $this, 'init' ) );
                add_action( 'rest_api_init', array( New Kento_Woo_Rest_Api, 'init' ) );

                if ( is_admin() ) {
                    // add_filter( 'set-screen-option', [__CLASS__, 'set_screen' ], 10, 3 );
                    add_action('admin_init', array( New Kento_Woo_Deactivation, '__construct'));
                    add_action('admin_menu', array( $this, 'kt_wc_quickbooks_options_page'), 100 );
                    add_action('admin_enqueue_scripts', array( New Kento_Woo_Quickbook_Api, 'kt_quickbooks_load_css_js'));
                    add_action('wp_ajax_kt_ajax_actionCall', array( $this, 'kt_ajax_action_callback'));
                    add_action('wp_ajax_nopriv_kt_ajax_actionCall', array( $this, 'kt_ajax_action_callback'));
                    add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
                }

            }
            
			/**
            * Initialize the plugin.
            */
            public function init() {
                // Set the plugin slug
                define( 'KT_QUICK_BOOKS_SLUG', 'kt-wc-quick-books-integration' );
                // // Setting action for plugin
                add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'Kento_Woo_QB_Sync_action_links' ));

            }

            /**
            * Add quickbook submenu in woocommerce
            */
            function kt_wc_quickbooks_options_page()
            {
                include_once( 'includes/kento-quickbook-menu.php' );

                $kentoQuickbookMenu = new Kento_Woo_QB_Menu();

                $kentoQuickbookMenu->loadMenu();

            }

            /**
            * Change Plan list
            */
            function changePlanList() {
                $seller_uuid = get_option('kt_seller_uuid');
                $quickBookApi = new Kento_Woo_Quickbook_Api;
                $request = array();
                $planDetails = $quickBookApi->apiPostRequest('get_change_plans', $request, $seller_uuid);
                $change = true;
                $plans = $planDetails->changePlans;
                $currentPlanId = $planDetails->currentPlanId;
                $sync_orders = $planDetails->sync_orders;

                if ($planDetails->currentPlanPrice == '0.00') {
                    $change = false;
                }
                include( 'templates/change-plans.php' );
            }

            /**
            * quickbook dashboard
            */
            function kt_wc_quickbooks_dashboard_callback() {
                $seller_uuid = get_option('kt_seller_uuid');
                $quickBookApi = new Kento_Woo_Quickbook_Api;
                $request = array();
                $data = $quickBookApi->apiPostRequest('get_dashboardDetails', $request, $seller_uuid);
                include( 'templates/dashboard.php' );
                if (!empty($data->planDetails) && trim($data->planDetails->domain) != trim($this->getSiteDomain())) {
                    include( 'includes/change-domain-alert.php' );
                    new Change_Domain_Alert();
                }
            }

            /**
            * Integrate quickbook with check subscription of seller
            */
            function kt_wc_quickbooks_integration_callback() {

                $seller_uuid = get_option('kt_seller_uuid');
                $request = array();
               
                $quickBookApi = new Kento_Woo_Quickbook_Api;
                $checkSubscribeData = $quickBookApi->apiPostRequest('check_subcription', $request, $seller_uuid);
                if (!is_string($checkSubscribeData)) {
                    $reconnect = false;
                    if ($checkSubscribeData->template == 'quickBooksSyncSetting') {
                        if (array_key_exists('errorcode', $checkSubscribeData->data) && $checkSubscribeData->data->errorcode == 403) {
                            $reconnect = true;
                            $connection_type = $checkSubscribeData->data->connection_type;
                        }
                        $settings = $checkSubscribeData->data;
                        $woocommercePayments = WC()->payment_gateways->get_available_payment_gateways();
                        // echo "<pre>"; print_r($woocommercePayments); exit;
                        include( 'templates/settings.php' );
                    } else if ($checkSubscribeData->template == 'quickBooksConnect') {
                        include( 'templates/quickbook-connect.php' );
                    } else {
                        $change = false;
                        $sync_orders = 0;
                        $plans = $checkSubscribeData->plans;
                        include( 'templates/plans.php' );
                    }
                } else {
                    echo $checkSubscribeData; exit;
                }
            }

            /**
            * Add quickbook setting link on plugin
            */
            function Kento_Woo_QB_Sync_action_links( $links ) {
                $links[] = '<a href="'. menu_page_url( KT_QUICK_BOOKS_SLUG, false ) .'">Settings</a>';

                return $links;
            }

            /**
             * Show row meta on the plugin screen.
             *
             * @param mixed $links Plugin Row Meta.
             * @param mixed $file  Plugin Base file.
             *
             * @return array
             */
            public static function plugin_row_meta( $links, $file ) {
                if ( plugin_basename( __FILE__ ) === $file ) {
                    $row_meta = array(
                        '<a href="'.KENTO_WOO_QB_SYNC_URL.'/doc/QuickBooks-Sync-for-Woo-WordPress.pdf" target="_blank">' . esc_html__( 'Setting Instruction', 'woo-quickbooks-sync' ) . '</a>'
                    );

                    return array_merge( $links, $row_meta );
                }

                return (array) $links;
            }

            public function getSiteDomain() {
                return preg_replace( "#^[^:/.]*[:/]+#i", "", get_site_url());
            }
            /**
            * Store seller data when plugin activate
            */
            public function storeSellerData() {
                $seller_uuid = get_option('kt_seller_uuid');
                
                $request = [
                    'seller_name' => get_option('blogname'),
                    'domain' => $this->getSiteDomain(),
                    'currency' => get_woocommerce_currency(),
                    'time_zone' => get_option('timezone_string'),
                    'billing_start_date' => date('Y-m-d'),
                    'email' => get_option('admin_email'),
                    'seller_uuid' => $seller_uuid
                ];

                $quickBookApi = new Kento_Woo_Quickbook_Api;
                $storeSeller = $quickBookApi->apiPostRequest('store_seller', $request);

                if (!is_string($storeSeller)) {
                    if ($seller_uuid) {
                        update_option('kt_seller_uuid', $storeSeller->uuid);
                    } else {
                        add_option('kt_seller_uuid', $storeSeller->uuid);
                    }
                } else {
                    echo $storeSeller; exit;
                }
            }

            /**
            * Deactivate seller data when plugin deactivate
            */
            public function deactivateSellerData() {
                $seller_uuid = get_option('kt_seller_uuid');
                $request = array();

                $quickBookApi = new Kento_Woo_Quickbook_Api;
                $deactivateSeller = $quickBookApi->apiPostRequest('deactivate_seller', $request, $seller_uuid);

                if (is_string($deactivateSeller)) {
                    echo $deactivateSeller; exit;
                }
            }
            /**
            * Save quickbook sync settings for woocommerce
            */
            public function kt_ajax_action_callback() {
                if (!empty( $_POST )) {
                    $request = $_POST;
                    $this->apiPostRequestWithSanitize($request);
                }
                die();
            }

            function apiPostRequestWithSanitize($request) {
                $apiAction = sanitize_text_field($request['api_action']);
                $seller_uuid = sanitize_text_field($request['seller_uuid']);

                $quickBookApi = new Kento_Woo_Quickbook_Api;
                $ajaxSyncData = $quickBookApi->apiPostRequest($apiAction, $request, $seller_uuid);

                if (!is_string($ajaxSyncData)) {
                    wp_send_json($ajaxSyncData);
                    // print_r($ajaxSyncData);
                } else {
                    echo $ajaxSyncData; 
                }
            }
        }
		// finally instantiate our plugin class and add it to the set of globals
        $GLOBALS['Kento_Woo_QB_Sync'] = new Kento_Woo_QB_Sync();

        register_activation_hook( __FILE__, 'wquick_plugin_activate' );
        /**
        * use this funation when plugin is activated
        */
        function wquick_plugin_activate() {
            $GLOBALS['Kento_Woo_QB_Sync']->storeSellerData(); 
        }

        register_deactivation_hook( __FILE__, 'wquick_plugin_deactivate' );

        function wquick_plugin_deactivate() {
            $GLOBALS['Kento_Woo_QB_Sync']->deactivateSellerData();
        }
	}
}