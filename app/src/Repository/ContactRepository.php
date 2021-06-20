<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

     /**
      * @return Contact[] Returns an array of Contact objects
      */
    public function getMyContactsOrdered($user)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.prospect = :user')
            ->setParameter('user', $user)
            ->orderBy('c.desiredDate', 'asc')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Contact[] Returns an array of Contact objects
     */
    public function getContactsOrdered($user)
    {
        $contacts = array();

        $createdRDV = $this->createQueryBuilder('c')
            ->leftJoin('c.property', 'property')
            ->leftJoin('property.owner', 'owner')
            ->where('owner = :user')
            ->andWhere("c.status = 'RDV_CREE'")
            ->orWhere("c.status = 'RDV_NOUVELLE_DATE'")
            ->setParameter('user', $user)
            ->orderBy('c.desiredDate', 'asc')
            ->getQuery()
            ->getResult()
            ;

        $validatedRDV = $this->createQueryBuilder('c')
            ->leftJoin('c.property', 'property')
            ->leftJoin('property.owner', 'owner')
            ->where('owner = :user')
            ->andWhere("c.status = 'RDV_VALIDE'")
            ->setParameter('user', $user)
            ->orderBy('c.desiredDate', 'asc')
            ->getQuery()
            ->getResult()
        ;

        $terminatedRDV = $this->createQueryBuilder('c')
            ->leftJoin('c.property', 'property')
            ->leftJoin('property.owner', 'owner')
            ->where('owner = :user')
            ->andWhere("c.status = 'RDV_TERMINE'")
            ->orWhere("c.status = 'RDV_FERME'")
            ->setParameter('user', $user)
            ->orderBy('c.desiredDate', 'asc')
            ->getQuery()
            ->getResult()
        ;

        array_push( $contacts, $createdRDV,$validatedRDV,$terminatedRDV );

        return $contacts;
    }

    // /**
    //  * @return Contact[] Returns an array of Contact objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
