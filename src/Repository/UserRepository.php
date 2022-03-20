<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->save($user);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findMostCommonStoryTag(User $user): ?Tag
    {
        return $this->_em->createQueryBuilder()
            ->select('t')
            ->from(Tag::class, 't')
            ->innerJoin('t.stories', 's')
            ->where('s.user = :user')
            ->groupBy('t.id')
            ->orderBy('COUNT(t)', 'DESC')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getEntityClass(): string
    {
        return User::class;
    }
}
