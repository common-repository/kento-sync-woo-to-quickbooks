<?php if ($settings->qbBaseCurrency == "USD") { ?>
    <div class="wizard-form-checkbox">
        <input name="tax_agency_required" <?php if (@$settings->settingData->tax_agency_required == 'Y' || (@$settings->taxAgencyFlag == false && @$settings->ustaxCodeFlag == false)) { echo 'checked="checked"'; } ?> value="Y" id="tax_agency_required" type="checkbox">
        <label for="tax_agency_required"><strong>QuickBooks TaxAgency Not Required</strong></label>
        <p>If you are not collecting tax from your customer or you are not required to collect tax. Please check this checkbox.</p>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">QuickBooks TaxAgency</div>
            <div class="col-md-12">
                <div  class="form-group select">
                    <select name="tax_agency" id="tax_agency" class="form-control <?php if (@$settings->settingData->tax_agency_required != 'Y' && (@$settings->taxAgencyFlag != false && @$settings->ustaxCodeFlag != false)) { ?> wizard-required <?php } ?>">
                        <?php foreach ($settings->taxAgencyList as $key => $value) { ?>
                            <option <?php if (@$settings->settingData->tax_agency == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="wizard-form-error"></div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="wizard-form-checkbox">
        <input name="tax_code_required" <?php if (@$settings->settingData->tax_code_required == 'Y' || @$settings->taxCodeFlag == false) { echo 'checked="checked"'; } ?> value="Y" id="tax_code_required" type="checkbox">
        <label for="tax_code_required"><strong>QB Tax Code Not Required</strong></label>
        <p>If you are not collecting tax from your customer or you are not required to collect tax. Please check this checkbox.</p>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">QuickBooks Tax Code for Sales</div>
            <div class="col-md-12">
                <div  class="form-group select">
                    <select name="tax_code" id="tax_code" class="form-control <?php if (@$settings->settingData->tax_code_required != 'Y' && @$settings->taxCodeFlag != false) { ?> wizard-required <?php } ?>">
                        <?php foreach ($settings->taxCodeList as $key => $value) { ?>
                            <option <?php if (@$settings->settingData->tax_code == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="wizard-form-error"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">QuickBooks Tax Code for Zero rate</div>
            <div class="col-md-12">
                <div  class="form-group select">
                    <select name="zero_tax_code" id="zero_tax_code" class="form-control <?php if (@$settings->settingData->tax_code_required != 'Y' && @$settings->taxCodeFlag != false) { ?> wizard-required <?php } ?>">
                        <?php foreach ($settings->taxCodeList as $key => $value) { ?>
                            <option <?php if (@$settings->settingData->zero_tax_code == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="wizard-form-error"></div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <?php if ($settings->qbBaseCurrency == "CAD") { ?>
        <p class="text-danger"><strong>This tax configuration is required for canadian Shop charging diffrential tax codes based on the customer province. If you do not select a Tax Rate for specific province, The Rate selected above will be used as Default.</strong></p>
        <br>
        <input type="hidden" name="cad_multi_tax" id="cad_multi_tax" value='<?php echo @$settings->settingData->cad_multi_tax; ?>' />
        <button type="button" id="canada_tax_rates" class="btn btn-outline-info" data-toggle="modal" data-target="#canada-tax-rates">Provincial Tax Codes for Canada</button>
    <?php } else if ($settings->qbBaseCurrency == "GBP") { ?>
        <p class="text-danger"><strong>This tax configuration is required for Europien Shop charging diffrential tax codes based on the customer country. If you do not select a Tax Rate for specific country, The Rate selected above will be used as Default.</strong></p>
        <br>
        <input type="hidden" name="gbp_multi_tax" id="gbp_multi_tax" value='<?php echo @$settings->settingData->gbp_multi_tax; ?>' />
        <button type="button" id="gbp_tax_rates" class="btn btn-outline-info" data-toggle="modal" data-target="#gbp-tax-rates">Country Tax Codes for Europe</button>
    <?php } else { ?>
        <input type="hidden" name="multi_tax_codes" id="multi_tax_codes" value='<?php echo @$settings->settingData->multi_tax_codes; ?>' />
        <button type="button" id="multi_tax_rates" class="btn btn-outline-info" data-toggle="modal" data-target="#multi-tax-rates">Add More Tax Codes</button>
    <?php } ?>
<?php } ?>