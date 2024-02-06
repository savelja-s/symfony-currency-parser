<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use App\Entity\ExchangeRateHistory;
use App\Enum\CurrencyEnum;
use App\Enum\ExchangeRateStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExchangeRate>
 *
 * @method ExchangeRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExchangeRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExchangeRate[]    findAll()
 * @method ExchangeRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    /**
     * @param  CurrencyEnum[]  $currencies
     * @return array<CurrencyEnum,ExchangeRate>
     * @throws QueryException
     */
    public function findCurrentByCurrency(array $currencies): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.status = :status')
            ->setParameter('status', ExchangeRateStatusEnum::Current)
            ->andWhere('e.currency IN (:currencies)')
            ->setParameter('currencies', array_column($currencies, 'name'))
            ->setMaxResults(count($currencies))
            ->indexBy('e', 'e.currency')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  ExchangeRate  $exchangeRate
     * @param  ExchangeRateHistory[]  $currencyHistories
     * @param  ExchangeRate|null  $newExchangeRate
     * @return void
     */
    public function saveExchangeRates(
        ExchangeRate $exchangeRate,
        array $currencyHistories,
        ?ExchangeRate $newExchangeRate
    ): void {
        $em = $this->getEntityManager();
        $entityForAdd = $exchangeRate;
        if ($newExchangeRate) {
            $em->persist($newExchangeRate);
            $entityForAdd = $newExchangeRate;
        }
        foreach ($currencyHistories as $history) {
            $entityForAdd->addExchangeRateHistory($history);
        }
        $em->flush();
    }
}
