<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Controller\Api;

use Mautic\CoreBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends CommonController
{
    /**
     * Stripe webhook endpoint
     */
    public function stripeAction(Request $request): Response
    {
        // Get the request body and signature header
        $payload = $request->getContent();
        $signatureHeader = $request->headers->get('Stripe-Signature');
        
        // Get the Stripe helper service
        $stripeHelper = $this->get('lodge_subscription.helper.stripe');
        $paymentModel = $this->get('lodge_subscription.model.payment');
        
        try {
            // Verify the webhook signature
            if (!$stripeHelper->verifyWebhookSignature($payload, $signatureHeader)) {
                return new JsonResponse(['success' => false, 'message' => 'Invalid signature'], 400);
            }
            
            // Parse the event
            $event = json_decode($payload, true);
            if (empty($event)) {
                return new JsonResponse(['success' => false, 'message' => 'Invalid payload'], 400);
            }
            
            // Process the webhook event
            $success = $paymentModel->handleStripeWebhook($event);
            
            if ($success) {
                return new JsonResponse(['success' => true]);
            } else {
                return new JsonResponse(['success' => false, 'message' => 'Failed to process event'], 500);
            }
        } catch (\Exception $e) {
            // Log the error
            $logger = $this->get('logger');
            $logger->error('Stripe webhook error: ' . $e->getMessage());
            
            return new JsonResponse(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }
} 