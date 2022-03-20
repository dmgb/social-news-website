<?php declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class InvitationEmail extends Constraint
{
    public string $emailRegisteredMessage = 'Email is already registered.';
    public string $invitationSentMessage = 'Invitation is already sent.';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
