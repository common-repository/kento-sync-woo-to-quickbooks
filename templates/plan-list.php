<?php foreach ($plans as $plan) {  
        $activated = '';
        if(isset($currentPlanId) && $currentPlanId == $plan->id) {
            $activated = 'activated';
        }
    ?>
    <div class="pricing-plan <?php echo $activated; ?>">
        <h2 class="pricing-header"><?php echo $plan->name ?></h2>
        <ul class="pricing-features">
            <li class="pricing-features-item"><?php echo $plan->plan_type ?></li>
            <?php if ($plan->max_orders_allowed != '') { ?>
                <li class="pricing-features-item"><?php echo $plan->max_orders_allowed ?>  Orders per month included</li>
            <?php } ?>
            <?php  if (!$change && $plan->trial_days != '') { ?>
                <li class="pricing-features-item">Free <?php echo $plan->trial_days ?> - Days Trial</li>
            <?php } ?>
            <?php if ($plan->dollar_per_order != '') { ?>
                <li class="pricing-features-item">Additional orders for $<?php echo $plan->dollar_per_order ?> per orders</li>
            <?php } ?>
            <?php foreach(explode('<br />', nl2br($plan->description)) as $description) { ?>
                <li class="pricing-features-item"><?php echo $description ?></li>
            <?php } ?>
        </ul>
        <div class="pricing-price">
            <span class="d-block">
                <?php if ($plan->price == "0.00") { ?>
                    FREE
                <?php } else {
                    echo "$".$plan->price;
                } ?>
            </span>
            <?php if ($plan->max_orders_allowed > $sync_orders) { ?>
                <a href="javascript:void(0);" data-change="<?php echo $change; ?>" data-id="<?php echo $plan->id; ?>" data-seller-uuid = "<?php echo $seller_uuid; ?>" class="pricing-button">
                    <?php echo $change ? "CHANGE" : ($plan->price == "0.00" ? "GET" : "BUY"); ?>
                </a>

                <a class="active-plan" href="javascript:void(0);">
                    <span class="dashicons dashicons-yes pr-4" style="font-size: 32px;"></span> ACTIVATED
                </a>
            <?php } else { ?>
                <div class="d-block text-danger">
                    <h6 class="disable-plan-error mb-0">Max order sync limit reached for this month.</h6>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>