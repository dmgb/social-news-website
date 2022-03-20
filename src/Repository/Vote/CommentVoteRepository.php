<?php declare(strict_types=1);

namespace App\Repository\Vote;

use App\Entity\Vote\CommentVote;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentVoteRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function getEntityClass(): string
    {
        return CommentVote::class;
    }
}
