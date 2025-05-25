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
$view['slots']->set('mauticContent', 'lodgeSubscription');
$view['slots']->set('headerTitle', $view['translator']->trans('lodge.subscription.view') . ': ' . $contact->getName());

// Include CSS
$view['assets']->addStylesheet('plugins/LodgeSubscriptionBundle/Assets/css/lodgesubscription.css');
?>

<div class="row">
    <div class="col-md-9">
        <!-- Subscription Summary -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.current_status'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="lodge-subscription-details">
                    <div class="lodge-subscription-summary">
                        <?php $currentYear = $subscriptionDetails['current_year']; ?>
                        <?php $totalOwed = $subscriptionDetails['total_owed']; ?>
                        
                        <?php if ($totalOwed > 0): ?>
                            <div class="lodge-subscription-summary lodge-subscription-arrears">
                                <i class="fa fa-exclamation-circle"></i>
                                <?php echo $view['translator']->trans('lodge.subscription.outstanding_balance', [
                                    '%amount%' => number_format($totalOwed, 2),
                                ]); ?>
                            </div>
                        <?php else: ?>
                            <div class="lodge-subscription-summary lodge-subscription-current">
                                <i class="fa fa-check-circle"></i>
                                <?php echo $view['translator']->trans('lodge.subscription.paid_current'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo $view['translator']->trans('lodge.subscription.type'); ?></label>
                                <div class="value">
                                    <?php echo $contact->getFieldValue('craft_subscription_type') ?: 'Full'; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo $view['translator']->trans('lodge.subscription.current_year_status'); ?></label>
                                <div class="value">
                                    <?php if ($subscriptionDetails['current_year_paid']): ?>
                                        <span class="text-success">
                                            <i class="fa fa-check"></i> <?php echo $view['translator']->trans('lodge.subscription.paid'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-danger">
                                            <i class="fa fa-times"></i> <?php echo $view['translator']->trans('lodge.subscription.unpaid'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo $view['translator']->trans('lodge.subscription.last_payment'); ?></label>
                                <div class="value">
                                    <?php $lastPaymentDate = $contact->getFieldValue('craft_last_payment_date'); ?>
                                    <?php if ($lastPaymentDate): ?>
                                        <?php echo $view['date']->toDate($lastPaymentDate); ?>
                                    <?php else: ?>
                                        <?php echo $view['translator']->trans('mautic.core.none'); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo $view['translator']->trans('lodge.subscription.current_year_amount'); ?></label>
                                <div class="value">
                                    <?php echo number_format($subscriptionDetails['amount_owed_current'], 2); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo $view['translator']->trans('lodge.subscription.arrears_amount'); ?></label>
                                <div class="value">
                                    <?php echo number_format($subscriptionDetails['amount_owed_arrears'], 2); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo $view['translator']->trans('lodge.subscription.total_outstanding'); ?></label>
                                <div class="value">
                                    <strong><?php echo number_format($totalOwed, 2); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-lg">
                    <?php if ($totalOwed > 0): ?>
                        <a href="<?php echo $view['router']->path('lodge_subscription_payment', [
                            'objectAction' => 'new',
                            'objectId'     => $contact->getId(),
                        ]); ?>" class="btn btn-primary lodge-payment-button">
                            <i class="fa fa-money"></i> <?php echo $view['translator']->trans('lodge.subscription.payment.new'); ?>
                        </a>
                        
                        <button class="btn btn-success lodge-payment-button" id="generatePaymentLink" data-contact-id="<?php echo $contact->getId(); ?>">
                            <i class="fa fa-link"></i> <?php echo $view['translator']->trans('lodge.subscription.generate_payment_link'); ?>
                        </button>
                    <?php endif; ?>
                </div>
                
                <?php if ($subscriptionDetails['amount_owed_arrears'] > 0): ?>
                    <div class="mt-md">
                        <button class="btn btn-warning lodge-payment-button" id="generateArrearsPaymentLink" data-contact-id="<?php echo $contact->getId(); ?>" data-type="arrears">
                            <i class="fa fa-link"></i> <?php echo $view['translator']->trans('lodge.subscription.generate_arrears_payment_link'); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Previous Years -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.previous_years'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th><?php echo $view['translator']->trans('lodge.subscription.settings.year'); ?></th>
                                <th><?php echo $view['translator']->trans('lodge.subscription.due'); ?></th>
                                <th><?php echo $view['translator']->trans('lodge.subscription.paid'); ?></th>
                                <th><?php echo $view['translator']->trans('mautic.core.status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($subscriptionDetails['previous_years'])): ?>
                                <?php foreach ($subscriptionDetails['previous_years'] as $year => $yearData): ?>
                                    <tr>
                                        <td><?php echo $year; ?></td>
                                        <td>
                                            <?php if ($yearData['due']): ?>
                                                <i class="fa fa-check text-success"></i>
                                            <?php else: ?>
                                                <i class="fa fa-times text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($yearData['paid']): ?>
                                                <i class="fa fa-check text-success"></i>
                                            <?php else: ?>
                                                <i class="fa fa-times text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($yearData['paid']): ?>
                                                <span class="label label-success"><?php echo $view['translator']->trans('lodge.subscription.paid'); ?></span>
                                            <?php elseif ($yearData['due']): ?>
                                                <span class="label label-danger"><?php echo $view['translator']->trans('lodge.subscription.unpaid'); ?></span>
                                            <?php else: ?>
                                                <span class="label label-default"><?php echo $view['translator']->trans('lodge.subscription.not_applicable'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center"><?php echo $view['translator']->trans('lodge.subscription.no_previous_data'); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <!-- Payment History -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.payment.history'); ?></h3>
            </div>
            <div class="panel-body">
                <?php if (count($payments) > 0): ?>
                    <div class="lodge-payment-list">
                        <?php foreach ($payments as $payment): ?>
                            <div class="lodge-payment-item">
                                <div class="lodge-payment-amount"><?php echo number_format($payment->getAmount(), 2); ?></div>
                                <div class="lodge-payment-date"><?php echo $view['date']->toDate($payment->getPaymentDate()); ?> (<?php echo $payment->getYear(); ?>)</div>
                                <div class="lodge-payment-method"><?php echo $payment->getPaymentMethod(); ?></div>
                                <?php if ($payment->isArrears()): ?>
                                    <span class="label label-warning"><?php echo $view['translator']->trans('lodge.subscription.arrears'); ?></span>
                                <?php endif; ?>
                                <div class="mt-xs">
                                    <a href="<?php echo $view['router']->path('lodge_subscription_payment', [
                                        'objectAction' => 'view',
                                        'objectId'     => $payment->getId(),
                                    ]); ?>" class="btn btn-default btn-xs">
                                        <i class="fa fa-eye"></i> <?php echo $view['translator']->trans('mautic.core.details'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php echo $view['translator']->trans('lodge.subscription.payment.none'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate payment link
    document.getElementById('generatePaymentLink').addEventListener('click', function() {
        var contactId = this.getAttribute('data-contact-id');
        var url = '<?php echo $view['router']->path('lodge_subscription_action', ['objectAction' => 'generatePaymentLink', 'objectId' => '__id__']); ?>';
        url = url.replace('__id__', contactId);
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show the payment link in a modal
                    Mautic.showModal('paymentLinkModal', '<div class="text-center"><h3><?php echo $view['translator']->trans('lodge.subscription.payment_link'); ?></h3><div class="well">' + data.payment_link + '</div></div>');
                } else {
                    // Show error
                    Mautic.showNotification('<?php echo $view['translator']->trans('lodge.subscription.payment_link_error'); ?>', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Mautic.showNotification('<?php echo $view['translator']->trans('lodge.subscription.payment_link_error'); ?>', 'error');
            });
    });
    
    // Generate arrears payment link
    if (document.getElementById('generateArrearsPaymentLink')) {
        document.getElementById('generateArrearsPaymentLink').addEventListener('click', function() {
            var contactId = this.getAttribute('data-contact-id');
            var url = '<?php echo $view['router']->path('lodge_subscription_action', ['objectAction' => 'generatePaymentLink', 'objectId' => '__id__']); ?>?type=arrears';
            url = url.replace('__id__', contactId);
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show the payment link in a modal
                        Mautic.showModal('paymentLinkModal', '<div class="text-center"><h3><?php echo $view['translator']->trans('lodge.subscription.arrears_payment_link'); ?></h3><div class="well">' + data.payment_link + '</div></div>');
                    } else {
                        // Show error
                        Mautic.showNotification('<?php echo $view['translator']->trans('lodge.subscription.payment_link_error'); ?>', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Mautic.showNotification('<?php echo $view['translator']->trans('lodge.subscription.payment_link_error'); ?>', 'error');
                });
        });
    }
});
</script> 