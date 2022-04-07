<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interface\Normalizable;
use App\Entity\Trait\IdTrait;
use App\Entity\Trait\ScoreTrait;
use App\Entity\Trait\ShortIdTrait;
use App\Entity\Trait\TimestampTrait;
use App\Repository\CommentRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: CommentRepository::class)]
#[Index(columns: ["short_id"], name: "short_id_idx")]
class Comment implements Normalizable
{
    use IdTrait;
    use ShortIdTrait;
    use TimestampTrait;
    use ScoreTrait;

    #[Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "This field can not be blank.")]
    #[Assert\Length(
        min: 15,
        max: 255,
        minMessage: "Comment must be at least 15 characters long.",
        maxMessage: "Comment must be no longer than 255 characters.",
    )]
    private string $body;

    #[OneToMany(mappedBy: "parent", targetEntity: "App\Entity\Comment"), OrderBy(["createdAt" => "DESC"])]
    private Collection $children;

    #[OneToMany(mappedBy: "comment", targetEntity: "App\Entity\Vote\CommentVote")]
    private Collection $votes;

    public function __construct(
        #[ManyToOne(targetEntity: "App\Entity\Story", inversedBy: "comments"), JoinColumn(name: "story_id", nullable: false)]
        private Story $story,
        #[ManyToOne(targetEntity: "App\Entity\User", inversedBy: "comments"), JoinColumn(name: "user_id", nullable: false)]
        private UserInterface $user,
        #[ManyToOne(targetEntity: "App\Entity\Comment", inversedBy: "children"), JoinColumn(name: "parent_id")]
        private ?Comment $parent,
    )
    {
        $this->createdAt = new DateTime();
        $this->children = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getStory(): Story
    {
        return $this->story;
    }

    public function getParent(): ?Comment
    {
        return $this->parent;
    }

    public function isParent(): bool
    {
        return null === $this->parent;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function hasVoteOfCurrentUser(?UserInterface $user): ?bool
    {
        if (!$user) return null;
        foreach ($this->votes as $vote) {
            if ($vote->getUser() === $user) {
                return true;
            }
        }

        return false;
    }
}
