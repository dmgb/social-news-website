<?php declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Comment;
use App\Helper\DateTimeFormatter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CommentNormalizer extends AbstractNormalizer
{
    public function __construct(
        protected UrlGeneratorInterface $router,
        protected Security $security,
    )
    {
        parent::__construct($router, $security);
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Comment $comment */
        $comment = $object;
        $return = [
            'id' => $comment->getId(),
            'shortId' => $comment->getShortId(),
            'story' => [
                'name' => $comment->getStory()->getTitle(),
                'url' => $this->router->generate('story_show', [
                    'shortId' => $comment->getStory()->getShortId(),
                    'slug' => $comment->getStory()->getSlug(),
                ])
            ],
            'body' => $comment->getBody(),
            'createdAt' => DateTimeFormatter::timeAgo($comment->getCreatedAt()),
            'score' => $comment->getScore(),
            'user' => $this->normalizeUser($comment->getUser()),
            'hasVoteOfCurrentUser' => $comment->hasVoteOfCurrentUser($this->security->getUser()),
            'urls' => [
                'reply' => $this->router->generate('comment_create', [
                    'story' => $comment->getStory()->getId(),
                    'parent' => $comment->getId(),
                ]),
                'vote' => $this->router->generate('comment_vote_create'),
            ],
        ];

        if (isset($context['include_children'])) {
            return array_merge($return, $this->normalizeChildren($comment));
        }

        return $return;
    }

    /**
     * @throws ExceptionInterface
     */
    private function normalizeChildren(Comment $comment): array
    {
        if ($comment->isParent()) {
            $fn = fn($child) => $this->normalize($child);
            $children = $comment->getChildren()->toArray();

            return [
                'children' => array_map($fn, $children),
                'childrenCount' => count($children),
            ];
        }

        $children = $comment->getParent()->getChildren();

        return [
            'children' => $children,
            'childrenCount' => count($children),
        ];
    }
}
