<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Property;
use App\Entity\User;
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
        public function findSearch(SearchData $searchData): array
        {
            $query = $this->createQueryBuilder('p');

            if (!empty($searchData->type) && $searchData->type !== 'all') {
                $query = $query->where('p.type = :type')->setParameter('type', $searchData->type);
            }

            if (!empty($searchData->city)) {
              $query = $query->andWhere('p.city LIKE :city')->setParameter('city', '%'. $searchData->city . '%');
            }

            if (!empty($searchData->categories)) {
                $query = $query->andWhere('p.category = :categories')->setParameter('categories', $searchData->categories);
            }

            if (!empty($searchData->minPrice)) {
                $query = $query->andWhere('p.price >= :minPrice')->setParameter('minPrice', $searchData->minPrice);
            }

            if (!empty($searchData->maxPrice)) {
                $query = $query->andWhere('p.price <= :maxPrice')->setParameter('maxPrice', $searchData->maxPrice);
            }

            return $query->getQuery()->getResult();
        }

        public function getUserProperties(User $user)
        {
            return $this->createQueryBuilder('p')
                ->where('p.owner = :user')
                ->setParameter('user', $user)
                ->orderBy('p.id')
                ->getQuery()->getResult();
        }
}
