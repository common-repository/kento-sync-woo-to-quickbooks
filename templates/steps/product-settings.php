<div class="form-group">
    <div class="row">
        <div class="col-md-12">QuickBooks Sales Revenue Acocunt for Products</div>
        <div class="col-md-12">
            <div class="row form-group select">
                <div class="select-with-add">
                    <select name="sales_account" id="sales_account" class="form-control wizard-required">
                        <?php foreach ($settings->incomeAccountsList as $key => $value) { ?>
                            <option <?php if (@$settings->settingData->sales_account == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="wizard-form-error"></div>
                </div>
                <div class="">
                    <span class="span-or"> - OR - </span>
                    <button type="button" id="add_sales_account" data-account-dropdown-id="sales_account" data-account-type="income" class="btn btn-outline-success account-modal-open">Add New</button>
                </div>
                <p>This account will be used as sales/income account for new products. Existing matched products will be use as is.</p>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-md-12">Order Products sync in QuickBooks as</div>
        <div class="col-md-12">
            <div class="form-group select">
                <select name="product_type" id="product_type" class="form-control wizard-required">
                    <?php foreach ($settings->productType_list as $key => $value) { ?>
                        <option <?php if (@$settings->settingData->product_type == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                    <?php } ?>
                </select>
                <div class="wizard-form-error"></div>
            </div>
        </div>
    </div>
</div>
<div class="form-group product_list <?php if (@$settings->settingData->product_type != "SAME") { echo 'hidden'; } ?>">
    <div class="row">
        <div class="col-md-12">Select QuickBooks Product</div>
        <div class="col-md-12">
            <div class="row form-group select">
                <div class="select-with-add">
                    <select name="product_id" id="product_id" class="form-control <?php if (@$settings->settingData->product_type == "SAME") { echo 'wizard-required'; } ?>">
                        <?php foreach ($settings->productList as $key => $value) { ?>
                            <option <?php if (@$settings->settingData->product_id == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="wizard-form-error"></div>
                </div>
                <div class="">
                    <span class="span-or"> - OR - </span>
                    <button type="button" id="add_product" data-custprod-dropdown-id="product_id" data-type="Product" class="btn btn-outline-success cust-prod-modal-open">Add New</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group product-matching" style="<?php echo (@$settings->settingData->product_type == "SAME")?"display:none;":"" ?>">
    <?php 
    foreach ($settings->productMatching_List as $key => $value) {
            $selectedVal = array_keys((Array) $settings->productMatching_List)[0];
            if(@$settings->settingData->product_matching) {
                $selectedVal = $settings->settingData->product_matching;
            }
        ?>
        <div class="wizard-form-radio">
            <input name="product_matching" <?php if ($selectedVal == $key) { echo 'checked'; } ?> value="<?php echo $key; ?>" id="product_matching_<?php echo $key; ?>" type="radio">
            <label for="product_matching_<?php echo $key; ?>"><?php echo $value; ?></label>
        </div>
    <?php } ?>
</div>
<hr>
<?php if ($settings->qbShippingSetting == "true") { ?>
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">Shipping Setting</div>
            <div class="col-md-12">
                <div class="form-group select">
                    <select name="shipping_field" id="shipping_field" class="form-control wizard-required">
                        <?php foreach ($settings->shippingSettingList as $key => $value) { ?>
                            <option <?php if (@$settings->settingData->shipping_field == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="wizard-form-error"></div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <input type="hidden" value="<?php echo @$settings->settingData->shipping_field; ?>" name="shipping_field" />
<?php } ?>
<div class="form-group">
    <div class="row">
        <div class="col-md-12">Order Shipping Product sync in QuickBooks as</div>
        <div class="col-md-12">
            <div class="form-group select">
                <select name="shipping_product_type" id="shipping_product_type" class="form-control wizard-required">
                    <?php foreach ($settings->productType_list as $key => $value) { ?>
                        <option <?php if (@$settings->settingData->shipping_product_type == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                    <?php } ?>
                </select>
                <div class="wizard-form-error"></div>
            </div>
        </div>
    </div>
</div>
<div class="form-group shipping_product_list <?php if (@$settings->settingData->shipping_product_type != "SAME") { echo 'hidden'; } ?>">
    <div class="row">
        <div class="col-md-12">Select Shipping Product</div>
        <div class="col-md-12">
            <div class="row form-group select">
                <div class="select-with-add">
                    <select name="shipping_product_id" id="shipping_product_id" class="form-control <?php if (@$settings->settingData->shipping_product_type == "SAME") { echo 'wizard-required'; } ?>">
                        <?php foreach ($settings->productList as $key => $value) { ?>
                            <option <?php if (@$settings->settingData->shipping_product_id == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="wizard-form-error"></div>
                </div>
                <div class="">
                    <span class="span-or"> - OR - </span>
                    <button type="button" id="add_shipping_product" data-custprod-dropdown-id="shipping_product_id" data-type="Product" class="btn btn-outline-success cust-prod-modal-open">Add New</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-md-12">Sales account in QuickBooks for Shipping Service</div>
        <div class="col-md-12">
            <div class="row form-group select">
                <div class="select-with-add">
                    <select name="shipping_account" id="shipping_account" class="form-control wizard-required">
                        <?php foreach ($settings->incomeAccountsList as $key => $value) { ?>
                            <option <?php if (@$settings->settingData->shipping_account == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="wizard-form-error"></div>
                </div>
                <div class="">
                    <span class="span-or"> - OR - </span>
                    <button type="button" id="add_shipping_account" data-account-dropdown-id="shipping_account" data-account-type="income" class="btn btn-outline-success account-modal-open">Add New</button>
                </div>
                <p>This account books your revenue for shipping. Your shipping service name will be product name in QuickBooks.</p>
            </div>
        </div>
    </div>
</div>