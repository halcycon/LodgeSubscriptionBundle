<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Controller\Api;

use Mautic\ApiBundle\Controller\CommonApiController;
use Mautic\LeadBundle\Entity\Lead;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionApiController extends CommonApiController
{
    /**
     * Get subscription information for a contact
     */
    public function getAction(Request $request): JsonResponse
    {
        $contactId = $request->query->get('contact_id');
        
        if (!$contactId) {
            return $this->returnError('Contact ID is required', 400);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $contact = $entityManager->getRepository(Lead::class)->find($contactId);
        
        if (!$contact) {
            return $this->returnError('Contact not found', 404);
        }
        
        try {
            $subscriptionModel = $this->get('lodge_subscription.model.subscription');
            $subscriptionDetails = $subscriptionModel->calculateOutstandingDues($contact);
            
            $paymentModel = $this->get('lodge_subscription.model.payment');
            $payments = $paymentModel->getContactPayments($contactId, 10, 0);
            
            return $this->handleView([
                'contact_id' => $contactId,
                'subscription_details' => $subscriptionDetails,
                'recent_payments' => $payments,
            ]);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), 500);
        }
    }
} 