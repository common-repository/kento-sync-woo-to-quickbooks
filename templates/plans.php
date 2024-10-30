<?php 
    include_once(plugin_dir_path( dirname(__FILE__) ) . 'templates/header.php');
?>
<h5 class="plan-select-title">Select Plan for Connect QuickBooks</h5>
<div class="pricing-background">
    <div class="">
        <p class="col-md-10 text-center billing-text m-auto alert alert-warning">Select plan which suits your bussiness and your billing will be start after trial days end.</p>
        <div class="pricing-table">
            <input type="hidden" name="successPayment" id="successPayment" />
            <?php include_once('plan-list.php'); ?>
        </div>
    </div>
</div>
<?php
    include_once(plugin_dir_path( dirname(__FILE__) ) . 'templates/footer.php');