<?php declare(strict_types=1);

namespace App\Entity\Vote;

use App\Entity\Story;
use App\Entity\Trait\IdTrait;
use App\Repository\Vote\StoryVoteRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Security\Core\User\UserInterface;

#[Entity(repositoryClass: StoryVoteRepository::class)]
#[UniqueConstraint(name: "story_vote_unique", columns: ["story_id", "user_id"])]
class StoryVote
{
    use IdTrait;

    public function __construct(
        #[ManyToOne(targetEntity: "App\Entity\Story", inversedBy: "votes"), JoinColumn(name: "story_id", nullable: false)]
        private Story $story,

        #[ManyToOne(targetEntity: "App\Entity\User", inversedBy: "storyVotes"), JoinColumn(name: "user_id", nullable: false)]
        private UserInterface $user,
    ){}

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
