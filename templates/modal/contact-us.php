<div class="modal fade" id="contactusModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Contact Us</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <div class="modal-body form-wizard">
                <p class="statusMsg"></p>
                <form id="contactus" class="" name="contactus" autocomplete="off" role="form">
                    <div class="form-group contact-name">
                        <label for="name">Your Name</label>
                        <input type="text" class="form-control wizard-required" name="name" id="cust_name" placeholder="Enter your name"/>
                        <div class="wizard-form-error"></div>
                    </div>
                    <div class="display-column-field"> 
                        <div class="form-group">
                            <label for="email">Your Email</label>
                            <input type="email" class="form-control wizard-required" name="email" id="cust_email" placeholder="Enter your email"/>
                            <div class="wizard-form-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" class="form-control wizard-required" name="subject" id="cust_subject" placeholder="Enter your subject"/>
                            <div class="wizard-form-error"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <?php wp_editor( "", 'message' ); ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-seller-uuid="<?php echo $seller_uuid; ?>" id="contact-us-send">Send</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>