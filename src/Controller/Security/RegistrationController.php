<?php declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use App\Security\InvalidInvitationTokenException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    public function __construct(
        private InvitationRepository $invitationRepository,
        private UserRepository $userRepository,
    ){}

    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): RedirectResponse|Response
    {
        $token = $request->get('token');
        if (null === $token) {
            throw new NotFoundHttpException();
        }

        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        try {
            $invitation = $this->invitationRepository->findOneBy(['token' => $token, 'isUsed' => false]);
            if (null === $invitation) {
                throw new InvalidInvitationTokenException();
            }
        } catch (InvalidInvitationTokenException $e) {
            return $this->render('error.html.twig', ['message' => $e->getReason()]);
        }

        $user = new User();
        $user->setEmail($invitation->getEmail());
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);
            $this->userRepository->save($user);
            $invitation->setIsUsed(true);
            $this->invitationRepository->save($invitation);
            $this->addFlash('success', 'Account is successfully created. Please log in.');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
