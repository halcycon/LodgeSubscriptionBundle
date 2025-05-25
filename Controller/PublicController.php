<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\LeadBundle\Entity\Lead;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends CommonController
{
    /**
     * Handle payment callback from Stripe
     */
    public function paymentCallbackAction(Request $request): Response
    {
        // Get the parameters from the request
        $contactId = (int) $request->query->get('contact_id', 0);
        $amount = (float) $request->query->get('amount', 0);
        $isArrears = (bool) $request->query->get('is_arrears', false);
        $year = (int) $request->query->get('year', date('Y'));
        $description = $request->query->get('description', 'Subscription Payment');
        
        // Check if we have a session ID from Stripe (for completed payments)
        $sessionId = $request->query->get('session_id');
        
        // In a real implementation, you would validate the Stripe session
        // For this example, we'll just show a payment confirmation page
        
        // Get the contact
        $entityManager = $this->getDoctrine()->getManager();
        $contact = $entityManager->getRepository(Lead::class)->find($contactId);
        
        if (!$contact) {
            return $this->notFound();
        }
        
        // If we have a session ID, it means the payment was successful
        $paymentSuccess = !empty($sessionId);
        
        // Get subscription details
        $subscriptionModel = $this->get('lodge_subscription.model.subscription');
        $subscriptionDetails = $subscriptionModel->calculateOutstandingDues($contact);
        
        // If payment was successful, record it
        if ($paymentSuccess) {
            $paymentModel = $this->get('lodge_subscription.model.payment');
            
            try {
                $paymentModel->createPayment(
                    $contactId,
                    $amount,
                    $year,
                    'Card', // Stripe payments are via card
                    $isArrears,
                    'Payment via Stripe. Session ID: ' . $sessionId,
                    $sessionId
                );
                
                // Recalculate subscription details after payment
                $subscriptionDetails = $subscriptionModel->calculateOutstandingDues($contact);
            } catch (\Exception $e) {
                // Log the error
                $logger = $this->get('logger');
                $logger->error('Payment recording error: ' . $e->getMessage());
            }
        }
        
        return $this->delegateView([
            'contentTemplate' => 'LodgeSubscriptionBundle:Public:payment_callback.html.php',
            'viewParameters' => [
                'contact' => $contact,
                'amount' => $amount,
                'isArrears' => $isArrears,
                'year' => $year,
                'description' => $description,
                'paymentSuccess' => $paymentSuccess,
                'sessionId' => $sessionId,
                'subscriptionDetails' => $subscriptionDetails,
            ],
            'pagetitle' => $paymentSuccess ? 'Payment Successful' : 'Make Payment',
        ]);
    }
} 