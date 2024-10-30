<?php echo $data->notification; ?>
<div class="container">
    <h4>Dashboard
        <div class="float-right">
            <?php if ($data->connection_type == '') { ?>
                <a href="<?php echo admin_url( '/admin.php?page=kt-wc-quick-books-integration' ); ?>" class="btn btn-sm btn-success">Connect QuickBooks</a>
            <?php } else { ?>

                <a href="javascript:void(0);" data-seller-uuid="<?php echo $seller_uuid; ?>" class="btn btn-sm btn-danger disconnect-quickbook">Disconnect QuickBooks</a>

                <?php if($data->connection_type != 'live') { ?>
                    <a href="javascript:void(0);" data-type="live" data-seller-uuid="<?php echo $seller_uuid; ?>" class="btn btn-sm btn-info switch-quickbook-connect">Switch QB to Live</a>
                <?php }
            } ?>
        </div>
    </h4>
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-primary border-0">
                <!-- Card body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="h5 card-title text-uppercase text-muted mb-0 text-white">Settings</h5>
                            <span class="h2 font-weight-bold mb-0 text-white">&nbsp;</span>
                            <hr>
                        </div>
                        <div class="col-auto">
                            <div class="text-white dashicons-before dashicons-admin-generic"></div>
                        </div>
                    </div>
                    <p class="mt-1 mb-0 text-sm">
                        <a href="<?php echo admin_url( '/admin.php?page=kt-wc-quick-books-integration' ); ?>" class="text-nowrap text-white font-weight-600">Edit Settings</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-danger border-0">
                <!-- Card body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="h5 card-title text-uppercase text-muted mb-0 text-white">Unsync Orders</h5>
                            <span class="h2 font-weight-bold mb-0 text-white"><?php echo $data->unsyncOrders ? $data->unsyncOrders : 0; ?></span>
                            <hr>
                        </div>
                        <div class="col-auto">
                            <div class="text-white dashicons-before dashicons-list-view"></div>
                        </div>
                    </div>
                    <p class="mt-1 mb-0 text-sm">
                        <a href="<?php echo admin_url( '/admin.php?page=kt-wc-quick-books-order-listing&status=P' ); ?>" class="text-nowrap text-white font-weight-600">See details</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-info border-0">
                <!-- Card body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="h5 card-title text-uppercase text-muted mb-0 text-white">Your Contacts</h5>
                            <span class="h2 font-weight-bold mb-0 text-white"><?php echo $data->sellerContactsCount ? $data->sellerContactsCount : 0; ?></span>
                            <hr>
                        </div>
                        <div class="col-auto">
                            <div class="text-white dashicons-before dashicons-buddicons-buddypress-logo"></div>
                        </div>
                    </div>
                    <p class="mt-1 mb-0 text-sm">
                        <a href="javascript:void(0);" class="text-nowrap text-white font-weight-600" data-toggle="modal" data-target="#addContactsModal">Add Contacts</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-success border-0">
                <!-- Card body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="h5 card-title text-uppercase text-muted mb-0 text-white">Contact Us</h5>
                            <span class="h2 font-weight-bold mb-0 text-white">&nbsp;</span>
                            <hr>
                        </div>
                        <div class="col-auto">
                            <div class="text-white dashicons-before dashicons-email-alt"></div>
                        </div>
                    </div>
                    <p class="mt-1 mb-0 text-sm">
                        <a href="javascript:void(0);" class="text-nowrap text-white font-weight-600" data-toggle="modal" data-target="#contactusModal">Contact Us</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6">
            <div class="card card-header-actions">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="h3 mb-0">Sync Process</h3>
                        </div>
                        <div class="col text-right">
                            <a href="javascript:void(0);" data-seller-uuid="<?php echo $seller_uuid; ?>" class="btn btn-sm btn-success sync-orders">Start Sync Orders</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-danger">Start sync orders will only process 20 orders at once. This may take about few minutes to sync. If you see the loading icon after a few minutes, you can reload page.</p>
                    <p class="text-danger">Please velidate the data in your QuickBooks acocunt before proceeding with more orders sync.</p>
                    <hr>
                    <?php 
                        $modalAuto = '';
                        $msgAuto = '';
                        $classAuto = 'auto-sync-btn';
                        if($data->planDetails->price == '0.00') {
                            $modalAuto = ' data-toggle="modal" data-target="#alert-modal"';
                            $msgAuto = 'Auto Sync is not available with free plan.';
                            $classAuto = '';
                        } else {
                            if($data->syncOrders <= 0) {
                                $modalAuto = ' data-toggle="modal" data-target="#alert-modal"';
                                $msgAuto = 'Auto Sync will be enable after first manual sync, with manual sync you can be assured that sync is working properly.';
                                $classAuto = '';
                            }
                        }
                        if($msgAuto != '') {
                            echo '<p>'.$msgAuto.'</p>';
                        }
                    ?>
                    <div class="d-block text-center">
                        <?php if ($data->autoSync == 'Y') { ?>
                            <button data-seller-uuid="<?php echo $seller_uuid; ?>" <?php echo $modalAuto; ?> class="btn btn-sm text-white btn-danger <?php echo $classAuto; ?>">Disable Auto Sync</button>
                        <?php } else { ?>
                            <button data-seller-uuid="<?php echo $seller_uuid; ?>" <?php echo $modalAuto; ?> class="btn btn-sm text-white btn-warning <?php echo $classAuto; ?>">Enable Auto Sync</button>
                        <?php } ?>
                    </div>
                    <br>
                    <div class="success-sync-text d-block badge-success">
                        <h6 class="text-white mb-0"><span class="dashicons-before dashicons-yes"></span> Total sync orders from Quickbook by kento App : <?php echo $data->syncOrders; ?></h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card card-header-actions">
                <div class="card-header">
                    <h2 class="h3 mb-0">Sync & Unsync Orders of last 3 Months</h2>
                </div>
                <div class="card-body chart-body">
                    <div id="columnchart_material"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card card-header-actions">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="h3 mb-0">Plan Details</h3>
                        </div>
                        <?php if ($data->changePlansCount > 0) { ?>
                            <div class="col text-right">
                                <p class="text-danger change-plan-text">
                                    <?php 
                                        if($data->syncOrders > 0 && $data->planDetails->price == '0.00') {
                                            echo $data->leftSyncOrder." Orders Left"; 
                                        }
                                    ?>
                                    <a href="<?php echo admin_url( '/admin.php?page=kt-wc-quick-books-change-plan' ); ?>" class="btn btn-sm btn-info">
                                        <?php   
                                            if ($data->planDetails->price == '0.00') {
                                                echo "Upgrade Plan";
                                            } else {
                                                echo "Change Plan";
                                            }
                                        ?>
                                    </a>
                                </p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-block plan-detail">
                        <ul class="list-group list-group-flush list my--3">
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small>Domain:</small>
                                        <h5 class="h5 mb-0"><?php echo $data->planDetails->domain; ?></h5>
                                    </div>
                                    <div class="col">
                                        <small>Plan Name:</small>
                                        <h5 class="h5 mb-0"><?php echo $data->planDetails->name; ?></h5>
                                    </div>
                                    <div class="col">
                                        <small>Plan Price:</small>
                                        <h5 class="h5 mb-0"><?php echo "$".$data->planDetails->price; ?></h5>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small>Activation Date:</small>
                                        <h5 class="h5 mb-0"><?php echo $data->planDetails->plan_activated_on; ?></h5>
                                    </div>
                                    <div class="col">
                                        <small>Billing Start Date:</small>
                                        <h5 class="h5 mb-0"><?php echo $data->planDetails->billing_start_date; ?></h5>
                                    </div>
                                    <div class="col">
                                        <small>Billing Frequency:</small>
                                        <h5 class="h5 mb-0">
                                            <?php if ($data->planDetails->plan_type == 'Monthly') { ?>
                                                30 days
                                            <?php } else if ($data->planDetails->plan_type == 'Yearly') { ?>
                                                1 Year
                                            <?php } ?>
                                        </h5>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
include_once('modal/alert-modal.php');
include_once('modal/confirmation-modal.php');
include_once('modal/contact-us.php');
include_once('modal/seller-contacts.php');
if($data->syncOrders <= 0) {
    include_once('modal/auto-sync-modal.php');
}
?>
<script>
var chartOrders = '<?php echo json_encode($data->chartOrders); ?>';
var chartOrdersMonths = '<?php echo json_encode($data->chartOrdersMonths); ?>';
</script>