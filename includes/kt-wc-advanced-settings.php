<?php
/**
 * Call get advanced settings for Quickbooks
 */

require_once( plugin_dir_path( dirname(__FILE__) ) . 'api/kento-quickbook-api.php' );

class Kt_Wc_QB_Advanced_Settings {
   
    /**
     * Get Advanced Settings
     */
    public function getAdvancedSettings() {
		$seller_uuid = get_option('kt_seller_uuid');
		$request = array();

		$quickBookApi = new Kento_Woo_Quickbook_Api;
		$advancedSettings = $quickBookApi->apiPostRequest('get_advanced_settings', $request, $seller_uuid);
	
		?>
		
		<?php 
			include_once(plugin_dir_path( dirname(__FILE__) ) . 'templates/header.php');
			echo $advancedSettings->data;
		?>
				<br class="clear">
		
		<?php
		include_once(plugin_dir_path( dirname(__FILE__) ) . 'templates/footer.php');
	}
}