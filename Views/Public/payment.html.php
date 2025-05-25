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
    <title><?php echo $view['translator']->trans('lodge.subscription.payment.online'); ?></title>
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
        .payment-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .payment-logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .payment-details {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .payment-amount {
            font-size: 24px;
            font-weight: bold;
            color: #4e5e9e;
        }
        .payment-description {
            margin-top: 10px;
            color: #666;
        }
        .payment-button {
            text-align: center;
            margin-top: 30px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
        }
        .success-message {
            text-align: center;
            color: #28a745;
            font-size: 24px;
            margin: 30px 0;
        }
        .error-message {
            text-align: center;
            color: #dc3545;
            font-size: 18px;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-container">
            <div class="payment-header">
                <h1><?php echo $view['translator']->trans('lodge.subscription.payment.online'); ?></h1>
                <p><?php echo $view['translator']->trans('lodge.subscription.payment.secure_payment'); ?></p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fa fa-times-circle"></i> <?php echo $error; ?>
                </div>
                <div class="text-center">
                    <a href="<?php echo $view['router']->path('lodge_subscription_public_payment', ['token' => $token]); ?>" class="btn btn-primary">
                        <i class="fa fa-refresh"></i> <?php echo $view['translator']->trans('lodge.subscription.payment.try_again'); ?>
                    </a>
                </div>
            <?php elseif ($success): ?>
                <div class="success-message">
                    <i class="fa fa-check-circle"></i> <?php echo $view['translator']->trans('lodge.subscription.payment.success'); ?>
                </div>
                <div class="payment-details">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><?php echo $view['translator']->trans('lodge.subscription.payment.reference'); ?>:</strong> <?php echo $transactionId; ?>
                        </div>
                        <div class="col-md-6 text-right">
                            <strong><?php echo $view['translator']->trans('lodge.subscription.payment.date'); ?>:</strong> <?php echo $view['date']->toDate(new \DateTime()); ?>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-center">
                            <div class="payment-amount"><?php echo number_format($amount, 2); ?></div>
                            <div class="payment-description"><?php echo $paymentDescription; ?></div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <p><?php echo $view['translator']->trans('lodge.subscription.payment.success_message'); ?></p>
                </div>
            <?php else: ?>
                <div class="payment-details">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><?php echo $view['translator']->trans('lodge.subscription.payment.contact'); ?>:</strong> <?php echo $contact->getName(); ?>
                        </div>
                        <div class="col-md-6 text-right">
                            <strong><?php echo $view['translator']->trans('lodge.subscription.payment.date'); ?>:</strong> <?php echo $view['date']->toDate(new \DateTime()); ?>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-center">
                            <div class="payment-amount"><?php echo number_format($amount, 2); ?></div>
                            <div class="payment-description"><?php echo $paymentDescription; ?></div>
                        </div>
                    </div>
                </div>
                
                <div id="payment-form">
                    <div id="card-element">
                        <!-- Stripe Elements will be inserted here -->
                    </div>
                    <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                    
                    <div class="payment-button">
                        <button id="submit-payment" class="btn btn-primary btn-lg">
                            <i class="fa fa-credit-card"></i> <?php echo $view['translator']->trans('lodge.subscription.payment.pay_now'); ?>
                        </button>
                    </div>
                </div>
                
                <div class="footer">
                    <p><?php echo $view['translator']->trans('lodge.subscription.payment.secure_notice'); ?></p>
                    <p><small><?php echo $view['translator']->trans('lodge.subscription.payment.questions'); ?></small></p>
                </div>
                
                <script src="https://js.stripe.com/v3/"></script>
                <script>
                    // Set up Stripe
                    var stripe = Stripe('<?php echo $stripePublishableKey; ?>');
                    var elements = stripe.elements();
                    
                    // Create card element
                    var card = elements.create('card', {
                        style: {
                            base: {
                                color: '#32325d',
                                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                                fontSmoothing: 'antialiased',
                                fontSize: '16px',
                                '::placeholder': {
                                    color: '#aab7c4'
                                }
                            },
                            invalid: {
                                color: '#fa755a',
                                iconColor: '#fa755a'
                            }
                        }
                    });
                    
                    // Mount the card element
                    card.mount('#card-element');
                    
                    // Handle validation errors
                    card.on('change', function(event) {
                        var displayError = document.getElementById('card-errors');
                        if (event.error) {
                            displayError.textContent = event.error.message;
                        } else {
                            displayError.textContent = '';
                        }
                    });
                    
                    // Handle form submission
                    var form = document.getElementById('payment-form');
                    var submitButton = document.getElementById('submit-payment');
                    
                    submitButton.addEventListener('click', function(ev) {
                        ev.preventDefault();
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
                        
                        stripe.createToken(card).then(function(result) {
                            if (result.error) {
                                // Show error to your customer
                                var errorElement = document.getElementById('card-errors');
                                errorElement.textContent = result.error.message;
                                submitButton.disabled = false;
                                submitButton.innerHTML = '<i class="fa fa-credit-card"></i> <?php echo $view['translator']->trans('lodge.subscription.payment.pay_now'); ?>';
                            } else {
                                // Send the token to your server
                                stripeTokenHandler(result.token);
                            }
                        });
                    });
                    
                    function stripeTokenHandler(token) {
                        // Create a form to submit the token
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?php echo $view['router']->path('lodge_subscription_public_payment_process', ['token' => $token]); ?>';
                        
                        var hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'stripeToken');
                        hiddenInput.setAttribute('value', token.id);
                        
                        form.appendChild(hiddenInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                </script>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 