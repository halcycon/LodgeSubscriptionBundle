<?php

return [
    'name'        => 'Lodge Subscription Manager',
    'description' => 'Manages lodge membership subscriptions, payments, and arrears tracking',
    'version'     => '1.0.0',
    'author'      => 'Mautic Community',

    'routes' => [
        'main' => [
            'lodge_subscription_index' => [
                'path'       => '/lodge/subscription/{page}',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\SubscriptionController::indexAction',
                'defaults'   => ['page' => 1],
            ],
            'lodge_subscription_action' => [
                'path'       => '/lodge/subscription/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\SubscriptionController::executeAction',
                'defaults'   => ['objectId' => 0],
            ],
            'lodge_subscription_payment' => [
                'path'       => '/lodge/payment/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\PaymentController::executeAction',
                'defaults'   => ['objectId' => 0],
            ],
            'lodge_subscription_settings' => [
                'path'       => '/lodge/settings',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\SettingsController::indexAction',
            ],
            'lodge_subscription_yearend' => [
                'path'       => '/lodge/yearend',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\YearEndController::indexAction',
            ],
            'lodge_subscription_yearend_execute' => [
                'path'       => '/lodge/yearend/execute',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\YearEndController::processYearEndAction',
            ],
        ],
        'public' => [
            'lodge_subscription_payment_callback' => [
                'path'       => '/lodge/payment/callback',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\PublicController::paymentCallbackAction',
            ],
        ],
        'api' => [
            'lodge_subscription_api' => [
                'path'       => '/lodge/subscription',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\Api\SubscriptionApiController::getAction',
                'method'     => 'GET',
            ],
            'lodge_subscription_webhook' => [
                'path'       => '/lodge/webhook/stripe',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\Api\WebhookController::stripeAction',
                'method'     => 'POST',
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'lodge_subscription' => [
                'route'    => 'lodge_subscription_index',
                'access'   => ['admin'],
                'parent'   => 'mautic.core.contacts',
                'priority' => 80,
                'iconClass' => 'fa-money',
                'checks'   => [
                    'integration' => [
                        'LodgeSubscription' => [
                            'enabled' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],

    'parameters' => [
        'standard_subscription_amount' => 250.00,
        'senior_subscription_amount' => 125.00,
        'stripe_webhook_secret' => '',
        'payment_success_url' => '',
        'payment_cancel_url' => '',
    ],
]; 