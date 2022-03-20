<?php declare(strict_types=1);

namespace App\Validator;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class InvitationEmailValidator extends ConstraintValidator
{
    public function __construct(
        private ObjectManager $manager,
    ){}

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof InvitationEmail) {
            throw new UnexpectedTypeException($constraint, InvitationEmail::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $user = $this->manager->getRepository(User::class)->findOneBy(['email' => $value]);
        $invitation = $user ? null : $this->manager->getRepository(Invitation::class)->findOneBy(['email' => $value, 'isUsed' => false]);
        if ($user || $invitation) {
            $message = $user ? $constraint->emailRegisteredMessage : $constraint->invitationSentMessage;
            $this->context->buildViolation($message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
