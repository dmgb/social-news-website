<?php declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class InvalidInvitationTokenException extends AuthenticationException
{
    public function getReason(): string
    {
        return 'Invitation token is invalid.';
    }
}
