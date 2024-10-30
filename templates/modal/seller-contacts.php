<div class="modal fade" id="addContactsModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Contact</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <div class="modal-body form-wizard">
                <p class="statusMsg"></p>
                <div class="alert alert-info">
                    <h6 class="mb-0"><span class="dashicons-before dashicons-lock"></span> This detail will be  confidential. We will only use it if any query or feedback for this plugin.</h6>
                </div>
                <form id="seller_contacts" class="" name="seller_contacts" autocomplete="off" role="form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control wizard-required" name="name" id="contact_name" placeholder="Enter name"/>
                        <div class="wizard-form-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control wizard-required" name="email" id="contact_email" placeholder="Enter email"/>
                        <div class="wizard-form-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="phone_no">Phone No</label>
                        <input type="text" class="form-control" name="phone_no" id="cust_phone_no" placeholder="Enter your phone_no"/>
                    </div>
                </form>
                <div class="seller-contact-save">
                    <button type="button" class="btn btn-success" data-seller-uuid="<?php echo $seller_uuid; ?>" id="seller-contacts-send">Save</button>
                </div>
                <?php if($data->sellerContacts) { ?>
                    <hr>
                    <h5>Contact List</h5>
                    <div id="custom-quick-books-table">
                        <table class="wp-list-table widefat fixed striped wcproducts">
                            <thead>
                                <tr>
                                    <th scope="col" class="manage-column column-primary">Name</th>
                                    <th scope="col" class="manage-column">Email</th>
                                    <th scope="col" class="manage-column">Phone</th>
                                </tr>
                            </thead>
                            <tbody id="the-list">
                                <?php foreach($data->sellerContacts as $sellerContact) { ?>
                                    <tr>
                                        <td><?php echo $sellerContact->name; ?></td>
                                        <td><?php echo $sellerContact->email; ?></td>
                                        <td><?php echo $sellerContact->phone_no; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>