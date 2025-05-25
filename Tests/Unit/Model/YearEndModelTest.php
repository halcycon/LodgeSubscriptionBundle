<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Tests\Unit\Model;

use Doctrine\ORM\EntityManager;
use Mautic\LeadBundle\Model\FieldModel;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Settings;
use MauticPlugin\LodgeSubscriptionBundle\Entity\SettingsRepository;
use MauticPlugin\LodgeSubscriptionBundle\Entity\YearEndLog;
use MauticPlugin\LodgeSubscriptionBundle\Entity\YearEndLogRepository;
use MauticPlugin\LodgeSubscriptionBundle\Model\SubscriptionModel;
use MauticPlugin\LodgeSubscriptionBundle\Model\YearEndModel;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class YearEndModelTest extends TestCase
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
     * @var FieldModel|MockObject
     */
    private $fieldModel;

    /**
     * @var TokenStorageInterface|MockObject
     */
    private $tokenStorage;

    /**
     * @var SettingsRepository|MockObject
     */
    private $settingsRepository;

    /**
     * @var YearEndLogRepository|MockObject
     */
    private $yearEndLogRepository;

    /**
     * @var YearEndModel
     */
    private $yearEndModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManager::class);
        $this->leadModel = $this->createMock(LeadModel::class);
        $this->subscriptionModel = $this->createMock(SubscriptionModel::class);
        $this->fieldModel = $this->createMock(FieldModel::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->settingsRepository = $this->createMock(SettingsRepository::class);
        $this->yearEndLogRepository = $this->createMock(YearEndLogRepository::class);
        
        $this->entityManager->method('getRepository')
            ->willReturnMap([
                [Settings::class, $this->settingsRepository],
                [YearEndLog::class, $this->yearEndLogRepository]
            ]);
        
        // Mock the token storage to return a user
        $token = $this->createMock(TokenInterface::class);
        $user = $this->createMock(UserInterface::class);
        $user->method('getUsername')->willReturn('admin');
        $token->method('getUser')->willReturn($user);
        $this->tokenStorage->method('getToken')->willReturn($token);
        
        $this->yearEndModel = new YearEndModel(
            $this->entityManager,
            $this->leadModel,
            $this->subscriptionModel,
            $this->fieldModel,
            $this->tokenStorage
        );
    }

    public function testCheckRequirements(): void
    {
        $currentYear = date('Y');
        $nextYear = (string) ((int) $currentYear + 1);
        
        // Mock settings for both current and next year
        $currentYearSettings = new Settings();
        $currentYearSettings->setYear($currentYear);
        
        $nextYearSettings = new Settings();
        $nextYearSettings->setYear($nextYear);
        
        $this->settingsRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnMap([
                [['year' => $currentYear], $currentYearSettings],
                [['year' => $nextYear], $nextYearSettings]
            ]);
        
        // Mock that all required fields exist
        $this->subscriptionModel->expects($this->once())
            ->method('checkRequiredFields')
            ->with($nextYear)
            ->willReturn([]);
        
        $result = $this->yearEndModel->checkRequirements();
        
        $this->assertEmpty($result, 'Should return no errors when all requirements are met');
    }

    public function testCheckRequirementsMissingNextYearSettings(): void
    {
        $currentYear = date('Y');
        $nextYear = (string) ((int) $currentYear + 1);
        
        // Mock settings for current year but not next year
        $currentYearSettings = new Settings();
        $currentYearSettings->setYear($currentYear);
        
        $this->settingsRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnMap([
                [['year' => $currentYear], $currentYearSettings],
                [['year' => $nextYear], null]
            ]);
        
        $result = $this->yearEndModel->checkRequirements();
        
        $this->assertCount(1, $result, 'Should return one error about missing next year settings');
        $this->assertStringContainsString('next year', $result[0]);
    }

    public function testCheckRequirementsMissingFields(): void
    {
        $currentYear = date('Y');
        $nextYear = (string) ((int) $currentYear + 1);
        
        // Mock settings for both years
        $currentYearSettings = new Settings();
        $currentYearSettings->setYear($currentYear);
        
        $nextYearSettings = new Settings();
        $nextYearSettings->setYear($nextYear);
        
        $this->settingsRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnMap([
                [['year' => $currentYear], $currentYearSettings],
                [['year' => $nextYear], $nextYearSettings]
            ]);
        
        // Mock missing fields
        $this->subscriptionModel->expects($this->once())
            ->method('checkRequiredFields')
            ->with($nextYear)
            ->willReturn(['craft_' . $nextYear . '_due', 'craft_' . $nextYear . '_paid']);
        
        $result = $this->yearEndModel->checkRequirements();
        
        $this->assertCount(1, $result, 'Should return one error about missing fields');
        $this->assertStringContainsString('fields', $result[0]);
    }

    public function testGetYearEndLogs(): void
    {
        $logs = [new YearEndLog(), new YearEndLog()];
        
        $this->yearEndLogRepository->expects($this->once())
            ->method('findBy')
            ->with([], ['year' => 'DESC'])
            ->willReturn($logs);
            
        $result = $this->yearEndModel->getYearEndLogs();
        
        $this->assertSame($logs, $result);
        $this->assertCount(2, $result);
    }
} 