<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUser(string $username): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.user', 'u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException|NoResultException
     */
    public function countPoints(User $user): int
    {
        return (int) $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameter('user', $user)
            ->select('SUM(c.score) as points')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getEntityClass(): string
    {
        return Comment::class;
    }
}
