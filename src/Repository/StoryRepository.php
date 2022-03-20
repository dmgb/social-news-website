<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Story;
use App\Entity\Tag;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class StoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function findBySearch(string $q): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.title LIKE :query')
            ->andWhere('s.isApproved = 1')
            ->andWhere('s.isDeleted = 0')
            ->setParameter('query', '%' . $q . '%')
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByTag(Tag $tag): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.tags', 't')
            ->andWhere('t = :tag')
            ->andWhere('s.isApproved = 1')
            ->andWhere('s.isDeleted = 0')
            ->setParameter('tag', $tag)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDomain(string $url): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.url LIKE :url')
            ->andWhere('s.isApproved = 1')
            ->andWhere('s.isDeleted = 0')
            ->innerJoin('s.user', 'u')
            ->setParameter('url', '%' . $url . '%')
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.isApproved = 1')
            ->andWhere('s.isDeleted = 0')
            ->setParameter('user', $user)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getEntityClass(): string
    {
        return Story::class;
    }
}
