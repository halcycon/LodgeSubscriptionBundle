<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Model;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Model\AbstractCommonModel;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\FieldModel;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Settings;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Payment;

class SubscriptionModel extends AbstractCommonModel
{
    private EntityManager $entityManager;
    private FieldModel $fieldModel;
    
    // Field constants
    private const FIELD_DUE_PREFIX = 'craft_';
    private const FIELD_PAID_PREFIX = 'craft_';
    private const FIELD_DUE_SUFFIX = '_due';
    private const FIELD_PAID_SUFFIX = '_paid';
    private const FIELD_PAID_CURRENT = 'craft_paid_current';
    private const FIELD_OWED_CURRENT = 'craft_owed_current';
    private const FIELD_OWED_ARREARS = 'craft_owed_arrears';
    private const FIELD_SUBSCRIPTION_TYPE = 'craft_subscription_type';
    private const FIELD_LAST_PAYMENT_DATE = 'craft_last_payment_date';
    private const FIELD_PAYMENT_METHOD = 'craft_payment_method';
    private const FIELD_NOTES = 'craft_notes';

    public function __construct(EntityManager $entityManager, FieldModel $fieldModel)
    {
        $this->entityManager = $entityManager;
        $this->fieldModel = $fieldModel;
    }

    /**
     * Check if required custom fields exist for the given year
     */
    public function checkRequiredFields(int $year): array
    {
        $missingFields = [];
        
        // Get all lead fields
        $leadFields = $this->fieldModel->getEntities();
        $existingFieldAliases = [];
        
        foreach ($leadFields as $field) {
            $existingFieldAliases[] = $field->getAlias();
        }
        
        // Define the required fields for the year
        $requiredFields = [
            self::FIELD_DUE_PREFIX . $year . self::FIELD_DUE_SUFFIX,
            self::FIELD_PAID_PREFIX . $year . self::FIELD_PAID_SUFFIX,
            self::FIELD_PAID_CURRENT,
            self::FIELD_OWED_CURRENT,
            self::FIELD_OWED_ARREARS,
        ];
        
        // Check which required fields are missing
        foreach ($requiredFields as $field) {
            if (!in_array($field, $existingFieldAliases)) {
                $missingFields[] = $field;
            }
        }
        
        return $missingFields;
    }

    /**
     * Create a required custom field
     */
    public function createCustomField(string $alias, string $label, string $type, array $properties = []): void
    {
        // Check if field already exists
        $existingField = $this->fieldModel->getEntityByAlias($alias);
        
        if (!$existingField) {
            $field = $this->fieldModel->getEntity();
            $field->setAlias($alias);
            $field->setLabel($label);
            $field->setType($type);
            $field->setObject('lead');
            $field->setIsPublished(true);
            $field->setIsRequired(false);
            $field->setIsPubliclyUpdatable(false);
            $field->setIsUniqueIdentifier(false);
            $field->setProperties($properties);
            
            $this->fieldModel->saveEntity($field);
        }
    }

    /**
     * Create all required custom fields for a year
     */
    public function createRequiredFields(int $year): array
    {
        $fieldsCreated = [];
        
        // Boolean fields for due and paid
        $this->createCustomField(
            self::FIELD_DUE_PREFIX . $year . self::FIELD_DUE_SUFFIX,
            'Dues Required ' . $year,
            'boolean',
            ['yes' => 'Yes', 'no' => 'No']
        );
        $fieldsCreated[] = self::FIELD_DUE_PREFIX . $year . self::FIELD_DUE_SUFFIX;
        
        $this->createCustomField(
            self::FIELD_PAID_PREFIX . $year . self::FIELD_PAID_SUFFIX,
            'Dues Paid ' . $year,
            'boolean',
            ['yes' => 'Yes', 'no' => 'No']
        );
        $fieldsCreated[] = self::FIELD_PAID_PREFIX . $year . self::FIELD_PAID_SUFFIX;
        
        // Create additional fields if they don't exist
        $additionalFields = [
            [
                'alias' => self::FIELD_PAID_CURRENT,
                'label' => 'Paid Current Year',
                'type' => 'boolean',
                'properties' => ['yes' => 'Yes', 'no' => 'No'],
            ],
            [
                'alias' => self::FIELD_OWED_CURRENT,
                'label' => 'Amount Owed Current Year',
                'type' => 'number',
                'properties' => [],
            ],
            [
                'alias' => self::FIELD_OWED_ARREARS,
                'label' => 'Amount Owed in Arrears',
                'type' => 'number',
                'properties' => [],
            ],
            [
                'alias' => self::FIELD_SUBSCRIPTION_TYPE,
                'label' => 'Subscription Type',
                'type' => 'select',
                'properties' => [
                    'list' => [
                        ['label' => 'Full', 'value' => 'Full'],
                        ['label' => 'Reduced', 'value' => 'Reduced'],
                        ['label' => 'Honorary', 'value' => 'Honorary'],
                    ],
                ],
            ],
            [
                'alias' => self::FIELD_LAST_PAYMENT_DATE,
                'label' => 'Last Payment Date',
                'type' => 'date',
                'properties' => [],
            ],
            [
                'alias' => self::FIELD_PAYMENT_METHOD,
                'label' => 'Payment Method',
                'type' => 'select',
                'properties' => [
                    'list' => [
                        ['label' => 'Cash', 'value' => 'Cash'],
                        ['label' => 'Card', 'value' => 'Card'],
                        ['label' => 'Bank Transfer', 'value' => 'Bank Transfer'],
                        ['label' => 'Cheque', 'value' => 'Cheque'],
                    ],
                ],
            ],
            [
                'alias' => self::FIELD_NOTES,
                'label' => 'Subscription Notes',
                'type' => 'textarea',
                'properties' => [],
            ],
        ];
        
        foreach ($additionalFields as $fieldInfo) {
            $existingField = $this->fieldModel->getEntityByAlias($fieldInfo['alias']);
            
            if (!$existingField) {
                $this->createCustomField(
                    $fieldInfo['alias'],
                    $fieldInfo['label'],
                    $fieldInfo['type'],
                    $fieldInfo['properties']
                );
                $fieldsCreated[] = $fieldInfo['alias'];
            }
        }
        
        return $fieldsCreated;
    }

    /**
     * Get the subscription amount for a contact based on their subscription type
     */
    public function getSubscriptionAmountForContact(Lead $contact, int $year): float
    {
        $subscriptionType = $contact->getFieldValue(self::FIELD_SUBSCRIPTION_TYPE) ?: 'Full';
        
        // Get the settings for the year
        $settingsRepository = $this->entityManager->getRepository(Settings::class);
        $settings = $settingsRepository->getSettingsForYear($year);
        
        if (!$settings) {
            throw new \Exception('Settings for year ' . $year . ' not found');
        }
        
        switch ($subscriptionType) {
            case 'Reduced':
                return $settings->getAmountReduced();
            case 'Honorary':
                return $settings->getAmountHonorary();
            case 'Full':
            default:
                return $settings->getAmountFull();
        }
    }

    /**
     * Record a payment for a contact
     */
    public function recordPayment(
        int $contactId, 
        float $amount, 
        int $year, 
        string $paymentMethod, 
        bool $isArrears = false,
        ?string $notes = null,
        ?string $transactionId = null
    ): Payment {
        $contact = $this->entityManager->getRepository(Lead::class)->find($contactId);
        
        if (!$contact) {
            throw new \Exception('Contact not found');
        }
        
        // Create the payment record
        $payment = new Payment();
        $payment->setContactId($contactId);
        $payment->setAmount($amount);
        $payment->setPaymentDate(new \DateTime());
        $payment->setPaymentMethod($paymentMethod);
        $payment->setYear($year);
        $payment->setNotes($notes);
        $payment->setTransactionId($transactionId);
        $payment->setIsCurrent(!$isArrears);
        $payment->setIsArrears($isArrears);
        
        $this->entityManager->persist($payment);
        
        // Update the contact's fields
        if ($isArrears) {
            // Reduce arrears amount
            $currentArrears = (float) $contact->getFieldValue(self::FIELD_OWED_ARREARS);
            $newArrears = max(0, $currentArrears - $amount);
            $this->updateContactField($contact, self::FIELD_OWED_ARREARS, $newArrears);
        } else {
            // Reduce current amount owed
            $currentOwed = (float) $contact->getFieldValue(self::FIELD_OWED_CURRENT);
            $newOwed = max(0, $currentOwed - $amount);
            $this->updateContactField($contact, self::FIELD_OWED_CURRENT, $newOwed);
            
            // If fully paid, mark as paid for the year
            if ($newOwed == 0) {
                $this->updateContactField($contact, self::FIELD_PAID_CURRENT, true);
                $this->updateContactField($contact, self::FIELD_PAID_PREFIX . $year . self::FIELD_PAID_SUFFIX, true);
            }
        }
        
        // Update last payment date
        $this->updateContactField($contact, self::FIELD_LAST_PAYMENT_DATE, new \DateTime());
        
        // Update the payment method if provided
        if (!empty($paymentMethod)) {
            $this->updateContactField($contact, self::FIELD_PAYMENT_METHOD, $paymentMethod);
        }
        
        return $payment;
    }

    /**
     * Update a contact field
     */
    private function updateContactField(Lead $contact, string $alias, $value): void
    {
        $contact->addUpdatedField($alias, $value);
        $this->entityManager->persist($contact);
        $this->entityManager->flush();
    }

    /**
     * Generate a Stripe payment link
     */
    public function generateStripePaymentLink(Lead $contact, float $amount, bool $isArrears = false): string
    {
        // Get the Stripe settings from the current year's settings
        $settingsRepository = $this->entityManager->getRepository(Settings::class);
        $currentYear = (int) date('Y');
        $settings = $settingsRepository->getSettingsForYear($currentYear);
        
        if (!$settings || empty($settings->getStripePublishableKey())) {
            throw new \Exception('Stripe is not properly configured');
        }
        
        // Create a basic Stripe checkout link (in a real implementation, you'd use the Stripe API)
        $baseUrl = $_ENV['MAUTIC_URL'] ?? 'https://yourdomain.com';
        $callbackUrl = $baseUrl . '/lodge/payment/callback';
        
        // The description will indicate if this is for current year or arrears
        $description = $isArrears 
            ? 'Lodge Subscription Arrears Payment' 
            : 'Lodge Subscription ' . $currentYear;
        
        // In a real implementation, you would use the Stripe API to create a checkout session
        // This is a placeholder that creates a URL with parameters for demonstration
        $paymentLink = sprintf(
            '%s?contact_id=%d&amount=%.2f&is_arrears=%d&description=%s',
            $callbackUrl,
            $contact->getId(),
            $amount,
            $isArrears ? 1 : 0,
            urlencode($description)
        );
        
        return $paymentLink;
    }

    /**
     * Calculate outstanding dues for a contact
     */
    public function calculateOutstandingDues(Lead $contact): array
    {
        $currentYear = (int) date('Y');
        $currentYearDue = $contact->getFieldValue(self::FIELD_DUE_PREFIX . $currentYear . self::FIELD_DUE_SUFFIX);
        $currentYearPaid = $contact->getFieldValue(self::FIELD_PAID_PREFIX . $currentYear . self::FIELD_PAID_SUFFIX);
        
        $currentOwed = (float) $contact->getFieldValue(self::FIELD_OWED_CURRENT);
        $arrearsOwed = (float) $contact->getFieldValue(self::FIELD_OWED_ARREARS);
        
        $result = [
            'current_year' => $currentYear,
            'current_year_due' => $currentYearDue,
            'current_year_paid' => $currentYearPaid,
            'amount_owed_current' => $currentOwed,
            'amount_owed_arrears' => $arrearsOwed,
            'total_owed' => $currentOwed + $arrearsOwed,
        ];
        
        // Check previous years
        $result['previous_years'] = [];
        
        for ($year = $currentYear - 1; $year >= $currentYear - 3; $year--) {
            $dueField = self::FIELD_DUE_PREFIX . $year . self::FIELD_DUE_SUFFIX;
            $paidField = self::FIELD_PAID_PREFIX . $year . self::FIELD_PAID_SUFFIX;
            
            // Only include if we have the fields for this year
            $dueValue = $contact->getFieldValue($dueField);
            $paidValue = $contact->getFieldValue($paidField);
            
            if ($dueValue !== null) {
                $result['previous_years'][$year] = [
                    'due' => $dueValue,
                    'paid' => $paidValue,
                ];
            }
        }
        
        return $result;
    }

    /**
     * Execute year-end process for all members with dues
     */
    public function executeYearEndProcess(int $year, int $nextYear): array
    {
        $log = [];
        $contactsProcessed = 0;
        
        // Get all contacts that had dues in the given year
        $dueFieldAlias = self::FIELD_DUE_PREFIX . $year . self::FIELD_DUE_SUFFIX;
        $paidFieldAlias = self::FIELD_PAID_PREFIX . $year . self::FIELD_PAID_SUFFIX;
        
        $contactRepository = $this->entityManager->getRepository(Lead::class);
        $leads = $contactRepository->findByFieldValue($dueFieldAlias, true);
        
        // Create fields for next year if they don't exist
        $fieldsCreated = $this->createRequiredFields($nextYear);
        $log[] = 'Created fields for year ' . $nextYear . ': ' . implode(', ', $fieldsCreated);
        
        // Get next year's subscription settings
        $settingsRepository = $this->entityManager->getRepository(Settings::class);
        $nextYearSettings = $settingsRepository->getSettingsForYear($nextYear);
        
        if (!$nextYearSettings) {
            throw new \Exception('Settings for year ' . $nextYear . ' not found. Please create settings first.');
        }
        
        foreach ($leads as $contact) {
            $contactId = $contact->getId();
            $contactName = $contact->getName();
            $log[] = 'Processing contact: ' . $contactName . ' (ID: ' . $contactId . ')';
            
            // Check if they paid their dues for the year
            $paidValue = $contact->getFieldValue($paidFieldAlias);
            $owedCurrent = (float) $contact->getFieldValue(self::FIELD_OWED_CURRENT);
            
            // If they didn't pay, move the current amount to arrears
            if (!$paidValue && $owedCurrent > 0) {
                $owedArrears = (float) $contact->getFieldValue(self::FIELD_OWED_ARREARS);
                $newArrearsAmount = $owedArrears + $owedCurrent;
                
                $this->updateContactField($contact, self::FIELD_OWED_ARREARS, $newArrearsAmount);
                $log[] = 'Moved ' . $owedCurrent . ' from current to arrears for ' . $contactName;
            }
            
            // Set up the fields for next year
            $nextYearDueField = self::FIELD_DUE_PREFIX . $nextYear . self::FIELD_DUE_SUFFIX;
            $nextYearPaidField = self::FIELD_PAID_PREFIX . $nextYear . self::FIELD_PAID_SUFFIX;
            
            // Assume they will owe dues next year (copy from current year)
            $this->updateContactField($contact, $nextYearDueField, $contact->getFieldValue($dueFieldAlias));
            $this->updateContactField($contact, $nextYearPaidField, false);
            
            // Reset current year paid status
            $this->updateContactField($contact, self::FIELD_PAID_CURRENT, false);
            
            // Calculate new subscription amount based on their type
            $subscriptionType = $contact->getFieldValue(self::FIELD_SUBSCRIPTION_TYPE) ?: 'Full';
            $newAmount = 0;
            
            switch ($subscriptionType) {
                case 'Reduced':
                    $newAmount = $nextYearSettings->getAmountReduced();
                    break;
                case 'Honorary':
                    $newAmount = $nextYearSettings->getAmountHonorary();
                    break;
                case 'Full':
                default:
                    $newAmount = $nextYearSettings->getAmountFull();
                    break;
            }
            
            // Set new current amount owed
            $this->updateContactField($contact, self::FIELD_OWED_CURRENT, $newAmount);
            $log[] = 'Set new subscription amount for ' . $contactName . ': ' . $newAmount;
            
            $contactsProcessed++;
        }
        
        // Create a year-end log entry
        $yearEndLog = new \MauticPlugin\LodgeSubscriptionBundle\Entity\YearEndLog();
        $yearEndLog->setYear($year);
        $yearEndLog->setProcessedDate(new \DateTime());
        $yearEndLog->setProcessedBy(0); // This would normally be the current user ID
        $yearEndLog->setContactsProcessed($contactsProcessed);
        $yearEndLog->setFieldsCreated(implode(', ', $fieldsCreated));
        $yearEndLog->setLog(implode("\n", $log));
        
        $this->entityManager->persist($yearEndLog);
        $this->entityManager->flush();
        
        return [
            'contacts_processed' => $contactsProcessed,
            'fields_created' => $fieldsCreated,
            'log' => $log,
        ];
    }
} 