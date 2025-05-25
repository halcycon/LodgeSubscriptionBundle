<?php

/*
 * @copyright   2023 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// This is a public page without Mautic's UI
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title><?php echo $view['translator']->trans('lodge.subscription.payment.success'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        body {
            background-color: #f8f8f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #333;
            padding: 20px;
        }
        .success-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 72px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .success-title {
            font-size: 28px;
            margin-bottom: 20px;
        }
        .success-message {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }
        .payment-details {
            margin: 30px 0;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
            text-align: left;
        }
        .payment-amount {
            font-size: 24px;
            font-weight: bold;
            color: #4e5e9e;
            text-align: center;
            margin: 15px 0;
        }
        .payment-reference {
            font-family: monospace;
            background: #eee;
            padding: 8px;
            border-radius: 4px;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fa fa-check-circle"></i>
            </div>
            
            <div class="success-title">
                <?php echo $view['translator']->trans('lodge.subscription.payment.success'); ?>
            </div>
            
            <div class="success-message">
                <?php echo $view['translator']->trans('lodge.subscription.payment.success_complete'); ?>
            </div>
            
            <div class="payment-details">
                <div class="row">
                    <div class="col-md-6">
                        <strong><?php echo $view['translator']->trans('lodge.subscription.payment.contact'); ?>:</strong>
                        <div><?php echo $contact->getName(); ?></div>
                    </div>
                    <div class="col-md-6 text-right">
                        <strong><?php echo $view['translator']->trans('lodge.subscription.payment.date'); ?>:</strong>
                        <div><?php echo $view['date']->toDate(new \DateTime()); ?></div>
                    </div>
                </div>
                
                <div class="payment-amount">
                    <?php echo number_format($payment->getAmount(), 2); ?>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <strong><?php echo $view['translator']->trans('lodge.subscription.payment.year'); ?>:</strong>
                        <div><?php echo $payment->getYear(); ?></div>
                    </div>
                    <div class="col-md-6 text-right">
                        <strong><?php echo $view['translator']->trans('lodge.subscription.payment.method'); ?>:</strong>
                        <div><?php echo $payment->getPaymentMethod(); ?></div>
                    </div>
                </div>
                
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <strong><?php echo $view['translator']->trans('lodge.subscription.payment.reference'); ?>:</strong>
                        <div class="payment-reference"><?php echo $payment->getTransactionId(); ?></div>
                    </div>
                </div>
            </div>
            
            <p>
                <?php echo $view['translator']->trans('lodge.subscription.payment.confirmation_sent'); ?>
            </p>
            
            <div class="footer">
                <p><?php echo $view['translator']->trans('lodge.subscription.payment.thank_you'); ?></p>
            </div>
        </div>
    </div>
</body>
</html> 