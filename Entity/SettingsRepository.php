<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SettingsRepository extends EntityRepository
{
    /**
     * Get the settings for a specific year
     */
    public function getSettingsForYear(int $year): ?Settings
    {
        return $this->findOneBy(['year' => $year]);
    }

    /**
     * Get the most recent year's settings
     */
    public function getMostRecentSettings(): ?Settings
    {
        $query = $this->createQueryBuilder('s')
            ->orderBy('s.year', 'DESC')
            ->setMaxResults(1);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Get all years for which we have settings
     */
    public function getAvailableYears(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s.year')
            ->orderBy('s.year', 'DESC');

        $result = $query->getQuery()->getArrayResult();
        
        return array_column($result, 'year');
    }

    /**
     * Check if we have settings for the current year
     */
    public function hasCurrentYearSettings(): bool
    {
        $currentYear = (int) date('Y');
        
        $count = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.year = :year')
            ->setParameter('year', $currentYear)
            ->getQuery()
            ->getSingleScalarResult();
            
        return (int) $count > 0;
    }

    /**
     * Get subscription amount based on type
     */
    public function getSubscriptionAmount(int $year, string $type = 'full'): float
    {
        $settings = $this->getSettingsForYear($year);
        
        if (!$settings) {
            return 0.0;
        }
        
        switch (strtolower($type)) {
            case 'reduced':
                return $settings->getAmountReduced();
            case 'honorary':
                return $settings->getAmountHonorary();
            case 'full':
            default:
                return $settings->getAmountFull();
        }
    }
} 