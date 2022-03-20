<?php declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\ControllerTrait;
use App\Entity\Comment;
use App\Entity\Story;
use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\Vote\CommentVoteRepository;
use App\Serializer\CommentNormalizer;
use App\Service\ShortIdGenerator;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentController extends AbstractController
{
    use ControllerTrait;

    public const MAX_PER_PAGE = 20;

    public function __construct(
        private CommentRepository $commentRepository,
        private CommentNormalizer $normalizer,
        private ShortIdGenerator $shortIdGenerator,
        private ValidatorInterface $validator,
    ){}

    /**
     * @throws ExceptionInterface
     */
    #[Route('/comments', name: 'comments', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $comments = $this->commentRepository->findAll();

        return $this->list($request, $comments);
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    #[Route('/comment/create/{story}/{parent?}', name: 'comment_create', methods: ['GET', 'POST'])]
    #[Entity('parent', options: ['id' => 'parent'])]
    public function create(Request $request, Story $story, ?Comment $parent): JsonResponse
    {
        $this->assureIsAjax($request);

        $content = json_decode($request->getContent(), true);
        $comment = new Comment($story, $this->getUser(), $parent);
        $comment->setBody($content['body']);
        $violations = $this->validator->validate($comment);

        if (count($violations) > 0) {
           return new JsonResponse(['errors' => $this->getFormErrors($violations)]);
        }

        $comment->setShortId($this->shortIdGenerator->generate(Comment::class));
        $this->commentRepository->save($comment);

        return new JsonResponse([
            'comment' => $this->normalizer->normalize($comment, context: ['include_children' => true]),
            'totalCount' => count($story->getComments()),
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/comments/{username}', name: 'comment_get_by_user', methods: ['GET'])]
    public function getByUser(Request $request, User $user): Response
    {
        return $this->list($request, $user->getComments()->toArray(), ['username' => $user->getUserIdentifier()]);
    }

    /**
     * @throws ExceptionInterface
     */
    private function list(Request $request, array $comments, ?array $extraData = null): Response
    {
        $paginator = $this->createPaginator($comments, $request->query->getInt('page', 1), self::MAX_PER_PAGE);

        return $this->render('comment/index.html.twig', [
            'comments' => array_map(fn($comment) => $this->normalizer->normalize($comment), $paginator->getCurrentPageResults()),
            'pager' => $paginator,
            'extraData' => $extraData,
        ]);
    }

    /**
     * @throws OptimisticLockException
     */
    #[Route('/c/vote/create', name: 'comment_vote_create', methods: ['GET', 'POST'])]
    public function handleVote(Request $request, CommentVoteRepository $voteRepository, UserRepository $userRepository): JsonResponse
    {
        $this->assureIsAjax($request);

        $content = json_decode($request->getContent(), true);
        $comment = $this->commentRepository->find($content['id']);
        $vote = new CommentVote($comment, $this->getUser());
        $voteRepository->save($vote);
        $comment->setScore($comment->getScore(), $content['upvote']);
        $this->commentRepository->save($comment);
        $commentSubmitter = $comment->getUser();
        $commentSubmitter->setKarma($commentSubmitter->getKarma(), $content['upvote']);
        $userRepository->save($commentSubmitter);

        return new JsonResponse(['score' => $comment->getScore()]);
    }
}
