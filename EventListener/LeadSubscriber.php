<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Event\TokenReplacementEvent;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\LeadEvents;
use MauticPlugin\LodgeSubscriptionBundle\Model\SubscriptionModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LeadSubscriber implements EventSubscriberInterface
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LeadEvents::LEAD_POST_SAVE => ['onLeadPostSave', 0],
            EmailEvents::EMAIL_ON_SEND => ['onEmailSend', 0],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailDisplay', 0],
        ];
    }

    /**
     * Handle post-save events for leads
     */
    public function onLeadPostSave(LeadEvent $event): void
    {
        $lead = $event->getLead();
        $changes = $event->getChanges();
        
        // If this is a new lead, we might want to set up subscription fields
        if ($event->isNew()) {
            // Get the current year
            $currentYear = (int) date('Y');
            
            // Check if the required fields exist
            $missingFields = $this->subscriptionModel->checkRequiredFields($currentYear);
            
            // If any fields are missing, create them
            if (!empty($missingFields)) {
                $this->subscriptionModel->createRequiredFields($currentYear);
            }
        }
    }

    /**
     * Handle token replacement for emails
     */
    public function onEmailDisplay(TokenReplacementEvent $event): void
    {
        $this->handleTokenReplacement($event);
    }

    /**
     * Handle token replacement for emails being sent
     */
    public function onEmailSend(EmailSendEvent $event): void
    {
        $this->handleTokenReplacement($event);
    }

    /**
     * Handle token replacements for both display and send events
     */
    private function handleTokenReplacement(TokenReplacementEvent $event): void
    {
        $lead = $event->getLead();
        if (!$lead) {
            return;
        }
        
        $content = $event->getContent();
        
        // Check for our custom tokens
        if (strpos($content, '{lodge.payment_link}') !== false) {
            // Generate a payment link for the current subscription
            try {
                $stripeHelper = $this->subscriptionModel->generateStripePaymentLink(
                    $lead,
                    (float) $lead->getFieldValue('craft_owed_current'),
                    false
                );
                
                $content = str_replace('{lodge.payment_link}', $stripeHelper, $content);
            } catch (\Exception $e) {
                $content = str_replace('{lodge.payment_link}', '#', $content);
            }
        }
        
        if (strpos($content, '{lodge.arrears_payment_link}') !== false) {
            // Generate a payment link for arrears
            try {
                $stripeHelper = $this->subscriptionModel->generateStripePaymentLink(
                    $lead,
                    (float) $lead->getFieldValue('craft_owed_arrears'),
                    true
                );
                
                $content = str_replace('{lodge.arrears_payment_link}', $stripeHelper, $content);
            } catch (\Exception $e) {
                $content = str_replace('{lodge.arrears_payment_link}', '#', $content);
            }
        }
        
        // Current subscription amount
        if (strpos($content, '{lodge.current_subscription}') !== false) {
            $currentAmount = (float) $lead->getFieldValue('craft_owed_current');
            $content = str_replace('{lodge.current_subscription}', number_format($currentAmount, 2), $content);
        }
        
        // Arrears amount
        if (strpos($content, '{lodge.arrears_amount}') !== false) {
            $arrearsAmount = (float) $lead->getFieldValue('craft_owed_arrears');
            $content = str_replace('{lodge.arrears_amount}', number_format($arrearsAmount, 2), $content);
        }
        
        // Total outstanding
        if (strpos($content, '{lodge.total_outstanding}') !== false) {
            $currentAmount = (float) $lead->getFieldValue('craft_owed_current');
            $arrearsAmount = (float) $lead->getFieldValue('craft_owed_arrears');
            $totalAmount = $currentAmount + $arrearsAmount;
            
            $content = str_replace('{lodge.total_outstanding}', number_format($totalAmount, 2), $content);
        }
        
        $event->setContent($content);
    }
} 