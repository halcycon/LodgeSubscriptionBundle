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
$view['slots']->set('headerTitle', $view['translator']->trans('lodge.subscription.payment.new'));

// Include CSS
$view['assets']->addStylesheet('plugins/LodgeSubscriptionBundle/Assets/css/lodgesubscription.css');
?>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $view['translator']->trans('lodge.subscription.payment.new'); ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $view['form']->start($form); ?>
                <div class="row">
                    <div class="col-md-6">
                        <?php echo $view['form']->row($form['contactId']); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $view['form']->row($form['contactName']); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <?php echo $view['form']->row($form['amount']); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $view['form']->row($form['paymentDate']); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <?php echo $view['form']->row($form['paymentMethod']); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $view['form']->row($form['year']); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <?php echo $view['form']->row($form['isArrears']); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $view['form']->row($form['transactionId']); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo $view['form']->row($form['notes']); ?>
                    </div>
                </div>

                <div class="row mt-lg">
                    <div class="col-xs-12 text-center">
                        <?php echo $view['form']->widget($form['buttons']['save']); ?>
                        <?php echo $view['form']->widget($form['buttons']['cancel']); ?>
                    </div>
                </div>
                <?php echo $view['form']->end($form); ?>
            </div>
        </div>
    </div>
</div> 