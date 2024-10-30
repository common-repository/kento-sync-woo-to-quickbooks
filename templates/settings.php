<?php if (!$reconnect) { 
    $hideSettings = false;   
    ?>
    <?php if ($settings->customTxnNumbers == "true") {  ?>
        <div class="notice notice-warning">
            <p><span class="dashicons-before dashicons-warning text-warning"></span> Auto increment transaction number create is enable in QuickBooks.</p>
        </div>
    <?php } ?>
    <?php 
        include_once(plugin_dir_path( dirname(__FILE__) ) . 'templates/header.php');
    ?>
    <section id="quickbooks_tabs" class="wizard-section">
        <div class="float-right">
            <?php if (@$settings->settingData->id) { ?>
                <a href="<?php echo admin_url( '/admin.php?page=kt-wc-quick-books-advanced-settings' ); ?>" data-seller-uuid="<?php echo $seller_uuid; ?>" class="btn btn-sm btn-info advanced-settings">Advanced Settings</a>
            <?php } ?>
            <a href="javascript:void(0);" data-seller-uuid="<?php echo $seller_uuid; ?>" class="btn btn-sm btn-danger disconnect-quickbook">Disconnect QuickBooks</a>
        </div>
        <div class="setting-header">
            <div class="header-logo">
                <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . '/assets/img/logo.png'; ?>" alt="Kento Tech"/>
            </div>
            <h2>QuickBooks Settings</h2>
        </div>
        <?php if (!@$settings->settingData->id) { ?>
            <?php if ($settings->isDifferentCur == true) {
                $hideSettings = true;
                ?>
                <div class="currency-confirmation setting-confirm">
                    <div>
                        <p><?php echo $settings->currencyMsg; ?></p>
                        <div class="clearfix">
                            <a href="javascript:;" class="btn btn-warning text-white setting-confirm-btn">Confirm</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (($settings->qbBaseCurrency == 'USD' && $settings->taxAgencyFlag == false && $settings->ustaxCodeFlag == false) || ($settings->qbBaseCurrency != 'USD' && $settings->taxCodeFlag == false)){ 
                $displayBox = false;
                if (!$hideSettings) {
                    $hideSettings = true;
                    $displayBox = true;
                }
                ?>
                <div class="tax-confirmation setting-confirm <?php if (!$displayBox) { echo "hide"; } ?>">
                    <div>
                        <p><?php echo $settings->taxMsg; ?></p>
                        <div class="clearfix">
                            <a href="javascript:;" class="btn btn-warning text-white setting-confirm-btn">Confirm</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        <div class="row no-gutters <?php if ($hideSettings) { echo "hide"; } ?>" id="qb-settings">
            <div class="col-lg-12 col-md-12">
                <div class="form-wizard setting-form">
                    <form action="" class="row" method="post" role="form">
                        <div class="col-lg-4 col-md-4 form-wizard-header">
                            <ul class="list-unstyled form-wizard-steps clearfix">
                                <li class="active"><span>1</span><h5 class="tab-label">Orders</h5></li>
                                <li><span>2</span><h5 class="tab-label">Products</h5></li>
                                <li><span>3</span><h5 class="tab-label">Payments</h5></li>
                                <li><span>4</span><h5 class="tab-label">Taxes</h5></li>
                            </ul>
                        </div>
                        <div class="col-lg-8 col-md-8">
                            <fieldset class="wizard-fieldset show">
                                <h5>Orders Settings</h5>
                                <?php include_once('steps/order-settings.php'); ?>
                                <div class="form-group clearfix">
                                    <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                                </div>
                            </fieldset>	
                            <fieldset class="wizard-fieldset">
                                <h5>Products Settings</h5>
                                <?php include_once('steps/product-settings.php'); ?>
                                <div class="form-group clearfix">
                                    <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                    <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                                </div>
                            </fieldset>	
                            <fieldset class="wizard-fieldset">
                                <h5>Bank & Woocommerce Payments</h5>
                                <?php include_once('steps/bank-wc-payments.php'); ?>
                                <div class="form-group clearfix">
                                    <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                    <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                                </div>
                            </fieldset>	
                            <fieldset class="wizard-fieldset">
                                <h5>Tax Settings</h5>
                                <?php include_once('steps/tax-settings.php'); ?>
                                <div class="form-group clearfix">
                                    <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                    <a href="javascript:void(0);" data-seller-uuid="<?php echo $seller_uuid; ?>" class="form-wizard-submit float-right">Submit</a>
                                </div>
                            </fieldset>
                        </div>	
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php
    include_once(plugin_dir_path( dirname(__FILE__) ) . 'templates/footer.php');
    include_once('modal/add-account-modal.php');
    include_once('modal/add-cust-prod-modal.php');
    include_once('modal/confirmation-modal.php');
    if ($settings->qbBaseCurrency == "CAD") { 
        include_once('modal/cad-modal.php');
    } else if ($settings->qbBaseCurrency == "GBP") { 
        include_once('modal/gbp-modal.php');
    } else if ($settings->qbBaseCurrency != "USD") { 
        include_once('modal/add-more-tax-modal.php');
    }

    ?>
    <script>
    var accountLists = '<?php echo json_encode($settings->accountTypeList); ?>';
    </script>
<?php } else { ?>
<p>Reconnecting to quickbooks</p>
<div id="quickbook_connect" class="hidden">
    <button id="quickbook-connect-btn" class="quickbook-connect" data-type="<?php echo $connection_type; ?>" data-seller-uuid="<?php echo $seller_uuid; ?>"><span>QuickBook Connect </span></button>
</div>
<?php } ?>