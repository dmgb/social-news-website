<?php declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

trait IdTrait
{
    #[Id, Column(type: "integer", options: ["unsigned" => true]), GeneratedValue(strategy: "AUTO")]
    private int $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
