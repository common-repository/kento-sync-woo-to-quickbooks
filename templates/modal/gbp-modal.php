<div class="modal fade" id="gbp-tax-rates" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Europe country's Tax Codes</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <div class="modal-body form-wizard">
                <form id="GBPTaxRates" class="" name="GBPTaxRates" autocomplete="off" role="form">
                    <div class="row col-md-12">
                        <?php foreach ($settings->GBPCountries as $key => $value) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12"><?php echo $value; ?></div>
                                        <div class="col-md-12">
                                            <div  class="form-group select">
                                                <select name="gbp_tax_rate[<?php echo $key; ?>]" id="gbp_tax_rate<?php echo $key; ?>" data-country="<?php echo $key; ?>" class="form-control wizard-required ">
                                                    <?php foreach ($settings->taxCodeList as $codeKey => $codeValue) { ?>
                                                        <option <?php if (@$settings->GBPTaxData->$key == $codeKey) { echo 'selected="selected"'; } ?> value="<?php echo $codeKey ?>"><?php echo $codeValue ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="wizard-form-error"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="gbp-tax-rate-submit" data-dismiss="modal">Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>