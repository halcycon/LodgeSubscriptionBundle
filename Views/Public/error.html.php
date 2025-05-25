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
    <title><?php echo $view['translator']->trans('lodge.subscription.payment.error'); ?></title>
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
        .error-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .error-icon {
            font-size: 72px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 28px;
            margin-bottom: 20px;
            color: #dc3545;
        }
        .error-message {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }
        .error-details {
            margin: 30px 0;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
            text-align: left;
            border-left: 4px solid #dc3545;
        }
        .error-code {
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
        .retry-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-icon">
                <i class="fa fa-times-circle"></i>
            </div>
            
            <div class="error-title">
                <?php echo $view['translator']->trans('lodge.subscription.payment.error'); ?>
            </div>
            
            <div class="error-message">
                <?php echo $view['translator']->trans('lodge.subscription.payment.error_message'); ?>
            </div>
            
            <?php if (!empty($errorMessage)): ?>
                <div class="error-details">
                    <strong><?php echo $view['translator']->trans('lodge.subscription.payment.error_details'); ?>:</strong>
                    <div class="error-code"><?php echo $errorMessage; ?></div>
                </div>
            <?php endif; ?>
            
            <div class="retry-button">
                <?php if (!empty($token)): ?>
                    <a href="<?php echo $view['router']->path('lodge_subscription_public_payment', ['token' => $token]); ?>" class="btn btn-primary btn-lg">
                        <i class="fa fa-refresh"></i> <?php echo $view['translator']->trans('lodge.subscription.payment.try_again'); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="footer">
                <p><?php echo $view['translator']->trans('lodge.subscription.payment.help_text'); ?></p>
            </div>
        </div>
    </div>
</body>
</html> 