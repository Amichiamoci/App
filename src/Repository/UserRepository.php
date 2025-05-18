<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(registry: $registry, entityClass: User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                message: sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword(password: $newHashedPassword);
        $this->getEntityManager()->persist(object: $user);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Finds all the users with the specified role
     * @param string $role The role we want to search for (case insensitive)
     * @return array
     */
    public function findByRole(string $role): array
    {
        // Filter the string for annoying characters
        $role = strtoupper(string: $role);
        $role = str_replace(search: '"', replace: '', subject: $role);
        $role = trim(string: $role);

        // Role everyone has -> it's not stored in db
        if ($role === 'ROLE_USER')
        {
            return $this->findAll();
        }

        $builder = $this ->createQueryBuilder(alias: 'u');
        $query = $builder
            ->Where($builder->expr()->like(x: 'u.roles', y: ':role'))
            //->andWhere(...)
            ->setParameter(key: 'role', value: '%'.$role.'%')
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Finds all users that have logged in at least once with an external provider
     * @return array
     */
    public function findOauthUsers(): array
    {
        return $this->findByRole(role: User::EXTERNAL_PROVIDER);
    }
}
