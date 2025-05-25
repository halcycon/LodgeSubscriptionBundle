<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractFormController;
use MauticPlugin\LodgeSubscriptionBundle\Form\Type\YearEndType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class YearEndController extends AbstractFormController
{
    /**
     * Year-end process form
     */
    public function indexAction(): Response
    {
        $yearEndModel = $this->get('lodge_subscription.model.yearend');
        
        $currentYear = (int) date('Y');
        $form = $this->get('form.factory')->create(YearEndType::class);
        
        // Check if prerequisites are met
        $errors = $yearEndModel->validateYearEndPrerequisites($currentYear);
        
        // Get statistics for the current year
        $statistics = $yearEndModel->getYearEndStatistics($currentYear);
        
        // Get previous year-end logs
        $logs = $yearEndModel->getAllYearEndLogs();
        
        return $this->delegateView([
            'viewParameters' => [
                'form' => $form->createView(),
                'errors' => $errors,
                'statistics' => $statistics,
                'logs' => $logs,
            ],
            'contentTemplate' => 'LodgeSubscriptionBundle:YearEnd:index.html.php',
            'pagetitle' => 'Year-End Processing',
        ]);
    }

    /**
     * Execute year-end process
     */
    public function processYearEndAction(): Response
    {
        $form = $this->get('form.factory')->create(YearEndType::class);
        
        if ($this->request->getMethod() === 'POST') {
            if ($form->handleRequest($this->request) && $form->isValid()) {
                $data = $form->getData();
                $year = (int) $data['year'];
                
                $yearEndModel = $this->get('lodge_subscription.model.yearend');
                
                try {
                    // Validate prerequisites
                    $errors = $yearEndModel->validateYearEndPrerequisites($year);
                    
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            $this->addFlash('error', $error);
                        }
                    } else {
                        // Execute the year-end process
                        $userId = $this->get('mautic.helper.user')->getUser()->getId();
                        $result = $yearEndModel->executeYearEndProcess($year, $userId);
                        
                        $this->addFlash(
                            'notice',
                            'Year-end process completed successfully. Processed ' . $result['contacts_processed'] . ' contacts.'
                        );
                        
                        return $this->redirect($this->generateUrl('lodge_subscription_yearend'));
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error executing year-end process: ' . $e->getMessage());
                }
            }
        }
        
        return $this->redirect($this->generateUrl('lodge_subscription_yearend'));
    }

    /**
     * View year-end log details
     */
    public function viewLogAction(int $year): Response
    {
        $yearEndModel = $this->get('lodge_subscription.model.yearend');
        $log = $yearEndModel->getYearEndLog($year);
        
        if (!$log) {
            $this->addFlash('error', 'Year-end log not found for year ' . $year);
            return $this->redirect($this->generateUrl('lodge_subscription_yearend'));
        }
        
        return $this->delegateView([
            'viewParameters' => [
                'log' => $log,
            ],
            'contentTemplate' => 'LodgeSubscriptionBundle:YearEnd:log.html.php',
            'pagetitle' => 'Year-End Log: ' . $year,
        ]);
    }
    
    /**
     * Execute action for AJAX modal.
     */
    public function executeAction(string $objectAction, int $objectId = 0, ?int $objectSubId = 0): Response
    {
        if ($objectAction === 'index') {
            return $this->indexAction();
        }
        
        if ($objectAction === 'process') {
            return $this->processYearEndAction();
        }
        
        if ($objectAction === 'log' && $objectId) {
            return $this->viewLogAction((int) $objectId);
        }
        
        return $this->notFound();
    }
} 