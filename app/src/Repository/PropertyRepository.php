<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }


  /**
   * Récupère les propriétés en lien avec la recherche soumise.
   * @param SearchData $searchData
   * @return Property[]
   */
    public function findSearch(SearchData $searchData)
    {

        $query = $this
            ->createQueryBuilder('p')
            ->where('p.type = :type')
            ->andWhere('p.category = :categories')
            ->andWhere('p.price >= :minPrice')
            ->andWhere('p.price <= :maxPrice')
            ->setParameters([
                'type' => $searchData->type,
                'categories' => $searchData->categories,
                'minPrice' => $searchData->minPrice,
                'maxPrice' => $searchData->maxPrice
            ])
          ;

        return $query->getQuery()->getResult();
    }
}
