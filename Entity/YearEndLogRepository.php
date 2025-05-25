<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Entity;

use Doctrine\ORM\EntityRepository;

class YearEndLogRepository extends EntityRepository
{
    /**
     * Get the year-end log for a specific year
     */
    public function getYearEndLog(int $year): ?YearEndLog
    {
        return $this->findOneBy(['year' => $year]);
    }

    /**
     * Get all year-end logs
     */
    public function getAllLogs(): array
    {
        $query = $this->createQueryBuilder('y')
            ->orderBy('y.year', 'DESC');

        return $query->getQuery()->getResult();
    }

    /**
     * Check if year-end process has been run for a specific year
     */
    public function hasYearEndProcess(int $year): bool
    {
        $count = $this->createQueryBuilder('y')
            ->select('COUNT(y.id)')
            ->where('y.year = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getSingleScalarResult();
            
        return (int) $count > 0;
    }

    /**
     * Get the most recent year-end log
     */
    public function getMostRecentLog(): ?YearEndLog
    {
        $query = $this->createQueryBuilder('y')
            ->orderBy('y.year', 'DESC')
            ->setMaxResults(1);

        return $query->getQuery()->getOneOrNullResult();
    }
} 