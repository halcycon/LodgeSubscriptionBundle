<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Model;

use Doctrine\ORM\EntityManager;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\FieldModel;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Settings;
use MauticPlugin\LodgeSubscriptionBundle\Entity\YearEndLog;

class YearEndModel
{
    private EntityManager $entityManager;
    private SubscriptionModel $subscriptionModel;
    private FieldModel $fieldModel;

    public function __construct(
        EntityManager $entityManager,
        SubscriptionModel $subscriptionModel,
        FieldModel $fieldModel
    ) {
        $this->entityManager = $entityManager;
        $this->subscriptionModel = $subscriptionModel;
        $this->fieldModel = $fieldModel;
    }

    /**
     * Check if a year end process has already been run for a year
     */
    public function isYearEndProcessed(int $year): bool
    {
        $repository = $this->entityManager->getRepository(YearEndLog::class);
        
        return $repository->hasYearEndProcess($year);
    }

    /**
     * Get all year-end logs
     */
    public function getAllYearEndLogs(): array
    {
        $repository = $this->entityManager->getRepository(YearEndLog::class);
        
        return $repository->getAllLogs();
    }

    /**
     * Get a specific year-end log
     */
    public function getYearEndLog(int $year): ?YearEndLog
    {
        $repository = $this->entityManager->getRepository(YearEndLog::class);
        
        return $repository->getYearEndLog($year);
    }

    /**
     * Check if settings exist for the next year
     */
    public function hasSettingsForNextYear(int $currentYear): bool
    {
        $repository = $this->entityManager->getRepository(Settings::class);
        $nextYear = $currentYear + 1;
        
        return $repository->getSettingsForYear($nextYear) !== null;
    }

    /**
     * Create settings for the next year based on current year
     */
    public function createNextYearSettings(int $currentYear): Settings
    {
        $repository = $this->entityManager->getRepository(Settings::class);
        $currentSettings = $repository->getSettingsForYear($currentYear);
        
        if (!$currentSettings) {
            throw new \Exception('Cannot find settings for current year: ' . $currentYear);
        }
        
        $nextYear = $currentYear + 1;
        
        // Check if settings already exist
        if ($repository->getSettingsForYear($nextYear)) {
            throw new \Exception('Settings for year ' . $nextYear . ' already exist');
        }
        
        // Create new settings based on current
        $newSettings = new Settings();
        $newSettings->setYear($nextYear);
        $newSettings->setAmountFull($currentSettings->getAmountFull());
        $newSettings->setAmountReduced($currentSettings->getAmountReduced());
        $newSettings->setAmountHonorary($currentSettings->getAmountHonorary());
        $newSettings->setStripePublishableKey($currentSettings->getStripePublishableKey());
        $newSettings->setStripeSecretKey($currentSettings->getStripeSecretKey());
        $newSettings->setStripeWebhookSecret($currentSettings->getStripeWebhookSecret());
        
        $this->entityManager->persist($newSettings);
        $this->entityManager->flush();
        
        return $newSettings;
    }

    /**
     * Validate if all prerequisites are met for year-end process
     */
    public function validateYearEndPrerequisites(int $year): array
    {
        $errors = [];
        $nextYear = $year + 1;
        
        // Check if process already run
        if ($this->isYearEndProcessed($year)) {
            $errors[] = 'Year-end process has already been run for ' . $year;
        }
        
        // Check if next year settings exist
        $settingsRepository = $this->entityManager->getRepository(Settings::class);
        if (!$settingsRepository->getSettingsForYear($nextYear)) {
            $errors[] = 'Settings for next year (' . $nextYear . ') do not exist. Please create them first.';
        }
        
        // Check if required fields exist
        $missingFields = $this->subscriptionModel->checkRequiredFields($year);
        if (!empty($missingFields)) {
            $errors[] = 'Missing required fields for current year: ' . implode(', ', $missingFields);
        }
        
        return $errors;
    }

    /**
     * Execute the year-end process
     */
    public function executeYearEndProcess(int $year, int $userId): array
    {
        // Validate prerequisites
        $errors = $this->validateYearEndPrerequisites($year);
        if (!empty($errors)) {
            throw new \Exception(implode("\n", $errors));
        }
        
        $nextYear = $year + 1;
        
        // Execute the year-end process through the subscription model
        $result = $this->subscriptionModel->executeYearEndProcess($year, $nextYear);
        
        // Create a year-end log if not created by the subscription model
        if (empty($result['log_id'])) {
            $yearEndLog = new YearEndLog();
            $yearEndLog->setYear($year);
            $yearEndLog->setProcessedDate(new \DateTime());
            $yearEndLog->setProcessedBy($userId);
            $yearEndLog->setContactsProcessed($result['contacts_processed']);
            $yearEndLog->setFieldsCreated(implode(', ', $result['fields_created']));
            $yearEndLog->setLog(implode("\n", $result['log']));
            
            $this->entityManager->persist($yearEndLog);
            $this->entityManager->flush();
            
            $result['log_id'] = $yearEndLog->getId();
        }
        
        return $result;
    }

    /**
     * Get statistics for the year-end process
     */
    public function getYearEndStatistics(int $year): array
    {
        $statistics = [];
        
        // Get number of members with dues
        $dueFieldAlias = 'craft_' . $year . '_due';
        $contactRepository = $this->entityManager->getRepository(Lead::class);
        $membersWithDues = count($contactRepository->findByFieldValue($dueFieldAlias, true));
        
        // Get number of members who have paid
        $paidFieldAlias = 'craft_' . $year . '_paid';
        $membersWhoHavePaid = count($contactRepository->findByFieldValue($paidFieldAlias, true));
        
        // Get number of members who have not paid
        $membersWithoutPayment = $membersWithDues - $membersWhoHavePaid;
        
        // Get total amount collected
        $paymentRepository = $this->entityManager->getRepository(\MauticPlugin\LodgeSubscriptionBundle\Entity\Payment::class);
        $payments = $paymentRepository->getYearPayments($year);
        
        $totalCollected = 0;
        foreach ($payments as $payment) {
            $totalCollected += $payment->getAmount();
        }
        
        // Get total outstanding
        $settingsRepository = $this->entityManager->getRepository(Settings::class);
        $settings = $settingsRepository->getSettingsForYear($year);
        
        $totalDue = 0;
        if ($settings) {
            $totalDue = $membersWithDues * $settings->getAmountFull(); // This is an approximation
        }
        
        $totalOutstanding = $totalDue - $totalCollected;
        
        $statistics = [
            'year' => $year,
            'members_with_dues' => $membersWithDues,
            'members_paid' => $membersWhoHavePaid,
            'members_unpaid' => $membersWithoutPayment,
            'payment_rate' => $membersWithDues > 0 ? ($membersWhoHavePaid / $membersWithDues) * 100 : 0,
            'total_collected' => $totalCollected,
            'total_due' => $totalDue,
            'total_outstanding' => $totalOutstanding,
        ];
        
        return $statistics;
    }
} 