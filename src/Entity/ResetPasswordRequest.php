<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use App\Repository\ResetPasswordRequestRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

#[Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;
    use IdTrait;

    public function __construct(
        #[ManyToOne(targetEntity: "User"), JoinColumn(name: "user_id", nullable: false)]
        private UserInterface $user,
        DateTimeInterface $expiresAt,
        string $selector,
        string $hashedToken,
    )
    {
        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
