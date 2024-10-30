<div class="modal fade" id="multi-tax-rates" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add More Tax Codes</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <div class="modal-body form-wizard">
                <p class="multitax_error text-danger"></p>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-outline-info add-more-tax float-right"><span class="dashicons dashicons-plus"></span> Add More</button>
                    </div>
                </div>
                <form id="MultiTaxRates" class="" name="MultiTaxRates" autocomplete="off" role="form">
                    <?php 
                        if(@$settings->multiTaxData) {
                            foreach ($settings->multiTaxData as $key => $value) { ?>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-1 multi_tax_squence"></div>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control multi_tax_title" value="<?php echo $key; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div  class="form-group">
                                                        <select class="form-control wizard-required">
                                                            <?php foreach ($settings->taxCodeList as $codeKey => $codeValue) { ?>
                                                                <option <?php if (@$settings->multiTaxData->$key == $codeKey) { echo 'selected="selected"'; } ?> value="<?php echo $codeKey ?>"><?php echo $codeValue ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="remove-tax-code text-danger btn btn-link"><span class="dashicons dashicons-dismiss"></span></button>                      
                                        </div>
                                    </div>
                                </div>
                        <?php } 
                        } else { ?>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-9">
                                        <div class="row">                                             
                                            <div class="col-md-6">
                                                <div class="form-group form-group-label">
                                                    <label>Tax Percentage</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div  class="form-group form-group-label">
                                                    <label>Tax code</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-1 multi_tax_squence"></div>
                                    <div class="col-md-9">
                                        <div class="row">                                             
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="multi_tax_title form-control" value="" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div  class="form-group">
                                                    <select class="form-control wizard-required">
                                                        <?php foreach ($settings->taxCodeList as $codeKey => $codeValue) { ?>
                                                            <option value="<?php echo $codeKey ?>"><?php echo $codeValue ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="wizard-form-error"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="remove-tax-code text-danger btn btn-link"><span class="dashicons dashicons-dismiss"></span></button>                      
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="multi-tax-rate-submit">Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="add_more_tax_code hidden">
    <div class="row">
        <div class="col-md-1 multi_tax_squence"></div>
        <div class="col-md-9">
            <div class="row">                                             
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="multi_tax_title form-control" value="" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div  class="form-group">
                        <select class="form-control wizard-required">
                            <?php foreach ($settings->taxCodeList as $codeKey => $codeValue) { ?>
                                <option value="<?php echo $codeKey ?>"><?php echo $codeValue ?></option>
                            <?php } ?>
                        </select>
                        <div class="wizard-form-error"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <button class="remove-tax-code text-danger btn btn-link"><span class="dashicons dashicons-dismiss"></span></button>                      
        </div>
    </div>
</div>