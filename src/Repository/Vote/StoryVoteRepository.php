<?php declare(strict_types=1);

namespace App\Repository\Vote;

use App\Entity\Vote\StoryVote;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class StoryVoteRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function getEntityClass(): string
    {
        return StoryVote::class;
    }
}
