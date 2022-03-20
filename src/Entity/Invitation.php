<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use App\Validator as InvitationAssert;
use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    use IdTrait;

    #[Column(type: "boolean")]
    private bool $isUsed = false;

    public function __construct(
        #[Column(type: "string")]
        #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
        #[InvitationAssert\InvitationEmail]
        private string $email,
        #[Column(type: "string")]
        private string $token,
    ){}

    public function setIsUsed(bool $isUsed): void
    {
        $this->isUsed = $isUsed;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
