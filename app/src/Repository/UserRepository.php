<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, User::class);
        $this->paginator = $paginator;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user
     * @param string $newEncodedPassword
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Returns the total count of users in database
     * 
     * @return int Total count of users
     */
    public function getTotalCount(): int
    {
        return $this->_em->createQueryBuilder()
            ->select('count(u)')
            ->from("App\Entity\User", "u")
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Returns the count of new users for this month
     * 
     * @return int Count of new users
     */
    public function getUserCountRegisteredThisMonth(): int
    {
        $conn = $this->_em->getConnection();
        $sql = "SELECT COUNT(*) FROM immo.user_account
        WHERE created_at > date_trunc('month', CURRENT_DATE)
            AND created_at < date_trunc('month', CURRENT_DATE + INTERVAL '1 month')";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchOne();
    }

    /**
     * Return the count of user by month on a year
     * 
     * @param string $year You can select a year or let the default current year value
     */
    public function getUsersOnYearByMonths(string $year = 'CURRENT_YEAR'): array
    {
        if ($year == 'CURRENT_YEAR') {  
            $year = (new DateTime('now'))->format('Y');
        }

        /** @var int[] $userByMonths Nombre d'utilisateurs par mois allant de janvier à décembre */
        $userByMonths = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        $conn = $this->_em->getConnection();
        $sql = "SELECT COUNT(*) as nb_users, EXTRACT(MONTH FROM created_at) as month
            FROM immo.user_account
            WHERE EXTRACT(YEAR FROM created_at) = '2021'
            GROUP BY EXTRACT(MONTH FROM created_at)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        // Remplit le tableau du nombre d'utilisateurs pour les mois en ayant (la bdd ne renvoi que les mois où il y en a minimum 1)
        foreach ($result as $value) {
            $index = (int) $value['month'] - 1;
            $userByMonths[$index] = $value['nb_users'];
        }

        return $userByMonths;
    }

    /**
     * Get a paginator to show progressively all the users
     * 
     * @param Request $request The current http request
     * @param int $limitPerPage The limit of element per page
     * @param string $pageName The name of the get argument which set the current page
     * 
     * @return SlidingPagination
     */
    public function getUsersPaginated(Request $request, int $limitPerPage = 6, string $pageName = 'page'): SlidingPagination
    {
        $builder = $this->_em->createQueryBuilder()
            ->select('u')
            ->from('App\Entity\User', 'u')
            ->orderBy('u.id');
        return $this->paginator->paginate(
            $builder, /* query NOT result */
            $request->query->getInt($pageName, 1), /*page number*/
            $limitPerPage
        );
    }
}
