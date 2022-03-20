<?php declare(strict_types=1);

namespace App\Entity\Vote;

use App\Entity\Comment;
use App\Entity\Trait\IdTrait;
use App\Repository\Vote\CommentVoteRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Security\Core\User\UserInterface;

#[Entity(repositoryClass: CommentVoteRepository::class)]
#[UniqueConstraint(name: "comment_vote_unique", columns: ["comment_id", "user_id"])]
class CommentVote
{
    use IdTrait;

    public function __construct(
        #[ManyToOne(targetEntity: "App\Entity\Comment", inversedBy: "votes"), JoinColumn(name: "comment_id", nullable: false)]
        private Comment $comment,

        #[ManyToOne(targetEntity: "App\Entity\User", inversedBy: "commentVotes"), JoinColumn(name: "user_id", nullable: false)]
        private UserInterface $user,
    ){}

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
