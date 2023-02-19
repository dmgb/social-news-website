<?php declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\ControllerTrait;
use App\Entity\Story;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Vote\StoryVote;
use App\Form\SearchFieldType;
use App\Form\StoryType;
use App\Repository\StoryRepository;
use App\Repository\UserRepository;
use App\Repository\Vote\StoryVoteRepository;
use App\Serializer\CommentNormalizer;
use App\Serializer\StoryNormalizer;
use App\Service\ShortIdGenerator;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StoryController extends AbstractController
{
    use ControllerTrait;

    private const MAX_PER_PAGE = 15;

    public function __construct(
        private readonly CommentNormalizer $commentNormalizer,
        private readonly ShortIdGenerator  $shortIdGenerator,
        private readonly StoryNormalizer   $storyNormalizer,
        private readonly StoryRepository   $storyRepository,
    ){}

    /**
     * @throws ExceptionInterface
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $stories = $this->storyRepository->findAll($this->getUser());

        return $this->list($request, $stories);
    }

    /**
     * @throws OptimisticLockException
     * @throws Exception
     */
    #[Route('/submit', name: 'story_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        if (!$this->getUser()->canSubmitStories()) {
            $this->addFlash('danger', 'you are not allowed to submit new stories.');

            return $this->redirectToRoute('index');
        }

        $story = new Story($this->getUser());
        $form = $this->createForm(StoryType::class, $story)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $shortId = $this->shortIdGenerator->generate(Story::class);
            $story->setShortId($shortId);
            $this->storyRepository->save($story);
            $this->addFlash('success', 'story is successfully submitted.');

            return $this->redirectToRoute('index');
        }

        return $this->render('story/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/s/{shortId}/{slug}', name: 'story_show', methods: 'GET')]
    public function show(Story $story): Response
    {
        if (!$story->isApproved() || $story->isDeleted()) {
            throw new NotFoundHttpException();
        }

        $comments = $story->getComments()->filter(fn($comment) => null === $comment->getParent())->getValues();
        $fn = fn($comment) => $this->commentNormalizer->normalize($comment, context: ['include_children' => true]);

        return $this->render('story/show.html.twig', [
            'story' => $this->storyNormalizer->normalize($story),
            'comments' => array_map($fn, $comments),
        ]);
    }

    #[Route('/s/{shortId}/{slug}/edit', name: 'story_update', methods: ['GET', 'POST'])]
    public function edit(Request $request, Story $story): Response
    {
        $form = $this->createForm(StoryType::class, $story)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $story->setUpdatedAt(new DateTime());
            $this->storyRepository->save($story);
            $this->addFlash('success', 'story is successfully updated.');

            return $this->redirectToRoute('index');
        }

        return $this->render('story/edit.html.twig', [
            'story' => $story,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/s/{shortId}/{slug}/delete', name: 'story_delete', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN') and is_granted('delete', story)")]
    public function delete(Request $request, Story $story): Response
    {
        $this->assureIsAjax($request);

        $story->setIsDeleted(true);
        $this->storyRepository->save($story);
        $this->addFlash('success', 'Story is deleted.');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/admin/s/{shortId}/{slug}/undelete', name: 'story_undelete', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN') and is_granted('delete', story)")]
    public function undelete(Request $request, Story $story): Response
    {
        $this->assureIsAjax($request);

        $story->setIsDeleted(false);
        $this->storyRepository->save($story);
        $this->addFlash('success', 'Story is undeleted.');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/admin/s/{shortId}/{slug}/approve', name: 'story_approve', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN') and is_granted('approve', story)")]
    public function approve(Request $request, Story $story): Response
    {
        $this->assureIsAjax($request);

        $story->setIsApproved(true);
        $story->setDissapprovedReason(null);
        $this->storyRepository->save($story);
        $this->addFlash('success', 'story is approved.');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/admin/s/{shortId}/{slug}/disapprove', name: 'story_disapprove', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN') and is_granted('disapprove', story)")]
    public function disapprove(Request $request, Story $story, ValidatorInterface $validator): Response
    {
        $this->assureIsAjax($request);

        $content = json_decode($request->getContent(), true);
        $story->setDissapprovedReason($content['disapprovedReason']);
        $violations = $validator->validate($story, null, ['approval']);
        if (count($violations) > 0) {
            return new JsonResponse(['errors' => $this->getFormErrors($violations)]);
        }

        $this->storyRepository->save($story);
        $this->addFlash('success', 'story is disapproved.');

        return new JsonResponse(['success' => true]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/search', name: 'story_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $form = $this->createForm(SearchFieldType::class, null, [
            'action' => $this->generateUrl('story_search'),
            'method' => 'GET',
        ])->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $q = $request->get('q');
            $stories = $q !== '' ? $this->storyRepository->findBySearch($q, $this->getUser()) : [];

            return $this->list($request, $stories, ['q' => $q]);
        }

        return $this->render('include/search_bar.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/tag/{name}', name: 'story_get_by_tag', methods: ['GET'])]
    public function getByTag(Request $request, Tag $tag): Response
    {
        $stories = $this->storyRepository->findByTag($tag);

        return $this->list($request, $stories, ['tagName' => $tag->getName()]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/domain/{name}', name: 'story_get_by_domain', methods: ['GET'])]
    public function getByDomain(Request $request, string $name): Response
    {
        $stories = $this->storyRepository->findByDomain($name, $this->getUser());
        $users = array_map(fn($story) => $story->getUser(), $stories);
        $uniqueSubmittersCount = count(array_unique($users));

        return $this->list($request, $stories, ['uniqueSubmittersCount' => $uniqueSubmittersCount]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/stories/{username}', name: 'story_get_by_user', methods: ['GET'])]
    public function getByUser(Request $request, User $user): Response
    {
        $stories = $this->storyRepository->findByUser($user, $this->getUser());

        return $this->list($request, $stories, ['username' => $user->getUserIdentifier()]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/admin/s/pending', name: 'story_pending')]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function getPending(Request $request, StoryRepository $storyRepository): Response
    {
        $stories = $storyRepository->findBy(['isApproved' => false, 'isDeleted' => false], ['createdAt' => 'DESC']);
        $deletedCount = $this->storyRepository->count(['isDeleted' => true]);
        $extraData = ['pendingCount' => count($stories), 'deletedCount' => $deletedCount];

        return $this->list($request, $stories, $extraData, true);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/admin/s/deleted', name: 'story_deleted')]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function getDeleted(Request $request, StoryRepository $storyRepository): Response
    {
        $stories = $storyRepository->findBy(['isDeleted' => true], ['createdAt' => 'DESC']);
        $pendingCount = $this->storyRepository->count(['isApproved' => false]);
        $extraData = ['deletedCount' => count($stories), 'pendingCount' => $pendingCount];

        return $this->list($request, $stories, $extraData, true);
    }

    /**
     * @throws ExceptionInterface
     */
    private function list(Request $request, array $stories, array $extraData = [], bool $isAdminView = false): Response
    {
        $currentPage = $request->query->getInt('page', 1);
        $paginator = $this->createPaginator($stories, $currentPage, self::MAX_PER_PAGE);
        $template = $isAdminView ? 'admin/dashboard.twig' : 'story/index.html.twig';
        $fn = fn($story) => $this->storyNormalizer->normalize($story);

        return $this->render($template, [
            'stories' => array_map($fn, $paginator->getCurrentPageResults()),
            'pager' => $paginator,
            'extraData' => $extraData,
            'actions' => $isAdminView ? $this->getAdminActions() : null,
        ]);
    }

    /**
     * @throws OptimisticLockException
     */
    #[Route('/s/vote/create', name: 'story_vote_create', methods: ['GET', 'POST'])]
    public function handleVote(Request $request, StoryVoteRepository $voteRepository, UserRepository $userRepository): JsonResponse
    {
        $this->assureIsAjax($request);

        $content = json_decode($request->getContent(), true);
        $story = $this->storyRepository->find($content['id']);
        $vote = new StoryVote($story, $this->getUser());
        $voteRepository->save($vote);
        $story->setScore($story->getScore(), $content['upvote']);
        $this->storyRepository->save($story);
        $storySubmitter = $story->getUser();
        $storySubmitter->setKarma($storySubmitter->getKarma(), $content['upvote']);
        $userRepository->save($storySubmitter);

        return new JsonResponse(['score' => $story->getScore()]);
    }
}
