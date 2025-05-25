<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Tests\Functional\Controller;

use Mautic\CoreBundle\Test\MauticMysqlTestCase;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Settings;
use Symfony\Component\HttpFoundation\Response;

class PublicControllerTest extends MauticMysqlTestCase
{
    /**
     * @var Lead
     */
    private $testContact;

    /**
     * @var Settings
     */
    private $settings;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test contact
        $contactModel = self::$container->get('mautic.lead.model.lead');
        $this->testContact = new Lead();
        $this->testContact->setFirstname('Test');
        $this->testContact->setLastname('Contact');
        $this->testContact->setEmail('test@example.com');
        $contactModel->saveEntity($this->testContact);

        // Create test settings
        $entityManager = self::$container->get('doctrine.orm.entity_manager');
        $this->settings = new Settings();
        $this->settings->setYear(date('Y'));
        $this->settings->setAmountFull(100.00);
        $this->settings->setAmountReduced(50.00);
        $this->settings->setAmountHonorary(0.00);
        $this->settings->setStripePublishableKey('pk_test_example');
        $this->settings->setStripeSecretKey('sk_test_example');
        $this->settings->setStripeWebhookSecret('whsec_example');
        $entityManager->persist($this->settings);
        $entityManager->flush();
    }

    public function testPaymentPageNotFound(): void
    {
        $client = self::createClient();
        $client->request('GET', '/p/subscription/payment/invalid_token');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    /**
     * This test doesn't actually test the payment page functionality
     * but rather ensures the controller can handle the route and parameters correctly.
     * Real payment processing would be tested separately with mocked Stripe services.
     */
    public function testPaymentPage(): void
    {
        // Generate a test token (in real implementation this would be a proper encrypted token)
        $token = base64_encode(json_encode([
            'contact_id' => $this->testContact->getId(),
            'amount' => 100.00,
            'year' => date('Y'),
            'expires' => time() + 3600,
            'type' => 'current',
        ]));

        $client = self::createClient();
        $client->request('GET', '/p/subscription/payment/' . $token);

        // Since we don't have a real token processing system in the test,
        // we can only check that the controller handles the request without errors
        $this->assertTrue($client->getResponse()->isSuccessful() || $client->getResponse()->isRedirection());
    }

    protected function tearDown(): void
    {
        $entityManager = self::$container->get('doctrine.orm.entity_manager');
        
        // Remove test settings
        $entityManager->remove($this->settings);
        
        // Remove test contact
        $entityManager->remove($this->testContact);
        
        $entityManager->flush();
        
        parent::tearDown();
    }
} 