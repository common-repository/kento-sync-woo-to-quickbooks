<div id="auto-sync-alert" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Start Sync Process</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <div class="modal-body confirm-container">
                <p>Start sync orders will only process 20 orders at once. This may take about few minutes to sync. If you see the loading icon after a few minutes, you can reload page.</p>
                <p>Please velidate the data in your QuickBooks acocunt before proceeding with more orders sync.</p>
                <p class="text-danger">After successfully sync few orders auto sync option will be enable for you.</p>
                <hr>
                <div class="clearfix">
                    <a href="javascript:void(0);" data-seller-uuid="<?php echo $seller_uuid; ?>" class="btn btn-sm btn-success sync-orders">Start Sync Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>