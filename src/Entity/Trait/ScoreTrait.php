<?php declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping\Column;

trait ScoreTrait
{
    #[Column(type: "integer", options: ["default" => 0])]
    private int $score = 0;

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score, bool $upvote): void
    {
        $this->score = $upvote ? $score + 1 : $score - 1;
    }
}
