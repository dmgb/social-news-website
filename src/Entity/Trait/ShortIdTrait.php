<?php declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping\Column;

trait ShortIdTrait
{
    #[Column(type: "string", length: 10, unique: true)]
    private string $shortId;

    public function getShortId(): ?string
    {
        return $this->shortId;
    }

    public function setShortId(string $shortId): void
    {
        $this->shortId = $shortId;
    }
}
