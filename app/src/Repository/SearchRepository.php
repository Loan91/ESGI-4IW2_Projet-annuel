<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Search|null find($id, $lockMode = null, $lockVersion = null)
 * @method Search|null findOneBy(array $criteria, array $orderBy = null)
 * @method Search[]    findAll()
 * @method Search[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Search::class);
    }

    /**
     * @param Property $property
     * @return int|mixed|string
     */
    public function findInterestedUsers(Property $property)
    {
        $query =  $this->createQueryBuilder('p')
            ->select(['IDENTITY(p.searcher), u.email, u.firstname, u.lastname'])
            ->join('p.searcher', 'u', 'with', 'u.id = p.searcher')
        ;

        if(!empty($property->getType()) || $property->getType() !== 'all') {
          $query = $query->where('p.type = :type')->setParameter('type', $property->getType());
        }

        if(!empty($property->getCity())) {
          $query = $query->andWhere('p.city = :city')->setParameter('city', $property->getCity());
        }

        if(!empty($property->getCategory())) {
          $query = $query->andWhere('p.category = :category')->setParameter('category', $property->getCategory());
        }

        if(!empty($property->getPrice())) {
          $query = $query->andWhere('p.minPrice <= :price')->setParameter('price', $property->getPrice());
          $query = $query->andWhere('p.maxPrice >= :price')->setParameter('price', $property->getPrice());
        }

        return $query->getQuery()->getResult();
    }


    /**
     * @return int|mixed|string
     */
    public function findSavedSearchByUser()
    {
        $query =  $this->createQueryBuilder('s')
            ->select('SELECT * from search')
        ;

        return $query->getQuery()->getResult();
    }
}
