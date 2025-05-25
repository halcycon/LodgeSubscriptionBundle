<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;

class Settings extends FormEntity
{
    private int $id;
    private int $year;
    private float $amountFull;
    private float $amountReduced;
    private float $amountHonorary = 0.0;
    private ?string $stripePublishableKey = null;
    private ?string $stripeSecretKey = null;
    private ?string $stripeWebhookSecret = null;

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('lodge_subscription_settings')
            ->setCustomRepositoryClass(SettingsRepository::class);

        $builder->addId();

        $builder->createField('year', 'integer')
            ->build();

        $builder->createField('amountFull', 'decimal')
            ->columnName('amount_full')
            ->precision(10)
            ->scale(2)
            ->build();

        $builder->createField('amountReduced', 'decimal')
            ->columnName('amount_reduced')
            ->precision(10)
            ->scale(2)
            ->build();

        $builder->createField('amountHonorary', 'decimal')
            ->columnName('amount_honorary')
            ->precision(10)
            ->scale(2)
            ->build();

        $builder->createField('stripePublishableKey', 'string')
            ->columnName('stripe_publishable_key')
            ->nullable()
            ->length(255)
            ->build();

        $builder->createField('stripeSecretKey', 'string')
            ->columnName('stripe_secret_key')
            ->nullable()
            ->length(255)
            ->build();

        $builder->createField('stripeWebhookSecret', 'string')
            ->columnName('stripe_webhook_secret')
            ->nullable()
            ->length(255)
            ->build();

        $builder->addUniqueConstraint(['year'], 'lodge_subscription_year');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getAmountFull(): float
    {
        return $this->amountFull;
    }

    public function setAmountFull(float $amountFull): self
    {
        $this->amountFull = $amountFull;
        return $this;
    }

    public function getAmountReduced(): float
    {
        return $this->amountReduced;
    }

    public function setAmountReduced(float $amountReduced): self
    {
        $this->amountReduced = $amountReduced;
        return $this;
    }

    public function getAmountHonorary(): float
    {
        return $this->amountHonorary;
    }

    public function setAmountHonorary(float $amountHonorary): self
    {
        $this->amountHonorary = $amountHonorary;
        return $this;
    }

    public function getStripePublishableKey(): ?string
    {
        return $this->stripePublishableKey;
    }

    public function setStripePublishableKey(?string $stripePublishableKey): self
    {
        $this->stripePublishableKey = $stripePublishableKey;
        return $this;
    }

    public function getStripeSecretKey(): ?string
    {
        return $this->stripeSecretKey;
    }

    public function setStripeSecretKey(?string $stripeSecretKey): self
    {
        $this->stripeSecretKey = $stripeSecretKey;
        return $this;
    }

    public function getStripeWebhookSecret(): ?string
    {
        return $this->stripeWebhookSecret;
    }

    public function setStripeWebhookSecret(?string $stripeWebhookSecret): self
    {
        $this->stripeWebhookSecret = $stripeWebhookSecret;
        return $this;
    }
} 