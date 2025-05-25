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
$view['slots']->set('headerTitle', $view['translator']->trans('lodge.subscription'));

// Include CSS
$view['assets']->addStylesheet('plugins/LodgeSubscriptionBundle/Assets/css/lodgesubscription.css');
?>

<!-- Statistics Overview -->
<div class="row lodge-stats-container">
    <div class="col-md-3">
        <div class="lodge-stats-card">
            <h3><?php echo $view['translator']->trans('lodge.subscription.members_with_dues'); ?></h3>
            <div class="lodge-stats-value"><?php echo $statistics['members_with_dues']; ?></div>
            <div class="lodge-stats-label"><?php echo $view['translator']->trans('lodge.subscription.current_year'); ?>: <?php echo $currentYear; ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lodge-stats-card">
            <h3><?php echo $view['translator']->trans('lodge.subscription.members_paid'); ?></h3>
            <div class="lodge-stats-value"><?php echo $statistics['members_paid']; ?></div>
            <div class="lodge-stats-label"><?php echo $view['translator']->trans('lodge.subscription.payment_rate'); ?>: <?php echo number_format($statistics['payment_rate'], 1); ?>%</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lodge-stats-card">
            <h3><?php echo $view['translator']->trans('lodge.subscription.total_collected'); ?></h3>
            <div class="lodge-stats-value"><?php echo number_format($statistics['total_collected'], 2); ?></div>
            <div class="lodge-stats-label"><?php echo $view['translator']->trans('lodge.subscription.payment.amount'); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lodge-stats-card">
            <h3><?php echo $view['translator']->trans('lodge.subscription.total_outstanding'); ?></h3>
            <div class="lodge-stats-value"><?php echo number_format($statistics['total_outstanding'], 2); ?></div>
            <div class="lodge-stats-label"><?php echo $view['translator']->trans('lodge.subscription.remaining'); ?></div>
        </div>
    </div>
</div>

<!-- Subscription Settings -->
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.settings'); ?></h3>
    </div>
    <div class="panel-body">
        <?php if ($settings): ?>
            <div class="row">
                <div class="col-md-4">
                    <strong><?php echo $view['translator']->trans('lodge.subscription.settings.year'); ?>:</strong> <?php echo $settings->getYear(); ?>
                </div>
                <div class="col-md-4">
                    <strong><?php echo $view['translator']->trans('lodge.subscription.settings.amount_full'); ?>:</strong> <?php echo number_format($settings->getAmountFull(), 2); ?>
                </div>
                <div class="col-md-4">
                    <strong><?php echo $view['translator']->trans('lodge.subscription.settings.amount_reduced'); ?>:</strong> <?php echo number_format($settings->getAmountReduced(), 2); ?>
                </div>
            </div>
            <div class="mt-lg">
                <a href="<?php echo $view['router']->path('lodge_subscription_settings'); ?>" class="btn btn-default">
                    <i class="fa fa-cog"></i> <?php echo $view['translator']->trans('lodge.subscription.settings'); ?>
                </a>
                <a href="<?php echo $view['router']->path('lodge_subscription_yearend'); ?>" class="btn btn-default">
                    <i class="fa fa-calendar-check-o"></i> <?php echo $view['translator']->trans('lodge.subscription.yearend'); ?>
                </a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <?php echo $view['translator']->trans('lodge.subscription.settings.missing'); ?>
            </div>
            <a href="<?php echo $view['router']->path('lodge_subscription_settings'); ?>" class="btn btn-primary">
                <i class="fa fa-plus"></i> <?php echo $view['translator']->trans('lodge.subscription.settings.create'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Payments -->
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.payment.recent'); ?></h3>
    </div>
    <div class="panel-body">
        <?php if (count($payments) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered lodge-payment-list">
                    <thead>
                        <tr>
                            <th><?php echo $view['translator']->trans('lodge.subscription.payment.contact'); ?></th>
                            <th><?php echo $view['translator']->trans('lodge.subscription.payment.amount'); ?></th>
                            <th><?php echo $view['translator']->trans('lodge.subscription.payment.date'); ?></th>
                            <th><?php echo $view['translator']->trans('lodge.subscription.payment.method'); ?></th>
                            <th><?php echo $view['translator']->trans('lodge.subscription.payment.year'); ?></th>
                            <th><?php echo $view['translator']->trans('mautic.core.actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td>
                                    <?php $contact = $view['model']->getEntity($payment->getContactId(), 'lead'); ?>
                                    <?php if ($contact): ?>
                                        <a href="<?php echo $view['router']->path('mautic_contact_action', ['objectAction' => 'view', 'objectId' => $payment->getContactId()]); ?>">
                                            <?php echo $contact->getName(); ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo $view['translator']->trans('mautic.core.unknown'); ?> (ID: <?php echo $payment->getContactId(); ?>)
                                    <?php endif; ?>
                                </td>
                                <td class="lodge-payment-amount"><?php echo number_format($payment->getAmount(), 2); ?></td>
                                <td class="lodge-payment-date"><?php echo $view['date']->toFull($payment->getPaymentDate()); ?></td>
                                <td><?php echo $payment->getPaymentMethod(); ?></td>
                                <td><?php echo $payment->getYear(); ?> <?php echo $payment->isArrears() ? '(Arrears)' : ''; ?></td>
                                <td>
                                    <a href="<?php echo $view['router']->path('lodge_subscription_payment', ['objectAction' => 'view', 'objectId' => $payment->getId()]); ?>" class="btn btn-default btn-xs">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <?php echo $view['translator']->trans('lodge.subscription.payment.none'); ?>
            </div>
        <?php endif; ?>
    </div>
</div> 