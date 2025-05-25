<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Mautic\IntegrationsBundle\Migration\AbstractMigration;

class Version_1_0_0 extends AbstractMigration
{
    protected function isApplicable(Schema $schema): bool
    {
        try {
            return !$schema->hasTable($this->concatPrefix('lodge_subscription_payments'));
        } catch (SchemaException $e) {
            return false;
        }
    }

    protected function up(): void
    {
        // Create the payments table
        $this->addSql("
            CREATE TABLE IF NOT EXISTS `{$this->concatPrefix('lodge_subscription_payments')}` (
                `id` INT AUTO_INCREMENT NOT NULL,
                `contact_id` INT NOT NULL,
                `amount` DECIMAL(10, 2) NOT NULL,
                `payment_date` DATETIME NOT NULL,
                `payment_method` VARCHAR(50) NOT NULL,
                `year` INT NOT NULL,
                `notes` LONGTEXT DEFAULT NULL,
                `transaction_id` VARCHAR(255) DEFAULT NULL,
                `is_current` TINYINT(1) NOT NULL DEFAULT 1,
                `is_arrears` TINYINT(1) NOT NULL DEFAULT 0,
                `date_created` DATETIME NOT NULL,
                `created_by` INT DEFAULT NULL,
                `date_modified` DATETIME DEFAULT NULL,
                `modified_by` INT DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX `{$this->concatPrefix('lodge_payment_contact')}` (`contact_id`),
                INDEX `{$this->concatPrefix('lodge_payment_date')}` (`payment_date`),
                INDEX `{$this->concatPrefix('lodge_payment_year')}` (`year`),
                INDEX `{$this->concatPrefix('lodge_payment_transaction')}` (`transaction_id`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ");

        // Create the subscription settings table
        $this->addSql("
            CREATE TABLE IF NOT EXISTS `{$this->concatPrefix('lodge_subscription_settings')}` (
                `id` INT AUTO_INCREMENT NOT NULL,
                `year` INT NOT NULL,
                `amount_full` DECIMAL(10, 2) NOT NULL,
                `amount_reduced` DECIMAL(10, 2) NOT NULL,
                `amount_honorary` DECIMAL(10, 2) DEFAULT 0.00,
                `stripe_publishable_key` VARCHAR(255) DEFAULT NULL,
                `stripe_secret_key` VARCHAR(255) DEFAULT NULL,
                `stripe_webhook_secret` VARCHAR(255) DEFAULT NULL,
                `date_created` DATETIME NOT NULL,
                `created_by` INT DEFAULT NULL,
                `date_modified` DATETIME DEFAULT NULL,
                `modified_by` INT DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `{$this->concatPrefix('lodge_subscription_year')}` (`year`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ");

        // Create the year-end processing log table
        $this->addSql("
            CREATE TABLE IF NOT EXISTS `{$this->concatPrefix('lodge_subscription_yearend_log')}` (
                `id` INT AUTO_INCREMENT NOT NULL,
                `year` INT NOT NULL,
                `processed_date` DATETIME NOT NULL,
                `processed_by` INT NOT NULL,
                `contacts_processed` INT NOT NULL,
                `fields_created` LONGTEXT DEFAULT NULL,
                `log` LONGTEXT DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX `{$this->concatPrefix('lodge_yearend_year')}` (`year`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ");
    }
} 