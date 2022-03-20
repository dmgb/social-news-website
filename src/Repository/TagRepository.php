<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Persistence\ManagerRegistry;

class TagRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function getEntityClass(): string
    {
        return Tag::class;
    }
}
