<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaymentRepository extends EntityRepository
{
    /**
     * Get a list of payments for a specific contact
     */
    public function getContactPayments(int $contactId, int $limit = 10, int $offset = 0): array
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.contactId = :contactId')
            ->setParameter('contactId', $contactId)
            ->orderBy('p.paymentDate', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    /**
     * Get all payments for a specific year
     */
    public function getYearPayments(int $year): array
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.year = :year')
            ->setParameter('year', $year)
            ->orderBy('p.paymentDate', 'DESC');

        return $query->getQuery()->getResult();
    }

    /**
     * Get paginated list of all payments
     */
    public function getPaymentList(array $filters = [], int $page = 1, int $limit = 30): Paginator
    {
        $query = $this->createQueryBuilder('p');

        if (!empty($filters['contact_id'])) {
            $query->andWhere('p.contactId = :contactId')
                ->setParameter('contactId', $filters['contact_id']);
        }

        if (!empty($filters['year'])) {
            $query->andWhere('p.year = :year')
                ->setParameter('year', $filters['year']);
        }

        if (!empty($filters['is_current'])) {
            $query->andWhere('p.isCurrent = :isCurrent')
                ->setParameter('isCurrent', $filters['is_current']);
        }

        if (!empty($filters['is_arrears'])) {
            $query->andWhere('p.isArrears = :isArrears')
                ->setParameter('isArrears', $filters['is_arrears']);
        }

        if (!empty($filters['payment_method'])) {
            $query->andWhere('p.paymentMethod = :paymentMethod')
                ->setParameter('paymentMethod', $filters['payment_method']);
        }

        if (!empty($filters['date_from'])) {
            $query->andWhere('p.paymentDate >= :dateFrom')
                ->setParameter('dateFrom', new \DateTime($filters['date_from']));
        }

        if (!empty($filters['date_to'])) {
            $query->andWhere('p.paymentDate <= :dateTo')
                ->setParameter('dateTo', new \DateTime($filters['date_to']));
        }

        $query->orderBy('p.paymentDate', 'DESC');

        // Calculate offset for pagination
        $offset = ($page - 1) * $limit;
        $query->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query->getQuery());
    }

    /**
     * Find payment by transaction ID
     */
    public function findByTransactionId(string $transactionId): ?Payment
    {
        return $this->findOneBy(['transactionId' => $transactionId]);
    }

    /**
     * Get sum of payments for a contact in a specific year
     */
    public function getContactYearPaymentsSum(int $contactId, int $year): float
    {
        $query = $this->createQueryBuilder('p')
            ->select('SUM(p.amount) as total')
            ->where('p.contactId = :contactId')
            ->andWhere('p.year = :year')
            ->setParameter('contactId', $contactId)
            ->setParameter('year', $year);

        $result = $query->getQuery()->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }
} 