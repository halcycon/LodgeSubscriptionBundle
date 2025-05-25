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
$view['slots']->set('mauticContent', 'lodgeSubscriptionSettings');
$view['slots']->set('headerTitle', $view['translator']->trans('lodge.subscription.settings'));

// Include CSS
$view['assets']->addStylesheet('plugins/LodgeSubscriptionBundle/Assets/css/lodgesubscription.css');
?>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.settings.subscription_rates'); ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $view['form']->start($form); ?>
                
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $view['form']->row($form['year']); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $view['form']->row($form['amountFull']); ?>
                    </div>
                    <div class="col-md-4">
                        <?php echo $view['form']->row($form['amountReduced']); ?>
                    </div>
                    <div class="col-md-4">
                        <?php echo $view['form']->row($form['amountHonorary']); ?>
                    </div>
                </div>
                
                <h4 class="mt-lg"><?php echo $view['translator']->trans('lodge.subscription.settings.stripe'); ?></h4>
                
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $view['form']->row($form['stripePublishableKey']); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $view['form']->row($form['stripeSecretKey']); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $view['form']->row($form['stripeWebhookSecret']); ?>
                    </div>
                </div>
                
                <div class="row mt-lg">
                    <div class="col-xs-12">
                        <?php echo $view['form']->widget($form['buttons']['save']); ?>
                        <?php echo $view['form']->widget($form['buttons']['cancel']); ?>
                    </div>
                </div>
                
                <?php echo $view['form']->end($form); ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.settings.existing'); ?></h3>
            </div>
            <div class="panel-body">
                <?php if (count($allSettings) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo $view['translator']->trans('lodge.subscription.settings.year'); ?></th>
                                    <th><?php echo $view['translator']->trans('lodge.subscription.settings.amount_full'); ?></th>
                                    <th><?php echo $view['translator']->trans('lodge.subscription.settings.amount_reduced'); ?></th>
                                    <th><?php echo $view['translator']->trans('lodge.subscription.settings.amount_honorary'); ?></th>
                                    <th><?php echo $view['translator']->trans('mautic.core.actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allSettings as $settings): ?>
                                    <tr>
                                        <td><?php echo $settings->getYear(); ?></td>
                                        <td><?php echo number_format($settings->getAmountFull(), 2); ?></td>
                                        <td><?php echo number_format($settings->getAmountReduced(), 2); ?></td>
                                        <td><?php echo number_format($settings->getAmountHonorary(), 2); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo $view['router']->path('lodge_subscription_settings_edit', [
                                                    'year' => $settings->getYear(),
                                                ]); ?>" class="btn btn-default btn-xs">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </a>
                                                <?php if ($settings->getYear() != date('Y')): ?>
                                                    <a href="<?php echo $view['router']->path('lodge_subscription_settings_delete', [
                                                        'year' => $settings->getYear(),
                                                    ]); ?>" class="btn btn-danger btn-xs" data-toggle="confirmation" 
                                                    data-message="<?php echo $view['translator']->trans('lodge.subscription.settings.delete_confirmation', ['%year%' => $settings->getYear()]); ?>">
                                                        <i class="fa fa-trash-o"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php echo $view['translator']->trans('lodge.subscription.settings.none'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.settings.help'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <p><i class="fa fa-info-circle"></i> <?php echo $view['translator']->trans('lodge.subscription.settings.help_text'); ?></p>
                </div>
                
                <h4><?php echo $view['translator']->trans('lodge.subscription.settings.subscription_types'); ?></h4>
                <ul>
                    <li><strong><?php echo $view['translator']->trans('lodge.subscription.settings.full'); ?></strong>: <?php echo $view['translator']->trans('lodge.subscription.settings.full_description'); ?></li>
                    <li><strong><?php echo $view['translator']->trans('lodge.subscription.settings.reduced'); ?></strong>: <?php echo $view['translator']->trans('lodge.subscription.settings.reduced_description'); ?></li>
                    <li><strong><?php echo $view['translator']->trans('lodge.subscription.settings.honorary'); ?></strong>: <?php echo $view['translator']->trans('lodge.subscription.settings.honorary_description'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div> 