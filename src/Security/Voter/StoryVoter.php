<?php declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Story;
use ReflectionClass;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class StoryVoter extends Voter
{
    const SHOW = 'show';
    const EDIT = 'edit';
    const APPROVE = 'approve';
    const DISAPPROVE = 'disapprove';
    const DELETE = 'delete';
    const UNDELETE = 'undelete';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, $this->getConstants()) && $subject instanceof Story;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        $story = $subject;

        return match ($attribute) {
            self::SHOW => $this->canView($story, $user),
            self::EDIT => $story->getUser() === $user || $user->isAdmin(),
            self::APPROVE, self::DISAPPROVE, self::DELETE, self::UNDELETE => $user->isAdmin(),
            default => false,
        };
    }

    private function canView(Story $story, UserInterface $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($story->isDeleted() || !$story->isApproved()) {
            return false;
        }

        return true;
    }

    private function getConstants(): array
    {
        $reflectionClass = new ReflectionClass($this);

        return $reflectionClass->getConstants();
    }
}
