<?php 
    include_once(plugin_dir_path( dirname(__FILE__) ) . 'templates/header.php');
?>
<div id="quickbook_connect" class="col-md-12">
    <div class="row">
        <div class="col-md-2">&nbsp;</div>
        <div class="col-md-8">
            <div class="connection_dashboard">
                <h5><strong>Connect your QuickBooks online account for Woocommerce order sync</strong></h5>
                <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . '/assets/img/woo-qbo-sync.png'; ?>" class="woo-qbo-img" />
                <div class="connect-box mb-3">
                    <button class="quickbook-connect" data-type="live" data-seller-uuid="<?php echo $seller_uuid; ?>">
                        <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . '/assets/img/quickbooks-logo.png'; ?>" width="40" class="qbo-img" />
                        <span>Connect to QuickBooks</span>
                    </button>
                    <p>Connect your QuickBooks online account to sync orders</p>
                </div>
                <hr>
                <div class="connect-box">
                    <p>If you have Sandbox account for Quickbooks online, connect your Sandbox account to sync orders  <a class="quickbook-connect sandbox" data-type="sandbox" data-seller-uuid="<?php echo $seller_uuid; ?>">
                        Connect Sandbox 
                    </a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    include_once(plugin_dir_path( dirname(__FILE__) ) . 'templates/footer.php');