<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Model;

use Doctrine\ORM\EntityManager;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Payment;

class PaymentModel
{
    private EntityManager $entityManager;
    private SubscriptionModel $subscriptionModel;

    public function __construct(
        EntityManager $entityManager,
        SubscriptionModel $subscriptionModel
    ) {
        $this->entityManager = $entityManager;
        $this->subscriptionModel = $subscriptionModel;
    }

    /**
     * Get a list of payments for a contact
     */
    public function getContactPayments(int $contactId, int $limit = 10, int $offset = 0): array
    {
        $repository = $this->entityManager->getRepository(Payment::class);
        
        return $repository->getContactPayments($contactId, $limit, $offset);
    }

    /**
     * Get payments for a specific year
     */
    public function getYearPayments(int $year): array
    {
        $repository = $this->entityManager->getRepository(Payment::class);
        
        return $repository->getYearPayments($year);
    }

    /**
     * Get all payments with filtering and pagination
     */
    public function getPayments(array $filters = [], int $page = 1, int $limit = 30): array
    {
        $repository = $this->entityManager->getRepository(Payment::class);
        $paginator = $repository->getPaymentList($filters, $page, $limit);
        
        $count = count($paginator);
        $totalPages = ceil($count / $limit);
        
        return [
            'total' => $count,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => $totalPages,
            'results' => iterator_to_array($paginator->getIterator()),
        ];
    }

    /**
     * Create a new payment
     */
    public function createPayment(
        int $contactId,
        float $amount,
        int $year,
        string $paymentMethod,
        bool $isArrears = false,
        ?string $notes = null,
        ?string $transactionId = null
    ): Payment {
        // Use the subscription model to record the payment and update contact fields
        $payment = $this->subscriptionModel->recordPayment(
            $contactId,
            $amount,
            $year,
            $paymentMethod,
            $isArrears,
            $notes,
            $transactionId
        );
        
        return $payment;
    }

    /**
     * Delete a payment
     */
    public function deletePayment(int $paymentId): bool
    {
        $repository = $this->entityManager->getRepository(Payment::class);
        $payment = $repository->find($paymentId);
        
        if (!$payment) {
            return false;
        }
        
        $this->entityManager->remove($payment);
        $this->entityManager->flush();
        
        return true;
    }

    /**
     * Find a payment by transaction ID
     */
    public function findPaymentByTransactionId(string $transactionId): ?Payment
    {
        $repository = $this->entityManager->getRepository(Payment::class);
        
        return $repository->findByTransactionId($transactionId);
    }

    /**
     * Get total payments for a contact in a specific year
     */
    public function getContactYearPaymentsTotal(int $contactId, int $year): float
    {
        $repository = $this->entityManager->getRepository(Payment::class);
        
        return $repository->getContactYearPaymentsSum($contactId, $year);
    }

    /**
     * Handle a webhook from Stripe
     */
    public function handleStripeWebhook(array $payload): bool
    {
        // This would be implemented to handle Stripe webhook events
        // For example, when a payment is completed through Stripe
        
        // Check the event type
        $eventType = $payload['type'] ?? '';
        
        if ($eventType !== 'checkout.session.completed') {
            // Only process completed checkout sessions
            return false;
        }
        
        // Extract session data
        $session = $payload['data']['object'] ?? [];
        
        if (empty($session) || empty($session['metadata'])) {
            // We need metadata to process the payment
            return false;
        }
        
        // Extract metadata
        $metadata = $session['metadata'];
        $contactId = (int) ($metadata['contact_id'] ?? 0);
        $year = (int) ($metadata['year'] ?? date('Y'));
        $isArrears = (bool) ($metadata['is_arrears'] ?? false);
        $notes = $metadata['notes'] ?? 'Payment via Stripe';
        
        // Get the payment amount
        $amount = (float) ($session['amount_total'] ?? 0) / 100; // Stripe amounts are in cents
        
        // Get the transaction ID
        $transactionId = $session['payment_intent'] ?? $session['id'] ?? null;
        
        // Check if we already processed this transaction
        if ($transactionId) {
            $existingPayment = $this->findPaymentByTransactionId($transactionId);
            
            if ($existingPayment) {
                // Already processed
                return true;
            }
        }
        
        // Create the payment
        if ($contactId > 0 && $amount > 0) {
            $this->createPayment(
                $contactId,
                $amount,
                $year,
                'Card', // Stripe payments are always by card
                $isArrears,
                $notes,
                $transactionId
            );
            
            return true;
        }
        
        return false;
    }
}