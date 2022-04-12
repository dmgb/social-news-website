<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use App\Repository\TagFilterRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Security\Core\User\UserInterface;

#[Entity(repositoryClass: TagFilterRepository::class)]
#[UniqueConstraint(name: "tag_user_unique", columns: ["tag_id", "user_id"])]
class TagFilter
{
    use IdTrait;

    public function __construct(
        #[ManyToOne(targetEntity: Tag::class, inversedBy: "tagFilters"), JoinColumn(name: "tag_id", nullable: false)]
        private Tag $tag,
        #[ManyToOne(targetEntity: User::class, inversedBy: "tagFilters"), JoinColumn(name: "user_id", nullable: false)]
        private UserInterface $user,
    ){}

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
