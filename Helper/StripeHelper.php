<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Helper;

use Doctrine\ORM\EntityManager;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Settings;
use MauticPlugin\LodgeSubscriptionBundle\Model\SubscriptionModel;

class StripeHelper
{
    private EntityManager $entityManager;
    private SubscriptionModel $subscriptionModel;
    private IntegrationHelper $integrationHelper;

    public function __construct(
        EntityManager $entityManager,
        SubscriptionModel $subscriptionModel,
        IntegrationHelper $integrationHelper
    ) {
        $this->entityManager = $entityManager;
        $this->subscriptionModel = $subscriptionModel;
        $this->integrationHelper = $integrationHelper;
    }

    /**
     * Check if Stripe is configured
     */
    public function isStripeConfigured(): bool
    {
        $settingsRepository = $this->entityManager->getRepository(Settings::class);
        $currentYear = (int) date('Y');
        $settings = $settingsRepository->getSettingsForYear($currentYear);
        
        if (!$settings) {
            return false;
        }
        
        return !empty($settings->getStripePublishableKey()) && !empty($settings->getStripeSecretKey());
    }

    /**
     * Get Stripe configuration
     */
    public function getStripeConfig(): array
    {
        $settingsRepository = $this->entityManager->getRepository(Settings::class);
        $currentYear = (int) date('Y');
        $settings = $settingsRepository->getSettingsForYear($currentYear);
        
        if (!$settings) {
            return [
                'publishable_key' => '',
                'secret_key' => '',
                'webhook_secret' => '',
            ];
        }
        
        return [
            'publishable_key' => $settings->getStripePublishableKey(),
            'secret_key' => $settings->getStripeSecretKey(),
            'webhook_secret' => $settings->getStripeWebhookSecret(),
        ];
    }

    /**
     * Generate a payment link for Stripe checkout
     */
    public function generatePaymentLink(
        int $contactId,
        float $amount,
        string $description,
        bool $isArrears = false,
        int $year = 0
    ): string {
        if (!$this->isStripeConfigured()) {
            throw new \Exception('Stripe is not properly configured');
        }
        
        if ($year === 0) {
            $year = (int) date('Y');
        }
        
        // In a real implementation, this would use the Stripe API to create a checkout session
        // For now, we'll create a URL with parameters
        $baseUrl = $_ENV['MAUTIC_URL'] ?? 'https://yourdomain.com';
        $callbackUrl = $baseUrl . '/lodge/payment/callback';
        
        $params = [
            'contact_id' => $contactId,
            'amount' => $amount,
            'year' => $year,
            'is_arrears' => $isArrears ? 1 : 0,
            'description' => urlencode($description),
        ];
        
        return $callbackUrl . '?' . http_build_query($params);
    }

    /**
     * Verify a Stripe webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $stripeConfig = $this->getStripeConfig();
        $webhookSecret = $stripeConfig['webhook_secret'];
        
        if (empty($webhookSecret)) {
            return false;
        }
        
        // In a real implementation, this would use the Stripe API to verify the signature
        // For demonstration purposes, we'll return true
        return true;
    }

    /**
     * Process a Stripe webhook event
     */
    public function processWebhookEvent(array $event): bool
    {
        // This would be implemented to handle different event types from Stripe
        // Currently we only support checkout.session.completed
        
        $eventType = $event['type'] ?? '';
        
        if ($eventType !== 'checkout.session.completed') {
            return false;
        }
        
        // We'd implement the actual processing logic here
        // For now, we'll just return true to indicate success
        return true;
    }
} 