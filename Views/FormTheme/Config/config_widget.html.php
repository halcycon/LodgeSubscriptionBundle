<?php
$fields    = $form->children;
$fieldKeys = array_keys($fields);
?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('lodge_subscription.config.subscription_settings'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <?php echo $view['form']->row($fields['standard_subscription_amount']); ?>
            </div>
            <div class="col-md-6">
                <?php echo $view['form']->row($fields['senior_subscription_amount']); ?>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('lodge_subscription.config.stripe_settings'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <?php echo $view['form']->row($fields['stripe_webhook_secret']); ?>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('lodge_subscription.config.payment_redirect_urls'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <?php echo $view['form']->row($fields['payment_success_url']); ?>
            </div>
            <div class="col-md-6">
                <?php echo $view['form']->row($fields['payment_cancel_url']); ?>
            </div>
        </div>
    </div>
</div> 