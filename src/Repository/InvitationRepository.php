<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Invitation;
use Doctrine\Persistence\ManagerRegistry;

class InvitationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function getEntityClass(): string
    {
        return Invitation::class;
    }
}
