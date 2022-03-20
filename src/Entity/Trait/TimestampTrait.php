<?php declare(strict_types=1);

namespace App\Entity\Trait;

use DateTimeInterface;
use Doctrine\ORM\Mapping\Column;

trait TimestampTrait
{
    #[Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTimeInterface $createdAt;

    #[Column(type: "datetime", nullable: true)]
    private DateTimeInterface $updatedAt;

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
