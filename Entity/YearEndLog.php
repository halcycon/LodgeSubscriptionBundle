<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;

class YearEndLog extends FormEntity
{
    private int $id;
    private int $year;
    private \DateTime $processedDate;
    private int $processedBy;
    private int $contactsProcessed;
    private ?string $fieldsCreated = null;
    private ?string $log = null;

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('lodge_subscription_yearend_log')
            ->setCustomRepositoryClass(YearEndLogRepository::class);

        $builder->addId();

        $builder->createField('year', 'integer')
            ->build();

        $builder->createField('processedDate', 'datetime')
            ->columnName('processed_date')
            ->build();

        $builder->createField('processedBy', 'integer')
            ->columnName('processed_by')
            ->build();

        $builder->createField('contactsProcessed', 'integer')
            ->columnName('contacts_processed')
            ->build();

        $builder->createField('fieldsCreated', 'text')
            ->columnName('fields_created')
            ->nullable()
            ->build();

        $builder->createField('log', 'text')
            ->nullable()
            ->build();

        $builder->addIndex(['year'], 'lodge_yearend_year');
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

    public function getProcessedDate(): \DateTime
    {
        return $this->processedDate;
    }

    public function setProcessedDate(\DateTime $processedDate): self
    {
        $this->processedDate = $processedDate;
        return $this;
    }

    public function getProcessedBy(): int
    {
        return $this->processedBy;
    }

    public function setProcessedBy(int $processedBy): self
    {
        $this->processedBy = $processedBy;
        return $this;
    }

    public function getContactsProcessed(): int
    {
        return $this->contactsProcessed;
    }

    public function setContactsProcessed(int $contactsProcessed): self
    {
        $this->contactsProcessed = $contactsProcessed;
        return $this;
    }

    public function getFieldsCreated(): ?string
    {
        return $this->fieldsCreated;
    }

    public function setFieldsCreated(?string $fieldsCreated): self
    {
        $this->fieldsCreated = $fieldsCreated;
        return $this;
    }

    public function getLog(): ?string
    {
        return $this->log;
    }

    public function setLog(?string $log): self
    {
        $this->log = $log;
        return $this;
    }
} 