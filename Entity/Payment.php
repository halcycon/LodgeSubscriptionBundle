<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;

class Payment extends FormEntity
{
    private int $id;
    private int $contactId;
    private float $amount;
    private \DateTime $paymentDate;
    private string $paymentMethod;
    private int $year;
    private ?string $notes = null;
    private ?string $transactionId = null;
    private bool $isCurrent = true;
    private bool $isArrears = false;

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('lodge_subscription_payments')
            ->setCustomRepositoryClass(PaymentRepository::class);

        $builder->addId();

        $builder->createField('contactId', 'integer')
            ->columnName('contact_id')
            ->build();

        $builder->createField('amount', 'decimal')
            ->precision(10)
            ->scale(2)
            ->build();

        $builder->createField('paymentDate', 'datetime')
            ->columnName('payment_date')
            ->build();

        $builder->createField('paymentMethod', 'string')
            ->columnName('payment_method')
            ->length(50)
            ->build();

        $builder->createField('year', 'integer')
            ->build();

        $builder->createField('notes', 'text')
            ->nullable()
            ->build();

        $builder->createField('transactionId', 'string')
            ->columnName('transaction_id')
            ->nullable()
            ->length(255)
            ->build();

        $builder->createField('isCurrent', 'boolean')
            ->columnName('is_current')
            ->build();

        $builder->createField('isArrears', 'boolean')
            ->columnName('is_arrears')
            ->build();

        $builder->addIndex(['contact_id'], 'lodge_payment_contact');
        $builder->addIndex(['payment_date'], 'lodge_payment_date');
        $builder->addIndex(['year'], 'lodge_payment_year');
        $builder->addIndex(['transaction_id'], 'lodge_payment_transaction');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContactId(): int
    {
        return $this->contactId;
    }

    public function setContactId(int $contactId): self
    {
        $this->contactId = $contactId;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getPaymentDate(): \DateTime
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTime $paymentDate): self
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(?string $transactionId): self
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function isCurrent(): bool
    {
        return $this->isCurrent;
    }

    public function setIsCurrent(bool $isCurrent): self
    {
        $this->isCurrent = $isCurrent;
        return $this;
    }

    public function isArrears(): bool
    {
        return $this->isArrears;
    }

    public function setIsArrears(bool $isArrears): self
    {
        $this->isArrears = $isArrears;
        return $this;
    }
} 