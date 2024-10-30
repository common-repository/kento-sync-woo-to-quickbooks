<div class="form-group">
    <div class="row">
        <div class="col-md-12">QuickBooks Bank Account for Payment Deposit</div>
        <div class="col-md-12">
            <div class="row form-group select">
                <div class="select-with-add">
                    <select name="bank_account" id="bank_account" class="form-control wizard-required">
                        <?php foreach ($settings->depositedToAccountsList as $key => $value) { ?>
                            <option <?php if (@$settings->settingData->bank_account == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="wizard-form-error"></div>
                </div>
                <div class="">
                    <span class="span-or"> - OR - </span>
                    <button type="button" id="add_bank_account" data-account-dropdown-id="bank_account" data-account-type="deposited" class="btn btn-outline-success account-modal-open">Add New</button>
                </div>
                <p>This should be an actual business bank account.</p>
            </div>
        </div>
    </div>
</div>
<hr>
<h5>QB Account for Woocommerce Payments</h5>
<?php foreach ($woocommercePayments as $paymentKey => $paymentValue) { ?>
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">Payment account for "<?php echo $paymentValue->title; ?>"</div>
            <div class="col-md-12">
                <div class="row form-group select">
                    <div class="select-with-add">
                        <select name="payment_account[<?php echo $paymentKey; ?>]" id="payment_account_<?php echo $paymentKey; ?>" class="form-control">
                            <?php foreach ($settings->depositedToAccountsList as $key => $value) { 
                                    $payment_account = json_decode(@$settings->settingData->payment_account);
                                ?>
                                <option <?php if (@$payment_account->$paymentKey == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="">
                        <span class="span-or"> - OR - </span>
                        <button type="button" id="add_payment_account_<?php echo $paymentKey; ?>" data-account-dropdown-id="payment_account_<?php echo $paymentKey; ?>" data-account-type="deposited" class="btn btn-outline-success account-modal-open">Add New</button>
                    </div>
                    <p>Payment received through <?php echo $paymentValue->title; ?> will be deposited in this account.</p>
                </div>
            </div>
        </div>
    </div>
<?php } ?>