<?php declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\ControllerTrait;
use App\Entity\Message;
use App\Entity\User;
use App\Enum\MessageState;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Serializer\MessageNormalizer;
use App\Service\ShortIdGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class MessageController extends AbstractController
{
    use ControllerTrait;

    private const FOLDERS = ['inbox', 'sent', 'trash'];
    private const MAX_PER_PAGE = 15;

    public function __construct(
        private readonly MessageNormalizer $normalizer,
        private readonly MessageRepository $repository,
        private readonly ShortIdGenerator  $shortIdGenerator,
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/messages/{folder}', name: 'messages', methods: ['GET', 'POST'])]
    public function index(Request $request, string $folder): Response
    {
        if ($folder && !in_array($folder, self::FOLDERS)) {
            throw $this->createNotFoundException();
        }

        /** @var $user User */
        $user = $this->getUser();
        $paginator = $this->createPaginator(
            match ($folder) {
                'inbox' => $this->repository->findByReceiver($user),
                'sent' => $this->repository->findBySender($user),
                'trash' => [],
            },
            $request->query->getInt('page', 1),
            self::MAX_PER_PAGE,
        );
        $data = $this->getData($this->normalizer, $paginator->getCurrentPageResults());
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['data' => $data]);
        }

        return $this->render('message/index.twig', [
            'data' => $data,
            'pager' => $paginator,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/message/new/{username}', name: 'message_new', methods: ['GET', 'POST'])]
    #[Entity('user', options: ['username' => 'username'])]
    public function new(Request $request, User $receiver): Response
    {
        /** @var $user User */
        $user = $this->getUser();
        $message = new Message($user, $receiver);
        $form = $this->createForm(MessageType::class, $message)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message->setShortId($this->shortIdGenerator->generate(Message::class));
            $this->repository->save($message);
            $this->addFlash('success', 'message is successfully sent.');

            return $this->redirectToRoute('index');
        }

        return $this->render('message/new.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/m/{shortId}', name: 'message_show', methods: ['GET', 'POST'])]
    public function show(Message $message): Response
    {
        return $this->render('message/show.twig', ['body' => $message->getBody()]);
    }

    #[Route('/m/{shortId}/change-state', name: 'message_change_state', methods:'POST')]
    public function toggleState(Request $request, string $shortId): Response
    {
        $this->assureIsAjax($request);

        $message = $this->repository->findOneBy(['shortId' => $shortId]);
        if (!$message instanceof Message) {
            $this->createNotFoundException();
        }

        $state = match ($message->getState()) {
            MessageState::READ => MessageState::UNREAD,
            MessageState::UNREAD => MessageState::READ,
        };
        $message->setState($state);
        $this->repository->save($message);

        return new JsonResponse(['state' => $message->getState()]);
    }
}
