<?php declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ResetPasswordType;
use App\Form\ResetPasswordRequestType;
use App\Mailer\UserMailer;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class PasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private UserMailer $mailer,
        private UserRepository $userRepository,
    ){}

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/reset-password/request', name: 'request_password_reset')]
    public function requestReset(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail($form->get('email')->getData());
        }

        return $this->render('security/password/reset/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/check-email', name: 'check_email_after_reset_password_request')]
    public function checkEmail(): Response
    {
        if (null === ($token = $this->getTokenObjectFromSession())) {
            $token = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('security/password/reset/check_email.html.twig', [
            'token' => $token,
        ]);
    }

    #[Route('/reset-password/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, string $token = null): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->render('error.html.twig', ['message' => $e->getReason()]);
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordHelper->removeResetRequest($token);
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($hashedPassword);
            $this->userRepository->save($user);
            $this->cleanSessionAfterReset();
            $this->addFlash('success', 'Password successfully changed. Please log in.');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/password/reset/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function processSendingPasswordResetEmail(string $emailFormData): Response
    {
        $user = $this->userRepository->findOneBy(['email' => $emailFormData]);

        if (!$user) {
            return $this->redirectToRoute('check_email_after_reset_password_request');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->render('error.html.twig', ['message' => $e->getReason()]);
        }

        $this->mailer->sendPasswordResetLink($user, $resetToken);
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('check_email_after_reset_password_request');
    }

    #[Route('/change-password', name: 'change-password', methods: ['GET', 'POST'])]
    public function change(Request $request, UserPasswordHasherInterface $passwordHasher): RedirectResponse|Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);
            $this->userRepository->save($user);
            $this->addFlash('success', 'Password is successfully changed.');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/password/change.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
