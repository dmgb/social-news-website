<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use JsonSerializable;

#[Entity(repositoryClass: TagRepository::class)]
class Tag implements JsonSerializable
{
    use IdTrait;

    #[ManyToMany(targetEntity: "Story", mappedBy: "tags")]
    private Collection $stories;

    #[OneToMany(mappedBy: 'tag', targetEntity: TagFilter::class)]
    private Collection $tagFilters;

    public function __construct(
        #[Column(type: "string", unique: true)]
        private readonly string $name,
    )
    {
        $this->stories = new ArrayCollection();
        $this->tagFilters = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStories(): Collection
    {
        return $this->stories;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
