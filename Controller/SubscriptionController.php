<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractFormController;
use Mautic\LeadBundle\Entity\Lead;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends AbstractFormController
{
    /**
     * List subscriptions
     */
    public function indexAction(int $page = 1): Response
    {
        // Set the page we came from
        $this->get('session')->set('mautic.lodgesubscription.page', $page);

        $limit = $this->get('mautic.helper.core_parameters')->get('default_pagelimit');
        
        // Get subscription statistics for the current year
        $currentYear = (int) date('Y');
        $yearEndModel = $this->get('lodge_subscription.model.yearend');
        $statistics = $yearEndModel->getYearEndStatistics($currentYear);
        
        // Get subscription settings
        $entityManager = $this->getDoctrine()->getManager();
        $settingsRepository = $entityManager->getRepository(\MauticPlugin\LodgeSubscriptionBundle\Entity\Settings::class);
        $settings = $settingsRepository->getSettingsForYear($currentYear);
        
        // Get recent payments
        $paymentModel = $this->get('lodge_subscription.model.payment');
        $payments = $paymentModel->getPayments([], 1, 10);
        
        return $this->delegateView([
            'contentTemplate' => 'LodgeSubscriptionBundle:Subscription:index.html.php',
            'viewParameters' => [
                'statistics' => $statistics,
                'settings' => $settings,
                'payments' => $payments['results'],
                'currentYear' => $currentYear,
                'page' => $page,
                'limit' => $limit,
            ],
            'pagetitle' => 'Lodge Subscription Manager',
        ]);
    }

    /**
     * View a member's subscription details
     */
    public function viewAction(int $objectId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contact = $entityManager->getRepository(Lead::class)->find($objectId);
        
        if (!$contact) {
            return $this->notFound();
        }
        
        // Calculate subscription details
        $subscriptionModel = $this->get('lodge_subscription.model.subscription');
        $subscriptionDetails = $subscriptionModel->calculateOutstandingDues($contact);
        
        // Get payment history
        $paymentModel = $this->get('lodge_subscription.model.payment');
        $payments = $paymentModel->getContactPayments($objectId, 100, 0);
        
        return $this->delegateView([
            'contentTemplate' => 'LodgeSubscriptionBundle:Subscription:view.html.php',
            'viewParameters' => [
                'contact' => $contact,
                'subscriptionDetails' => $subscriptionDetails,
                'payments' => $payments,
            ],
            'pagetitle' => 'Subscription Details: ' . $contact->getName(),
        ]);
    }

    /**
     * Generate a payment link for a member
     */
    public function generatePaymentLinkAction(int $objectId): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contact = $entityManager->getRepository(Lead::class)->find($objectId);
        
        if (!$contact) {
            return $this->sendJsonResponse(['success' => false, 'message' => 'Contact not found']);
        }
        
        try {
            $subscriptionModel = $this->get('lodge_subscription.model.subscription');
            $subscriptionDetails = $subscriptionModel->calculateOutstandingDues($contact);
            
            $amount = $subscriptionDetails['amount_owed_current'];
            $isArrears = false;
            
            // Check if this is for arrears instead
            if (isset($_GET['type']) && $_GET['type'] === 'arrears') {
                $amount = $subscriptionDetails['amount_owed_arrears'];
                $isArrears = true;
            }
            
            // Generate a Stripe payment link
            $stripeHelper = $this->get('lodge_subscription.helper.stripe');
            
            if (!$stripeHelper->isStripeConfigured()) {
                return $this->sendJsonResponse(['success' => false, 'message' => 'Stripe is not configured']);
            }
            
            $description = $isArrears
                ? 'Lodge Subscription Arrears Payment'
                : 'Lodge Subscription ' . date('Y');
                
            $paymentLink = $stripeHelper->generatePaymentLink(
                $objectId,
                $amount,
                $description,
                $isArrears
            );
            
            return $this->sendJsonResponse([
                'success' => true,
                'payment_link' => $paymentLink,
            ]);
        } catch (\Exception $e) {
            return $this->sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Execute action for AJAX modal.
     */
    public function executeAction($objectAction, $objectId = 0, $objectSubId = 0, $objectModel = '')
    {
        if ($objectAction === 'view' && $objectId) {
            return $this->viewAction((int) $objectId);
        }
        
        if ($objectAction === 'generatePaymentLink' && $objectId) {
            return $this->generatePaymentLinkAction((int) $objectId);
        }
        
        return $this->notFound();
    }
} 