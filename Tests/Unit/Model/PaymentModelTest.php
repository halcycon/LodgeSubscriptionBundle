<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Tests\Unit\Model;

use Doctrine\ORM\EntityManager;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Payment;
use MauticPlugin\LodgeSubscriptionBundle\Entity\PaymentRepository;
use MauticPlugin\LodgeSubscriptionBundle\Model\PaymentModel;
use MauticPlugin\LodgeSubscriptionBundle\Model\SubscriptionModel;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PaymentModelTest extends TestCase
{
    /**
     * @var EntityManager|MockObject
     */
    private $entityManager;

    /**
     * @var LeadModel|MockObject
     */
    private $leadModel;

    /**
     * @var SubscriptionModel|MockObject
     */
    private $subscriptionModel;

    /**
     * @var PaymentRepository|MockObject
     */
    private $paymentRepository;

    /**
     * @var PaymentModel
     */
    private $paymentModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManager::class);
        $this->leadModel = $this->createMock(LeadModel::class);
        $this->subscriptionModel = $this->createMock(SubscriptionModel::class);
        $this->paymentRepository = $this->createMock(PaymentRepository::class);
        
        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->with(Payment::class)
            ->willReturn($this->paymentRepository);
        
        $this->paymentModel = new PaymentModel(
            $this->entityManager,
            $this->leadModel,
            $this->subscriptionModel
        );
    }

    public function testCreatePayment(): void
    {
        // Create a test payment data
        $contactId = 123;
        $amount = 100.00;
        $paymentDate = new \DateTime();
        $paymentMethod = 'Credit Card';
        $year = 2023;
        $isArrears = false;
        $transactionId = 'txn_123456';
        $notes = 'Test payment';

        // Create a mock contact
        $contact = $this->createMock(Lead::class);
        $contact->method('getId')
            ->willReturn($contactId);
        
        $this->leadModel->expects($this->once())
            ->method('getEntity')
            ->with($contactId)
            ->willReturn($contact);
        
        // The subscription model should be called to update contact fields
        $this->subscriptionModel->expects($this->once())
            ->method('updateContactAfterPayment')
            ->with($contactId, $amount, $year, $isArrears);
        
        // The entity manager should be called to persist and flush
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($payment) use ($contactId, $amount, $year, $isArrears, $transactionId) {
                return $payment instanceof Payment
                    && $payment->getContactId() === $contactId
                    && $payment->getAmount() === $amount
                    && $payment->getYear() === $year
                    && $payment->isArrears() === $isArrears
                    && $payment->getTransactionId() === $transactionId;
            }));
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Call the method
        $payment = $this->paymentModel->createPayment(
            $contactId,
            $amount,
            $paymentDate,
            $paymentMethod,
            $year,
            $isArrears,
            $transactionId,
            $notes
        );
        
        // Verify the payment object
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($contactId, $payment->getContactId());
        $this->assertEquals($amount, $payment->getAmount());
        $this->assertEquals($paymentDate, $payment->getPaymentDate());
        $this->assertEquals($paymentMethod, $payment->getPaymentMethod());
        $this->assertEquals($year, $payment->getYear());
        $this->assertEquals($isArrears, $payment->isArrears());
        $this->assertEquals($transactionId, $payment->getTransactionId());
        $this->assertEquals($notes, $payment->getNotes());
    }
    
    public function testGetContactPayments(): void
    {
        $contactId = 123;
        $mockPayments = [
            new Payment(),
            new Payment(),
        ];
        
        $this->paymentRepository->expects($this->once())
            ->method('getContactPayments')
            ->with($contactId)
            ->willReturn($mockPayments);
            
        $payments = $this->paymentModel->getContactPayments($contactId);
        
        $this->assertSame($mockPayments, $payments);
        $this->assertCount(2, $payments);
    }
} 