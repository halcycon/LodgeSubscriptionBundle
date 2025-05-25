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
$view['slots']->set('mauticContent', 'lodgeSubscriptionYearEnd');
$view['slots']->set('headerTitle', $view['translator']->trans('lodge.subscription.yearend'));

// Include CSS
$view['assets']->addStylesheet('plugins/LodgeSubscriptionBundle/Assets/css/lodgesubscription.css');
?>

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.yearend.process'); ?></h3>
            </div>
            <div class="panel-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h4><?php echo $view['translator']->trans('lodge.subscription.yearend.errors'); ?></h4>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="lodge-yearend-warning">
                        <h4><i class="fa fa-exclamation-triangle"></i> <?php echo $view['translator']->trans('lodge.subscription.yearend.warning_title'); ?></h4>
                        <p><?php echo $view['translator']->trans('lodge.subscription.yearend.warning'); ?></p>
                    </div>
                    
                    <form action="<?php echo $view['router']->path('lodge_subscription_yearend_execute'); ?>" method="post" class="form-horizontal">
                        <?php echo $view['form']->widget($form); ?>
                        
                        <div class="row mt-lg">
                            <div class="col-xs-12 text-center">
                                <button type="submit" class="btn btn-primary" onclick="return confirm('<?php echo $view['translator']->trans('lodge.subscription.yearend.confirm'); ?>');">
                                    <i class="fa fa-check"></i> <?php echo $view['translator']->trans('lodge.subscription.yearend.execute'); ?>
                                </button>
                                <a href="<?php echo $view['router']->path('lodge_subscription_index'); ?>" class="btn btn-default">
                                    <i class="fa fa-times"></i> <?php echo $view['translator']->trans('mautic.core.form.cancel'); ?>
                                </a>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.yearend.statistics'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="row lodge-stats-container">
                    <div class="col-md-3">
                        <div class="lodge-stats-card">
                            <h3><?php echo $view['translator']->trans('lodge.subscription.members_with_dues'); ?></h3>
                            <div class="lodge-stats-value"><?php echo $statistics['members_with_dues']; ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="lodge-stats-card">
                            <h3><?php echo $view['translator']->trans('lodge.subscription.members_paid'); ?></h3>
                            <div class="lodge-stats-value"><?php echo $statistics['members_paid']; ?></div>
                            <div class="lodge-stats-label"><?php echo number_format($statistics['payment_rate'], 1); ?>%</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="lodge-stats-card">
                            <h3><?php echo $view['translator']->trans('lodge.subscription.members_unpaid'); ?></h3>
                            <div class="lodge-stats-value"><?php echo $statistics['members_unpaid']; ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="lodge-stats-card">
                            <h3><?php echo $view['translator']->trans('lodge.subscription.total_outstanding'); ?></h3>
                            <div class="lodge-stats-value"><?php echo number_format($statistics['total_outstanding'], 2); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-md">
                    <p><i class="fa fa-info-circle"></i> <?php echo $view['translator']->trans('lodge.subscription.yearend.statistics_info'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Previous Year-End Logs -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.yearend.logs'); ?></h3>
            </div>
            <div class="panel-body">
                <?php if (count($logs) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo $view['translator']->trans('lodge.subscription.settings.year'); ?></th>
                                    <th><?php echo $view['translator']->trans('lodge.subscription.yearend.processed_date'); ?></th>
                                    <th><?php echo $view['translator']->trans('lodge.subscription.yearend.contacts'); ?></th>
                                    <th><?php echo $view['translator']->trans('mautic.core.actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?php echo $log->getYear(); ?></td>
                                        <td><?php echo $view['date']->toDate($log->getProcessedDate()); ?></td>
                                        <td><?php echo $log->getContactsProcessed(); ?></td>
                                        <td>
                                            <a href="<?php echo $view['router']->path('lodge_subscription_yearend_log', [
                                                'year' => $log->getYear(),
                                            ]); ?>" class="btn btn-default btn-xs">
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
                        <?php echo $view['translator']->trans('lodge.subscription.yearend.no_logs'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Help -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.yearend.help'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <p><i class="fa fa-info-circle"></i> <?php echo $view['translator']->trans('lodge.subscription.yearend.help_text'); ?></p>
                </div>
                
                <h4><?php echo $view['translator']->trans('lodge.subscription.yearend.process_steps'); ?></h4>
                <ol>
                    <li><?php echo $view['translator']->trans('lodge.subscription.yearend.step1'); ?></li>
                    <li><?php echo $view['translator']->trans('lodge.subscription.yearend.step2'); ?></li>
                    <li><?php echo $view['translator']->trans('lodge.subscription.yearend.step3'); ?></li>
                    <li><?php echo $view['translator']->trans('lodge.subscription.yearend.step4'); ?></li>
                </ol>
            </div>
        </div>
    </div>
</div> 