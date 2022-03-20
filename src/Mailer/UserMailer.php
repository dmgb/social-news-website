<?php declare(strict_types=1);

namespace App\Mailer;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class UserMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private string $senderEmail,
    ){}

    /**
     * @throws TransportExceptionInterface
     */
    public function sendUserInvitationLink(string $email, string $url): void
    {
        $email = (new TemplatedEmail())
            ->from($this->senderEmail)
            ->to($email)
            ->subject('You are invited to join snw')
            ->htmlTemplate('email/user_invitation.html.twig')
            ->context([
                'url' => $url,
            ]);

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendPasswordResetLink(User $user, ResetPasswordToken $token): void
    {
        $email = (new TemplatedEmail())
            ->from($this->senderEmail)
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('security/password/reset/email.html.twig')
            ->context([
                'token' => $token,
            ]);

        $this->mailer->send($email);
    }
}
