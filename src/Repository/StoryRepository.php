<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Story;
use App\Entity\Tag;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class StoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function findAll(?UserInterface $user = null)
    {
        $qb = $this->createQueryBuilder('s');
        $this->addTagFilters($qb, $user);
        $this->addApprovedClause($qb);

        return $qb
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(string $q, ?UserInterface $user = null): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->andWhere('s.title LIKE :query');
        $this->addTagFilters($qb, $user);
        $this->addApprovedClause($qb);

        return $qb
            ->setParameter('query', '%'.$q.'%')
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByTag(Tag $tag): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->innerJoin('s.tags', 't')->andWhere('t = :tag');
        $this->addApprovedClause($qb);

        return $qb
            ->setParameter('tag', $tag)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDomain(string $url, ?UserInterface $user = null): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->andWhere('s.url LIKE :url');
        $this->addTagFilters($qb, $user);
        $this->addApprovedClause($qb);

        return $qb
            ->innerJoin('s.user', 'u')
            ->setParameter('url', '%'.$url.'%')
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUser(UserInterface $user, ?UserInterface $appUser = null): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->andWhere('s.user = :user');
        $this->addTagFilters($qb, $appUser);
        $this->addApprovedClause($qb);

        return $qb
            ->setParameter('user', $user)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    private function addTagFilters(QueryBuilder $qb, ?UserInterface $user = null): void
    {
        if ($user && !$user->getTagFilters()->isEmpty()) {
            $tags = $user->getTagFilters()->map(fn($tagFilter) => $tagFilter->getTag());
            $qb
                ->innerJoin('s.tags', 't')
                ->andWhere(':tags NOT MEMBER OF s.tags')
                ->setParameter('tags', $tags);
        }
    }

    private function addApprovedClause(QueryBuilder $qb): void
    {
        $qb->andWhere('s.isApproved = 1')->andWhere('s.isDeleted = 0');
    }

    public function getEntityClass(): string
    {
        return Story::class;
    }
}
