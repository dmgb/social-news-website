<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToMany;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;

#[Entity(repositoryClass: TagRepository::class)]
class Tag implements JsonSerializable
{
    use IdTrait;

    #[ManyToMany(targetEntity: "Story", mappedBy: "tags")]
    private Collection $stories;

    public function __construct(
        #[Column(type: "string", unique: true)]
        private string $name,
    )
    {
        $this->stories = new ArrayCollection();
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
}
