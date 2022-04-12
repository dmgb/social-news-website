<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\TagFilter;
use Doctrine\Persistence\ManagerRegistry;

class TagFilterRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function save(object $filters): void
    {
        foreach ($filters as $filter) {
            $this->_em->persist($filter);
        }

        $this->_em->flush();
    }

    public function remove(object $filters): void
    {
        $ids = $filters->map(fn($filter) => $filter->getId())->toArray();
        $query = $this->_em->createQuery('delete from App\Entity\TagFilter tf where tf.id IN (:ids)');
        $query->setParameter('ids', $ids);
        $query->execute();
    }

    public function getEntityClass(): string
    {
        return TagFilter::class;
    }
}
