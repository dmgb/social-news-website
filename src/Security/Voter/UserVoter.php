<?php declare(strict_types=1);

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    const INVITE = 'invite';
    const BAN = 'ban';
    const UNBAN = 'unban';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::INVITE, self::BAN, self::UNBAN]) && $subject instanceof UserInterface;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $appUser = $token->getUser();
        if (!$appUser instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::INVITE, self::BAN, self::UNBAN => $appUser->isAdmin(),
            default => false,
        };
    }
}
