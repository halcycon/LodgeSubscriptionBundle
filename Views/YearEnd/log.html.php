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
$view['slots']->set('mauticContent', 'lodgeSubscriptionYearEndLog');
$view['slots']->set('headerTitle', $view['translator']->trans('lodge.subscription.yearend.log_details') . ' ' . $yearEndLog->getYear());

// Include CSS
$view['assets']->addStylesheet('plugins/LodgeSubscriptionBundle/Assets/css/lodgesubscription.css');
?>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php echo $view['translator']->trans('lodge.subscription.yearend.log_details'); ?> <?php echo $yearEndLog->getYear(); ?>
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.settings.year'); ?></label>
                            <div class="value">
                                <?php echo $yearEndLog->getYear(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.yearend.processed_date'); ?></label>
                            <div class="value">
                                <?php echo $view['date']->toFull($yearEndLog->getProcessedDate()); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.yearend.contacts'); ?></label>
                            <div class="value">
                                <?php echo $yearEndLog->getContactsProcessed(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.yearend.processed_by'); ?></label>
                            <div class="value">
                                <?php echo $yearEndLog->getProcessedBy(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.yearend.dues_moved'); ?></label>
                            <div class="value">
                                <?php echo number_format($yearEndLog->getDuesMoved(), 2); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.yearend.members_with_arrears'); ?></label>
                            <div class="value">
                                <?php echo $yearEndLog->getMembersWithArrears(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($yearEndLog->getNotes()): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo $view['translator']->trans('lodge.subscription.payment.notes'); ?></label>
                            <div class="value">
                                <?php echo nl2br($view['formatter']->_($yearEndLog->getNotes())); ?>
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
                                    '%name%' => $yearEndLog->getCreatedByUser(),
                                    '%date%' => $view['date']->toFull($yearEndLog->getDateAdded()),
                                ]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <a href="<?php echo $view['router']->path('lodge_subscription_yearend'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> <?php echo $view['translator']->trans('mautic.core.form.back'); ?>
                </a>
            </div>
        </div>
    </div>
</div> 