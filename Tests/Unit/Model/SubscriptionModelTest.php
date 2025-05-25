<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Tests\Unit\Model;

use Doctrine\ORM\EntityManager;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\FieldModel;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Payment;
use MauticPlugin\LodgeSubscriptionBundle\Entity\PaymentRepository;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Settings;
use MauticPlugin\LodgeSubscriptionBundle\Entity\SettingsRepository;
use MauticPlugin\LodgeSubscriptionBundle\Model\SubscriptionModel;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class SubscriptionModelTest extends TestCase
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
     * @var FieldModel|MockObject
     */
    private $fieldModel;

    /**
     * @var SettingsRepository|MockObject
     */
    private $settingsRepository;

    /**
     * @var PaymentRepository|MockObject
     */
    private $paymentRepository;

    /**
     * @var SubscriptionModel
     */
    private $subscriptionModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManager::class);
        $this->leadModel = $this->createMock(LeadModel::class);
        $this->fieldModel = $this->createMock(FieldModel::class);
        $this->settingsRepository = $this->createMock(SettingsRepository::class);
        $this->paymentRepository = $this->createMock(PaymentRepository::class);
        
        $this->entityManager->method('getRepository')
            ->willReturnMap([
                [Settings::class, $this->settingsRepository],
                [Payment::class, $this->paymentRepository]
            ]);
        
        $this->subscriptionModel = new SubscriptionModel(
            $this->entityManager,
            $this->leadModel,
            $this->fieldModel
        );
    }

    public function testGetSubscriptionDetails(): void
    {
        $contactId = 123;
        $currentYear = date('Y');
        
        // Create a mock contact
        $contact = $this->createMock(Lead::class);
        $contact->method('getId')
            ->willReturn($contactId);
        
        // Mock settings for current year
        $settings = new Settings();
        $settings->setYear($currentYear);
        $settings->setAmountFull(100.00);
        $settings->setAmountReduced(50.00);
        $settings->setAmountHonorary(0.00);
        
        $this->settingsRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['year' => $currentYear])
            ->willReturn($settings);
        
        // Mock subscription type
        $contact->expects($this->once())
            ->method('getFieldValue')
            ->with('craft_subscription_type')
            ->willReturn('Full');
        
        // Mock payment repository
        $this->paymentRepository->expects($this->once())
            ->method('getContactPaymentsByYear')
            ->with($contactId, $currentYear)
            ->willReturn([]);
        
        // Test the method
        $details = $this->subscriptionModel->getSubscriptionDetails($contact);
        
        // Assert that the details array is structured correctly
        $this->assertIsArray($details);
        $this->assertArrayHasKey('current_year', $details);
        $this->assertArrayHasKey('total_owed', $details);
        $this->assertArrayHasKey('amount_owed_current', $details);
        $this->assertArrayHasKey('amount_owed_arrears', $details);
        $this->assertArrayHasKey('current_year_paid', $details);
        $this->assertArrayHasKey('previous_years', $details);
        
        // Current year should be the current year
        $this->assertEquals($currentYear, $details['current_year']);
        
        // Since no payments were mocked, amount owed should be the full subscription amount
        $this->assertEquals(100.00, $details['amount_owed_current']);
    }
    
    public function testUpdateContactAfterPayment(): void
    {
        $contactId = 123;
        $amount = 100.00;
        $year = date('Y');
        $isArrears = false;
        
        // Create a mock contact
        $contact = $this->createMock(Lead::class);
        
        $this->leadModel->expects($this->once())
            ->method('getEntity')
            ->with($contactId)
            ->willReturn($contact);
        
        // The contact should be updated with the payment info
        $contact->expects($this->atLeastOnce())
            ->method('addUpdatedField')
            ->withConsecutive(
                ['craft_' . $year . '_paid', true],
                ['craft_last_payment_date', $this->anything()],
                ['craft_last_payment_amount', $amount]
            );
        
        // Entity manager should be called to save the contact
        $this->leadModel->expects($this->once())
            ->method('saveEntity')
            ->with($contact);
        
        // Call the method
        $this->subscriptionModel->updateContactAfterPayment($contactId, $amount, $year, $isArrears);
    }
} 