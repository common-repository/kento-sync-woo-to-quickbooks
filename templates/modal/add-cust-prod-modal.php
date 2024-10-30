<div class="modal fade" id="custProdModalForm" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="custProdModalLabel">Add Customer</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body form-wizard">
                <p class="statusMsg"></p>
                <form name="custProdForm" id="custProdForm" role="form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control wizard-required" name="displayName" id="cust_prod_name" placeholder="Enter name"/>
                        <div class="wizard-form-error"></div>
                    </div>
                    <div class="form-group hidden product_account_div">
                        <label for="product_account">Account</label>
                        <select class="form-control wizard-required" name="product_account" id="product_account">
                            <?php foreach ($settings->incomeAccountsList as $key => $value) { ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php } ?>
                        </select>
                        <div class="wizard-form-error"></div>
                    </div>
                </form>
            </div>    
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" data-seller-uuid="<?php echo $seller_uuid; ?>" data-type="" data-custprod-dropdownId="" class="btn btn-success saveCustProd">Save</button>
            </div>
        </div>
    </div>
</div>