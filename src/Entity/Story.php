<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interface\Normalizable;
use App\Entity\Trait\ScoreTrait;
use App\Entity\Trait\ShortIdTrait;
use App\Entity\Trait\TimestampTrait;
use App\Entity\Trait\IdTrait;
use App\Repository\StoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: StoryRepository::class), HasLifecycleCallbacks]
#[Index(columns: ["short_id"], name: "short_id_idx")]
#[UniqueEntity(fields: "title", message: "Story with this title already exists.")]
#[UniqueEntity(fields: "url", message: "Story with this url already exists.")]
class Story implements Normalizable
{
    use IdTrait;
    use ScoreTrait;
    use ShortIdTrait;
    use TimestampTrait;

    #[Column(type: "string", unique: true)]
    #[Assert\NotBlank(message: "Title can not be blank.")]
    #[Assert\Length(
        min: 2,
        max: 75,
        minMessage: "Title must be at least 2 characters long.",
        maxMessage: "Title must be no longer than 75 characters.",
    )]
    private string $title;

    #[Column(type: "string")]
    private string $slug;

    #[Column(type: "string", unique: true)]
    #[Assert\NotBlank(message: "URL can not be blank.")]
    #[Assert\Length(max: 2000, maxMessage: "URL must be no longer than 2000 characters.")]
    #[Assert\Url(message: "URL is not valid.")]
    private string $url;

    #[Column(type: "boolean", options: ["default" => false])]
    private bool $isApproved= false;

    #[Column(type: "string", nullable: true)]
    #[Assert\Length(
        min: 4,
        max: 255,
        minMessage: "Reason must be at least 4 characters long.",
        maxMessage: "Reason must be no more than 255 characters long.",
        groups: ['approval']
    )]
    private string|null $disapprovedReason;

    #[Column(type: "boolean", options: ["default" => false])]
    private bool $isDeleted = false;

    #[ManyToMany(targetEntity: "Tag", inversedBy: "stories")]
    #[JoinTable(name: "story_tag"), JoinColumn(name: "story_id", referencedColumnName: "id")]
    #[InverseJoinColumn(name: "tag_id", referencedColumnName: "id")]
    #[OrderBy(['name' => 'DESC'])]
    #[Assert\Count(
        min: 1,
        max: 5,
        minMessage: "At least one tag must be selected.",
        maxMessage: "No more than 5 tags must be selected.",
    )]
    private Collection $tags;

    #[OneToMany(mappedBy: "story", targetEntity: "App\Entity\Vote\StoryVote")]
    private Collection $votes;

    #[OneToMany(mappedBy: "story", targetEntity: "App\Entity\Comment",), OrderBy(["createdAt" => "DESC"])]
    private Collection $comments;

    public function __construct(
        #[ManyToOne(targetEntity: "User", inversedBy: "stories"), JoinColumn(name: "user_id", nullable: false)]
        private UserInterface $user,
    )
    {
        $this->createdAt = new \DateTime;
        $this->tags = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    #[PrePersist]
    public function setSlug(): void
    {
        $this->slug = strtolower((new AsciiSlugger())->slug($this->getTitle())->toString());
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): void
    {
        $this->isApproved = $isApproved;
    }

    public function getDissapprovedReason(): string|null
    {
        return $this->disapprovedReason;
    }

    public function setDissapprovedReason(string|null $disapprovedReason): void
    {
        $this->disapprovedReason = $disapprovedReason;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function hasVoteOfUser(?UserInterface $user): ?bool
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
