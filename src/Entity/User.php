<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use App\Entity\Trait\TimestampTrait;
use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: "username", message: "Username entered already in use.")]
#[UniqueEntity(fields: "email", message: "Email entered already in use.")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use IdTrait;
    use TimestampTrait;

    const BANNED_USERNAMES = ['admin', 'administrator', 'contact', 'help', 'moderator', 'security', 'support'];
    const MIN_KARMA_TO_SUBMIT_STORIES = -3;

    #[Column(type: "string", unique: true)]
    #[Assert\Length(
        min: 2,
        max: 16,
        minMessage: "Username must be at least 2 characters long.",
        maxMessage: "Username must be no more than 16 characters long.",
    )]
    #[Assert\Expression("this.isUsernameValid(value)")]
    private string $username;

    #[Column(type: "string", unique: true)]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    private string $email;

    #[Column(type: "string")]
    private string $password;

    #[Assert\Length(
        min: 8,
        max: 4096,
        minMessage: "Password must be at least {{ limit }} characters long.",
    )]
    #[Assert\Regex(
        pattern: '/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])/',
        message: 'Password must include both lower and upper case characters, and at least one number.',
    )]
    private string $plainPassword;

    #[Column(type: "string")]
    private string $avatarPath;

    #[Column(type: "json")]
    private array $roles = [];

    #[Column(type: "integer", options: ["default" => 0])]
    private int $karma = 0;

    #[Column(type: "boolean", options: ["default" => false])]
    private bool $isBanned = false;

    #[Column(type: "string", nullable: true)]
    #[Assert\Length(
        min: 7,
        max: 255,
        minMessage: "Reason must be at least 7 characters long.",
        maxMessage: "Reason must be no more than 255 characters long.",
        groups: ['ban']
    )]
    private string|null $bannedReason;

    #[Column(type: "datetime", nullable: true)]
    private DateTimeInterface|null $forceReloginAt;

    #[OneToMany(mappedBy: "user", targetEntity: "Story")]
    private Collection $stories;

    #[OneToMany(mappedBy: "user", targetEntity: "Comment")]
    private Collection $comments;

    #[OneToMany(mappedBy: "user", targetEntity: "App\Entity\Vote\StoryVote")]
    private Collection $storyVotes;

    #[OneToMany(mappedBy: "user", targetEntity: "App\Entity\Vote\CommentVote")]
    private Collection $commentVotes;

    #[OneToMany(mappedBy: 'user', targetEntity: TagFilter::class)]
    private Collection $tagFilters;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->stories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->storyVotes = new ArrayCollection();
        $this->commentVotes = new ArrayCollection();
        $this->tagFilters = new ArrayCollection();
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function isUsernameValid(string $username): bool
    {
        return !in_array(strtolower($username), self::BANNED_USERNAMES);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getAvatarPath(): string
    {
        return $this->avatarPath;
    }

    public function setAvatarPath(string $avatarPath): void
    {
        $this->avatarPath = $avatarPath;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getKarma(): int
    {
        return $this->karma;
    }

    public function setKarma(int $karma, bool $upvote): void
    {
        $this->karma = $upvote ? $karma + 1 : $karma - 1;
    }

    public function getAverageKarma(): float|int
    {
        return 0 === $this->karma ? 0 : round($this->karma / (count($this->stories) + count($this->comments)), 2);
    }

    public function isBanned(): bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): void
    {
        $this->isBanned = $isBanned;
    }

    public function getBannedReason(): string|null
    {
        return $this->bannedReason;
    }

    public function setBannedReason(string|null $bannedReason): void
    {
        $this->bannedReason = $bannedReason;
    }

    public function getForceReloginAt(): DateTimeInterface|null
    {
        return $this->forceReloginAt;
    }

    public function setForceReloginAt(DateTimeInterface $forceReloginAt): void
    {
        $this->forceReloginAt = $forceReloginAt;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->roles);
    }

    public function getStories(): Collection
    {
        return $this->stories;
    }

    public function getApprovedStoriesCount(): int
    {
        $s = count($this->stories);
        return $this->stories->filter(fn($story) => $story->isApproved() && !$story->isDeleted())->count();
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getStoryVotes(): Collection
    {
        return $this->storyVotes;
    }

    public function getCommentVotes(): Collection
    {
        return $this->commentVotes;
    }

    public function getTagFilters(): Collection
    {
        return $this->tagFilters;
    }

    public function canSubmitStories(): bool
    {
        return $this->getKarma() >= self::MIN_KARMA_TO_SUBMIT_STORIES;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
    }

    public function __toString(): string
    {
        return $this->getUserIdentifier();
    }
}
