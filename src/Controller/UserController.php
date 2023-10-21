<?php declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\ControllerTrait;
use App\Entity\Invitation;
use App\Entity\User;
use App\Form\TagFilterType;
use App\Mailer\UserMailer;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use App\Service\InvitationTokenGenerator;
use App\Service\TagFilterService;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private readonly UserMailer         $mailer,
        private readonly UserRepository     $repository,
        private readonly ValidatorInterface $validator,
    ){}

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/u/{username}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $appUser = $this->getUser();
        if ($user->isAdmin() && !$appUser?->isAdmin()) {
            throw new NotFoundHttpException();
        }

        return $this->render('user/show.twig', [
            'user' => $user,
            'mostCommonStoryTag' => $this->repository->findMostCommonStoryTag($user),
            'actions' => $appUser?->isAdmin() ? $this->getAdminActions($user) : null,
        ]);
    }

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    #[Route('/invite-user', name: 'user_invite', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN') and is_granted('invite', user)")]
    public function invite(
        Request                  $request,
        InvitationTokenGenerator $tokenGenerator,
        InvitationRepository     $invitationRepository,
    ): Response
    {
        $this->assureIsAjax($request);

        $content = json_decode($request->getContent(), true);
        $email = $content['email'];
        $token = $tokenGenerator->generate();
        $invitation = new Invitation($email, $token);
        $violations = $this->validator->validate($invitation);

        if (count($violations) > 0) {
            return new JsonResponse(['errors' => $this->getFormErrors($violations)]);
        }

        $invitationRepository->save($invitation);
        $url = $this->generateUrl('register', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->mailer->sendUserInvitationLink($email, $url);
        $this->addFlash('success', 'invitation has been successfully sent.');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/u/{username}/ban', name: 'user_ban', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN') and is_granted('ban', user)")]
    public function ban(Request $request, User $user): Response
    {
        $this->assureIsAjax($request);

        $content = json_decode($request->getContent(), true);
        $user->setIsBanned(true);
        $user->setBannedReason($content['bannedReason']);
        $violations = $this->validator->validate($user, null, ['ban']);

        if (count($violations) > 0) {
            return new JsonResponse(['errors' => $this->getFormErrors($violations)]);
        }

        $user->setForceReloginAt(new DateTime());
        $this->repository->save($user);
        $this->addFlash('success', 'user is banned.');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/u/{username}/unban', name: 'user_unban', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN') and is_granted('unban', user)")]
    public function unban(Request $request, User $user): Response
    {
        $this->assureIsAjax($request);

        $user->setIsBanned(false);
        $user->setBannedReason(null);
        $this->repository->save($user);
        $this->addFlash('success', 'user is unbanned.');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/u/{username}/filters', name: 'user_filter_tags', methods: ['GET', 'POST'])]
    public function filterTags(Request $request, UserInterface $user, TagFilterService $service): Response
    {
        $currentFilters = $user->getTagFilters();
        $currentTags = $service->getTagsFromFilters($currentFilters);
        $form = $this->createForm(TagFilterType::class, ['currentTags' => clone $currentTags]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->handle($form->getData()['tags'], $currentFilters, $user);
            $this->addFlash('success', 'Filters successfully saved.');

            return $this->redirectToRoute('index');
        }

        return $this->render('user/filter_tags.twig', [
            'form' => $form->createView(),
        ]);
    }
}
