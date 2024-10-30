<?php
if ( ! class_exists( 'Kento_Woo_QB_Menu', false ) ) {

    require_once( plugin_dir_path( dirname(__FILE__) ) . 'kento-sync-woo-to-quickbooks.php' );

	/**
	 * menu listed for plugin.
     *
	 */
	class Kento_Woo_QB_Menu {

        protected $kento_Woo_QB_Sync;
        /**
		 * Primary class constructor.
		 *
		 */
		public function __construct() {
			$this->kento_Woo_QB_Sync = new Kento_Woo_QB_Sync();
        }
        
        public function loadMenu() {
            include_once(plugin_dir_path( dirname(__FILE__) ) . 'listing/kt-wc-order-listing.php');
            include_once(plugin_dir_path( dirname(__FILE__) ) . 'listing/kt-wc-product-listing.php');
            include_once(plugin_dir_path( dirname(__FILE__) ) . 'includes/kt-wc-advanced-settings.php');
            
            add_menu_page(
                __( 'KentoTech Sync</br><span style="font-size:10px;">QuickBooks Online</span>', 'kt_wc_qb_sync' ),
                __( 'KentoTech Sync</br><span style="font-size:10px;">QuickBooks Online</span>', 'kt_wc_qb_sync' ),
                'manage_options',
                'kt-wc-quick-books-dashboard',
                array( $this->kento_Woo_QB_Sync, 'kt_wc_quickbooks_dashboard_callback'),
                plugin_dir_url( dirname(__FILE__) ) . 'assets/img/kento-icon.png',
                10
            );
            add_submenu_page(
                'kt-wc-quick-books-dashboard',
                __( 'Dashboard' ),
                __( 'Dashboard' ),
                'manage_options',
                'kt-wc-quick-books-dashboard',
                array( $this->kento_Woo_QB_Sync, 'kt_wc_quickbooks_dashboard_callback')
            );
            add_submenu_page(
                'kt-wc-quick-books-dashboard',
                __( 'Order Listing' ),
                __( 'Order Listing' ),
                'manage_options',
                'kt-wc-quick-books-order-listing',
                array( New Kt_Wc_QB_Order_List, 'getOrderlisting' )
            );

            add_submenu_page(
                'kt-wc-quick-books-dashboard',
                __( 'Product Listing' ),
                __( 'Product Listing' ),
                'manage_options',
                'kt-wc-quick-books-product-listing',
                array( New Kt_Wc_QB_Product_List, 'getProductlisting' )
            );
            add_submenu_page(
                '',
                __( '' ),
                __( '' ),
                'manage_options',
                'kt-wc-quick-books-change-plan',
                array( $this->kento_Woo_QB_Sync, 'changePlanList')
            );
            // add_action( "load-$hook", [ $this->kento_Woo_QB_Sync, 'screen_option' ] );
            add_submenu_page(
                'kt-wc-quick-books-dashboard',
                __( 'Settings' ),
                __( 'Settings' ),
                'manage_options',
                'kt-wc-quick-books-integration',
                array( $this->kento_Woo_QB_Sync, 'kt_wc_quickbooks_integration_callback')
            );

            add_submenu_page(
                '',
                __( '' ),
                __( '' ),
                'manage_options',
                'kt-wc-quick-books-advanced-settings',
                array( New Kt_Wc_QB_Advanced_Settings, 'getAdvancedSettings')
            );
        }

    }
}