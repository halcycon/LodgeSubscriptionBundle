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
            ],
            'lodge_subscription_action' => [
                'path'       => '/lodge/subscription/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\SubscriptionController::executeAction',
            ],
            'lodge_subscription_payment' => [
                'path'       => '/lodge/payment/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\LodgeSubscriptionBundle\Controller\PaymentController::executeAction',
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

    'services' => [
        'integrations' => [
            'mautic.integration.lodgesubscription' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Integration\LodgeSubscriptionIntegration::class,
                'tags'      => [
                    'mautic.integration',
                    'mautic.basic_integration',
                    'mautic.config_integration',
                ],
            ],
            'lodge_subscription.integration.stripe' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Integration\StripeIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session.factory',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                ],
            ],
        ],
        'events' => [
            'lodge_subscription.field.subscriber' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'lodge_subscription.model.subscription',
                ],
            ],
        ],
        'forms' => [
            'lodge_subscription.form.type.payment' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Form\Type\PaymentType::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
            'lodge_subscription.form.type.subscription_settings' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Form\Type\SubscriptionSettingsType::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
            'lodge_subscription.form.type.yearend' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Form\Type\YearEndType::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
            'lodge_subscription.form.type.config' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Form\Type\ConfigType::class,
            ],
            'lodge_subscription.form.type.config_integration' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Form\Type\ConfigIntegrationType::class,
            ],
        ],
        'models' => [
            'lodge_subscription.model.subscription' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Model\SubscriptionModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'mautic.lead.model.field',
                ],
            ],
            'lodge_subscription.model.payment' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Model\PaymentModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'lodge_subscription.model.subscription',
                ],
            ],
            'lodge_subscription.model.yearend' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Model\YearEndModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'lodge_subscription.model.subscription',
                    'mautic.lead.model.field',
                ],
            ],
        ],
        'other' => [
            'lodge_subscription.helper.stripe' => [
                'class'     => MauticPlugin\LodgeSubscriptionBundle\Helper\StripeHelper::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'lodge_subscription.model.subscription',
                    'mautic.helper.integration',
                ],
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'lodge_subscription' => [
                'route'    => 'lodge_subscription_index',
                'access'   => 'admin',
                'parent'   => 'mautic.core.contacts',
                'priority' => 80,
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
    
    'config' => [
        'form' => [
            'formTheme' => 'LodgeSubscriptionBundle:FormTheme\Config',
            'formType' => MauticPlugin\LodgeSubscriptionBundle\Form\Type\ConfigType::class,
        ],
    ],
]; 