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
$view['slots']->set('headerTitle', $view['translator']->trans('mautic.core.form.delete').' '.$view['translator']->trans('lodge.subscription.settings'));
?>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('mautic.core.form.delete').' '.$view['translator']->trans('lodge.subscription.settings'); ?></h3>
            </div>
            <div class="panel-body">
                <p><?php echo $view['translator']->trans('lodge.subscription.settings.delete_message', ['%year%' => $settings->getYear()]); ?></p>
                <p class="text-danger"><strong><?php echo $view['translator']->trans('mautic.core.form.delete.warning'); ?></strong></p>
                
                <h4><?php echo $view['translator']->trans('lodge.subscription.settings.details'); ?></h4>
                <dl class="dl-horizontal">
                    <dt><?php echo $view['translator']->trans('lodge.subscription.settings.year'); ?></dt>
                    <dd><?php echo $settings->getYear(); ?></dd>
                    
                    <dt><?php echo $view['translator']->trans('lodge.subscription.settings.amount_full'); ?></dt>
                    <dd><?php echo number_format($settings->getAmountFull(), 2); ?></dd>
                    
                    <dt><?php echo $view['translator']->trans('lodge.subscription.settings.amount_reduced'); ?></dt>
                    <dd><?php echo number_format($settings->getAmountReduced(), 2); ?></dd>
                    
                    <dt><?php echo $view['translator']->trans('lodge.subscription.settings.amount_honorary'); ?></dt>
                    <dd><?php echo number_format($settings->getAmountHonorary(), 2); ?></dd>
                </dl>
            </div>
            <div class="panel-footer">
                <form action="<?php echo $view['router']->path('lodge_subscription_settings_delete', ['year' => $settings->getYear()]); ?>" method="post">
                    <input type="hidden" name="form[year]" value="<?php echo $settings->getYear(); ?>" />
                    <button type="submit" class="btn btn-danger"><?php echo $view['translator']->trans('mautic.core.form.delete'); ?></button>
                    <a href="<?php echo $view['router']->path('lodge_subscription_settings'); ?>" class="btn btn-default"><?php echo $view['translator']->trans('mautic.core.form.cancel'); ?></a>
                </form>
            </div>
        </div>
    </div>
</div> 