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

    public function normalize($comment, string $format = null, array $context = []): array
    {
        $data = [
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
            'routes' => [
                'reply' => $this->router->generate('comment_create', [
                    'story' => $comment->getStory()->getId(),
                    'parent' => $comment->getId(),
                ]),
                'vote' => $this->router->generate('comment_vote_create'),
            ],
        ];

        return isset($context['include_children']) ? array_merge($data, $this->normalizeChildren($comment)) : $data;
    }

    /**
     * @throws ExceptionInterface
     */
    private function normalizeChildren(Comment $comment): array
    {
        return null === $comment->getParent() ?
            [
                'children' => array_map(fn($child) => $this->normalize($child), $comment->getChildren()->toArray()),
                'childrenCount' => count($comment->getChildren()),
            ]
            :
            [
                'childrenCount' => count($comment->getParent()->getChildren()),
            ];
    }
}
