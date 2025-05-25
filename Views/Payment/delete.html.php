<?php

/*
 * @copyright   2023 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'lodgeSubscriptionPayment');
$view['slots']->set('headerTitle', $view['translator']->trans('mautic.core.form.delete').' '.$view['translator']->trans('lodge.subscription.payment'));
?>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('mautic.core.form.delete').' '.$view['translator']->trans('lodge.subscription.payment'); ?></h3>
            </div>
            <div class="panel-body">
                <p><?php echo $view['translator']->trans('lodge.subscription.payment.delete_confirmation', ['%id%' => $payment->getId()]); ?></p>
                <p class="text-danger"><strong><?php echo $view['translator']->trans('mautic.core.form.delete.warning'); ?></strong></p>
                
                <h4><?php echo $view['translator']->trans('lodge.subscription.payment.details'); ?></h4>
                <dl class="dl-horizontal">
                    <dt><?php echo $view['translator']->trans('lodge.subscription.payment.id'); ?></dt>
                    <dd><?php echo $payment->getId(); ?></dd>
                    
                    <dt><?php echo $view['translator']->trans('lodge.subscription.payment.contact'); ?></dt>
                    <dd><?php echo $payment->getContactId(); ?></dd>
                    
                    <dt><?php echo $view['translator']->trans('lodge.subscription.payment.amount'); ?></dt>
                    <dd><?php echo number_format($payment->getAmount(), 2); ?></dd>
                    
                    <dt><?php echo $view['translator']->trans('lodge.subscription.payment.date'); ?></dt>
                    <dd><?php echo $view['date']->toFull($payment->getPaymentDate()); ?></dd>
                    
                    <dt><?php echo $view['translator']->trans('lodge.subscription.payment.year'); ?></dt>
                    <dd><?php echo $payment->getYear(); ?> <?php echo $payment->isArrears() ? '('.$view['translator']->trans('lodge.subscription.arrears').')' : ''; ?></dd>
                </dl>
            </div>
            <div class="panel-footer">
                <form action="<?php echo $view['router']->path('lodge_subscription_payment', ['objectAction' => 'delete', 'objectId' => $payment->getId()]); ?>" method="post">
                    <input type="hidden" name="form[payment_id]" value="<?php echo $payment->getId(); ?>" />
                    <button type="submit" class="btn btn-danger"><?php echo $view['translator']->trans('mautic.core.form.delete'); ?></button>
                    <a href="<?php echo $view['router']->path('lodge_subscription_action', ['objectAction' => 'view', 'objectId' => $contactId]); ?>" class="btn btn-default"><?php echo $view['translator']->trans('mautic.core.form.cancel'); ?></a>
                </form>
            </div>
        </div>
    </div>
</div> 