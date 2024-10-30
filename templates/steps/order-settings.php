<div class="form-group">
    <label for="prefix" class="">QuickBooks Order Prefix (Ideal to use if you have multiple sales channels)</label>
    <input type="text" maxlength="4" name="prefix" value="<?php echo @$settings->settingData->prefix; ?>" <?php if (!$settings->editable) { ?> disabled="disabled" <?php } ?> class="form-control <?php if (@$settings->settingData->prefix_required != 'Y') { echo 'wizard-required'; } ?> " id="prefix">
    <div class="wizard-form-error"></div>
</div>
<div class="wizard-form-checkbox">
    <input name="prefix_required" <?php if (@$settings->settingData->prefix_required == 'Y') { echo 'checked="checked"'; } ?> <?php if (!$settings->prefixEdit) { ?> disabled="disabled" <?php } ?> value="Y" id="prefix_required" type="checkbox">
    <label for="prefix_required">Disable Prefix But prefix is good to use if you have multiple sales channels connected with the same QB</label>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-md-12">If order fully paid, What you want to generate in QB?</div>
        <div class="col-md-12">
            <div class="form-group select">
                <select name="receipt_type" id="receipt_type" class="form-control wizard-required">
                    <?php foreach ($settings->fullyPaid_list as $key => $value) { ?>
                        <option <?php if (@$settings->settingData->receipt_type == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                    <?php } ?>
                </select>
                <div class="wizard-form-error"></div>
                <p>All partially paid orders will be sync as invoice only.</p>      
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="sync_start_date" class="">Select date from you want to start sync in QuickBooks?</label>
    <div class="select-with-add">
        <input type="input" value="<?php if (@$settings->settingData->sync_start_date != '') { echo date("m/d/Y", strtotime(@$settings->settingData->sync_start_date)); } ?>" class="form-control wizard-required" name="sync_start_date" id="sync_start_date">
        <div class="wizard-form-error"></div>
    </div>
    <p>If you want to Sync prior to max allowed date then Please 
        <a href="javascript:void(0);" data-toggle="modal" data-target="#contactusModal">Contact Us</a>
    .</p>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-md-12">Order Customer sync in QuickBooks as</div>
        <div class="col-md-12">
            <div class="form-group select">
                <select name="customer_type" id="customer_type" class="form-control wizard-required">
                    <?php foreach ($settings->customerType_list as $key => $value) { ?>
                        <option <?php if (@$settings->settingData->customer_type == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                    <?php } ?>
                </select>
                <div class="wizard-form-error"></div>
            </div>
        </div>
    </div>
</div>
<div class="form-group customer_list <?php if (@$settings->settingData->customer_type != "SAME") { echo 'hidden'; } ?>">
    <div class="row">
        <div class="col-md-12">Select QuickBooks Customer</div>
        <div class="col-md-12">
            <div class="row form-group select">
                <div class="select-with-add">
                    <select name="customer_id" id="customer_id" class="form-control <?php if (@$settings->settingData->customer_type == "SAME") { echo 'wizard-required'; } ?>">
                        <?php foreach ($settings->customerList as $key => $value) { ?>
                            <option <?php if (@$settings->settingData->customer_id == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="wizard-form-error"></div>
                </div>
                <div class="">
                    <span class="span-or"> - OR - </span>
                    <button type="button" id="add_customer" data-custprod-dropdown-id="customer_id" data-type="Customer" class="btn btn-outline-success cust-prod-modal-open">Add New</button>
                </div>
            </div>
        </div>
    </div>
</div>