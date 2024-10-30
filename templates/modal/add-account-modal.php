<div class="modal fade" id="accountModalForm" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="accountModalLabel">Add Account</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body form-wizard">
                <p class="statusMsg"></p>
                <p>Create new Account in QuickBooks</p>
                <form name="accountForm" id="accountForm" role="form">
                    <div class="form-group">
                        <label for="account_name">Account Name</label>
                        <input type="text" class="form-control wizard-required" name="account_name" id="account_name" placeholder="Account name"/>
                        <div class="wizard-form-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="account_type">Type</label>
                        <select class="form-control wizard-required" name="account_type" id="account_type">
                            <option value="">--- Select Account Type ---</option>
                        </select>
                        <div class="wizard-form-error"></div>
                    </div>
                    <div class="form-group hidden">
                        <label for="account_sub_type">Sub Type</label>
                        <select class="form-control" name="account_sub_type" id="account_sub_type">
                        </select>
                    </div>
                </form>
            </div>    
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" data-seller-uuid="<?php echo $seller_uuid; ?>" data-account-dropdownId="" class="btn btn-success saveAccount">Save</button>
            </div>
        </div>
    </div>
</div>