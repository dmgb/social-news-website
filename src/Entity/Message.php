<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use App\Entity\Trait\ShortIdTrait;
use App\Entity\Trait\TimestampTrait;
use App\Enum\MessageState;
use App\Repository\MessageRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[Index(columns: ["short_id"], name: "short_id_idx")]
class Message
{
    use IdTrait;
    use ShortIdTrait;
    use TimestampTrait;

    #[Column(type: "string")]
    #[Assert\NotBlank(message: "Subject can not be blank.")]
    #[Assert\Length(
        min: 2,
        max: 75,
        minMessage: "Subject should be at least 2 characters long.",
        maxMessage: "Title should be no longer than 75 characters.",
    )]
    private string $subject;

    #[ORM\Column(length: 5000)]
    #[Assert\NotBlank(message: "Message should have a body")]
    #[Assert\Length(
        min: 15,
        max: 5000,
        minMessage: "Body must be at least 15 characters long.",
        maxMessage: "Body must be no longer than 5000 characters.",
    )]
    private string $body;

    #[OneToMany(mappedBy: "parent", targetEntity: "App\Entity\Message"), OrderBy(["createdAt" => "DESC"])]
    private Collection $children;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'sendMessages'), JoinColumn(nullable: false)]
        private User $sender,
        #[ORM\ManyToOne(inversedBy: 'receivedMessages'), JoinColumn(nullable: false)]
        private User $receiver,
        #[ManyToOne(targetEntity: "App\Entity\Message", inversedBy: "children"), JoinColumn(name: "parent_id")]
        private readonly ?Message $parent = null,
        #[Column(type: 'string', enumType: MessageState::class)]
        public $state = MessageState::UNREAD,
    )
    {
        $this->createdAt = new DateTime();
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $title): void
    {
        $this->subject = $title;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getSender(): User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getParent(): ?Message
    {
        return $this->parent;
    }

    public function isParent(): bool
    {
        return null === $this->parent;
    }

    public function getState(): MessageState
    {
        return $this->state;
    }

    public function setState(MessageState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }
}
