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
$view['slots']->set('headerTitle', $view['translator']->trans('lodge.subscription.payment') . ' #' . $payment->getId());

// Include CSS
$view['assets']->addStylesheet('plugins/LodgeSubscriptionBundle/Assets/css/lodgesubscription.css');
?>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php echo $view['translator']->trans('lodge.subscription.payment'); ?> #<?php echo $payment->getId(); ?>
                </h3>
                <div class="panel-toolbar">
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                            <i class="fa fa-cog"></i> <?php echo $view['translator']->trans('mautic.core.actions'); ?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a href="<?php echo $view['router']->path('lodge_subscription_payment', [
                                    'objectAction' => 'delete',
                                    'objectId'     => $payment->getId(),
                                ]); ?>" data-toggle="confirmation" data-message="<?php echo $view['translator']->trans('lodge.subscription.payment.delete_confirm'); ?>">
                                    <i class="fa fa-trash-o text-danger"></i> <?php echo $view['translator']->trans('mautic.core.form.delete'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $view['router']->path('lodge_subscription_action', [
                                    'objectAction' => 'view',
                                    'objectId'     => $payment->getContactId(),
                                ]); ?>">
                                    <i class="fa fa-user"></i> <?php echo $view['translator']->trans('lodge.subscription.view'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.payment.contact'); ?></label>
                            <div class="value">
                                <?php if ($contact): ?>
                                    <a href="<?php echo $view['router']->path('mautic_contact_action', [
                                        'objectAction' => 'view',
                                        'objectId'     => $contact->getId(),
                                    ]); ?>">
                                        <?php echo $contact->getName(); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo $view['translator']->trans('mautic.core.unknown'); ?> (ID: <?php echo $payment->getContactId(); ?>)
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.payment.amount'); ?></label>
                            <div class="value lodge-payment-amount">
                                <?php echo number_format($payment->getAmount(), 2); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.payment.date'); ?></label>
                            <div class="value">
                                <?php echo $view['date']->toFull($payment->getPaymentDate()); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.payment.method'); ?></label>
                            <div class="value">
                                <?php echo $payment->getPaymentMethod(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.payment.year'); ?></label>
                            <div class="value">
                                <?php echo $payment->getYear(); ?>
                                <?php if ($payment->isArrears()): ?>
                                    <span class="label label-warning"><?php echo $view['translator']->trans('lodge.subscription.arrears'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.payment.transaction_id'); ?></label>
                            <div class="value">
                                <?php echo $payment->getTransactionId() ?: $view['translator']->trans('mautic.core.none'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($payment->getNotes()): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.payment.notes'); ?></label>
                            <div class="value">
                                <?php echo nl2br($view['formatter']->_($payment->getNotes())); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('mautic.core.info'); ?></label>
                            <div class="value">
                                <?php echo $view['translator']->trans('mautic.core.created.by', [
                                    '%name%' => $payment->getCreatedByUser(),
                                    '%date%' => $view['date']->toFull($payment->getDateAdded()),
                                ]); ?>
                                <?php if ($payment->getDateModified()): ?>
                                <br>
                                <?php echo $view['translator']->trans('mautic.core.updated.by', [
                                    '%name%' => $payment->getModifiedByUser(),
                                    '%date%' => $view['date']->toFull($payment->getDateModified()),
                                ]); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 