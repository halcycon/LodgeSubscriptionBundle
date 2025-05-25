<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractFormController;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Settings;
use MauticPlugin\LodgeSubscriptionBundle\Form\Type\SubscriptionSettingsType;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends AbstractFormController
{
    /**
     * Manage subscription settings
     */
    public function indexAction(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $settingsRepository = $entityManager->getRepository(Settings::class);
        
        $settings = new Settings();
        $settings->setYear((int) date('Y'));
        
        // Check if settings already exist for current year
        $currentSettings = $settingsRepository->getSettingsForYear((int) date('Y'));
        if ($currentSettings) {
            $settings = $currentSettings;
        }
        
        $action = $this->generateUrl('lodge_subscription_settings');
        $form = $this->get('form.factory')->create(SubscriptionSettingsType::class, $settings, ['action' => $action]);
        
        if ($this->request->getMethod() === 'POST') {
            if ($this->isFormValid($form)) {
                $data = $form->getData();
                
                // Check if settings already exist for this year
                $existingSettings = $settingsRepository->getSettingsForYear($data->getYear());
                if ($existingSettings && $existingSettings->getId() !== $data->getId()) {
                    $this->addFlash('mautic.core.error.settings.exists', [
                        '%year%' => $data->getYear(),
                    ]);
                } else {
                    $entityManager->persist($data);
                    $entityManager->flush();
                    
                    $this->addFlash('mautic.core.notice.updated', [
                        '%name%' => 'Subscription Settings',
                        '%url%' => $this->generateUrl('lodge_subscription_settings'),
                    ]);
                    
                    // Redirect to prevent form resubmission
                    return $this->redirect($action);
                }
            }
        }
        
        // Get all years' settings
        $allSettings = $settingsRepository->findBy([], ['year' => 'DESC']);
        
        return $this->delegateView([
            'viewParameters' => [
                'form' => $form->createView(),
                'allSettings' => $allSettings,
            ],
            'contentTemplate' => 'LodgeSubscriptionBundle:Settings:index.html.php',
            'pagetitle' => 'Subscription Settings',
        ]);
    }

    /**
     * Edit settings for a specific year
     */
    public function editAction(int $year): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $settingsRepository = $entityManager->getRepository(Settings::class);
        
        $settings = $settingsRepository->getSettingsForYear($year);
        
        if (!$settings) {
            $this->addFlash('mautic.core.error.notfound', ['%name%' => 'Settings for year ' . $year]);
            return $this->redirect($this->generateUrl('lodge_subscription_settings'));
        }
        
        $action = $this->generateUrl('lodge_subscription_settings');
        $form = $this->get('form.factory')->create(SubscriptionSettingsType::class, $settings, ['action' => $action]);
        
        if ($this->request->getMethod() === 'POST') {
            if ($this->isFormValid($form)) {
                $data = $form->getData();
                
                $entityManager->persist($data);
                $entityManager->flush();
                
                $this->addFlash('mautic.core.notice.updated', [
                    '%name%' => 'Subscription Settings',
                    '%url%' => $this->generateUrl('lodge_subscription_settings'),
                ]);
                
                // Redirect to prevent form resubmission
                return $this->redirect($action);
            }
        }
        
        // Get all years' settings
        $allSettings = $settingsRepository->findBy([], ['year' => 'DESC']);
        
        return $this->delegateView([
            'viewParameters' => [
                'form' => $form->createView(),
                'allSettings' => $allSettings,
            ],
            'contentTemplate' => 'LodgeSubscriptionBundle:Settings:index.html.php',
            'pagetitle' => 'Edit Subscription Settings: ' . $year,
        ]);
    }

    /**
     * Delete settings for a specific year
     */
    public function deleteAction(int $year): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $settingsRepository = $entityManager->getRepository(Settings::class);
        
        $settings = $settingsRepository->getSettingsForYear($year);
        
        if (!$settings) {
            $this->addFlash('mautic.core.error.notfound', ['%name%' => 'Settings for year ' . $year]);
            return $this->redirect($this->generateUrl('lodge_subscription_settings'));
        }
        
        // Confirm delete
        $flashes = [];
        
        if ($this->request->getMethod() === 'POST') {
            $entityManager->remove($settings);
            $entityManager->flush();
            
            $flashes[] = [
                'type' => 'notice',
                'msg' => 'mautic.core.notice.deleted',
                'msgVars' => ['%name%' => 'Settings for year ' . $year],
            ];
            
            return $this->postActionRedirect([
                'returnUrl' => $this->generateUrl('lodge_subscription_settings'),
                'flashes' => $flashes,
            ]);
        }
        
        return $this->delegateView([
            'viewParameters' => [
                'settings' => $settings,
            ],
            'contentTemplate' => 'LodgeSubscriptionBundle:Settings:delete.html.php',
            'pagetitle' => 'Delete Subscription Settings: ' . $year,
        ]);
    }
} 