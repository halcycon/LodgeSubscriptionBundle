<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractFormController;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\LodgeSubscriptionBundle\Form\Type\PaymentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends AbstractFormController
{
    /**
     * New payment form
     */
    public function newAction(int $contactId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contact = $entityManager->getRepository(Lead::class)->find($contactId);
        
        if (!$contact) {
            return $this->notFound();
        }
        
        $subscriptionModel = $this->get('lodge_subscription.model.subscription');
        $paymentModel = $this->get('lodge_subscription.model.payment');
        
        // Default values
        $paymentData = [
            'contactId' => $contactId,
            'contactName' => $contact->getName(),
            'year' => (int) date('Y'),
            'isArrears' => false,
        ];
        
        // Calculate the amount due
        $subscriptionDetails = $subscriptionModel->calculateOutstandingDues($contact);
        $paymentData['amount'] = $subscriptionDetails['amount_owed_current'];
        
        $form = $this->get('form.factory')->create(PaymentType::class, $paymentData);
        
        if ($this->request->getMethod() === 'POST') {
            if ($this->isFormCancelled($form)) {
                return $this->closeModalResponse();
            }
            
            if ($this->isFormValid($form)) {
                $formData = $form->getData();
                
                try {
                    $payment = $paymentModel->createPayment(
                        (int) $formData['contactId'],
                        (float) $formData['amount'],
                        (int) $formData['year'],
                        $formData['paymentMethod'],
                        (bool) $formData['isArrears'],
                        $formData['notes'],
                        $formData['transactionId']
                    );
                    
                    $this->addFlash('mautic.core.notice.created', [
                        '%name%' => 'Payment',
                        '%url%' => $this->generateUrl('lodge_subscription_payment', [
                            'objectAction' => 'view',
                            'objectId' => $payment->getId(),
                        ]),
                    ]);
                    
                    return $this->closeModalResponse();
                } catch (\Exception $e) {
                    $this->addFlash('mautic.core.error.payment.create', [
                        '%message%' => $e->getMessage(),
                    ]);
                }
            }
        }
        
        return $this->delegateView([
            'viewParameters' => [
                'form' => $form->createView(),
                'contact' => $contact,
            ],
            'contentTemplate' => 'LodgeSubscriptionBundle:Payment:form.html.php',
            'pagetitle' => 'New Payment for ' . $contact->getName(),
        ]);
    }

    /**
     * View payment details
     */
    public function viewAction(int $objectId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $payment = $entityManager->getRepository(\MauticPlugin\LodgeSubscriptionBundle\Entity\Payment::class)->find($objectId);
        
        if (!$payment) {
            return $this->notFound();
        }
        
        $contact = $entityManager->getRepository(Lead::class)->find($payment->getContactId());
        
        return $this->delegateView([
            'viewParameters' => [
                'payment' => $payment,
                'contact' => $contact,
            ],
            'contentTemplate' => 'LodgeSubscriptionBundle:Payment:view.html.php',
            'pagetitle' => 'Payment Details',
        ]);
    }

    /**
     * Delete a payment
     */
    public function deleteAction(int $objectId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $payment = $entityManager->getRepository(\MauticPlugin\LodgeSubscriptionBundle\Entity\Payment::class)->find($objectId);
        
        if (!$payment) {
            return $this->notFound();
        }
        
        $paymentModel = $this->get('lodge_subscription.model.payment');
        
        $contactId = $payment->getContactId();
        
        // Confirm delete
        $flashes = [];
        
        if ($this->request->getMethod() === 'POST') {
            $paymentModel->deletePayment($objectId);
            
            $flashes[] = [
                'type' => 'notice',
                'msg' => 'mautic.core.notice.deleted',
                'msgVars' => ['%name%' => 'Payment'],
            ];
            
            return $this->postActionRedirect([
                'returnUrl' => $this->generateUrl('lodge_subscription_action', [
                    'objectAction' => 'view',
                    'objectId' => $contactId,
                ]),
                'flashes' => $flashes,
            ]);
        }
        
        return $this->delegateView([
            'viewParameters' => [
                'payment' => $payment,
                'contactId' => $contactId,
            ],
            'contentTemplate' => 'LodgeSubscriptionBundle:Payment:delete.html.php',
            'pagetitle' => 'Delete Payment',
        ]);
    }

    /**
     * Execute action for AJAX modal.
     */
    public function executeAction(string $objectAction, int $objectId = 0): Response
    {
        if ($objectAction === 'new') {
            return $this->newAction($objectId);
        }
        
        if ($objectAction === 'view') {
            return $this->viewAction($objectId);
        }
        
        if ($objectAction === 'delete') {
            return $this->deleteAction($objectId);
        }
        
        return $this->notFound();
    }
} 