<?php declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Story;
use App\Helper\DateTimeFormatter;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class StoryNormalizer extends AbstractNormalizer
{
    public function __construct(
        protected UrlGeneratorInterface $router,
        protected Security $security,
    )
    {
        parent::__construct($router, $security);
    }

    public function normalize($story, string $format = null, array $context = []): array
    {
        $routeParameters = ['shortId' => $story->getShortId(), 'slug' => $story->getSlug()];

        return [
            'id' => $story->getId(),
            'title' => $story->getTitle(),
            'url' => $story->getUrl(),
            'domain' => $this->normalizeDomain($story->getUrl()),
            'user' => $this->normalizeUser($story->getUser()),
            'createdAt' => DateTimeFormatter::timeAgo($story->getCreatedAt()),
            'score' => $story->getScore(),
            'tags' => $this->normalizeTags($story->getTags()->toArray()),
            'commentsCount' => count($story->getComments()),
            'hasVoteOfCurrentUser' => $story->hasVoteOfUser($this->security->getUser()),
            'routes' => [
                'show' => $this->router->generate('story_show', $routeParameters),
                'comment' => $this->router->generate('comment_create', ['story' => $story->getId()]),
                'vote' => $this->router->generate('story_vote_create'),
                'edit' => $this->security->isGranted('edit', $story) ?
                    $this->router->generate('story_update', $routeParameters) :
                    null,
                'delete' => $this->security->isGranted('delete', $story) ?
                    $this->getDeleteRoute($story, $routeParameters) :
                    null,
                'approve' => $this->security->isGranted('ROLE_ADMIN') && !$story->isDeleted() ?
                    $this->router->generate('story_approve', $routeParameters) :
                    null,
                'disapprove' => $this->security->isGranted('ROLE_ADMIN') && !$story->getDissapprovedReason() && !$story->isDeleted() ?
                    $this->router->generate('story_disapprove', $routeParameters) :
                    null,
            ],
        ];
    }

    private function getDeleteRoute(Story $story, array $routeParameters): ?array
    {
        if ($story->isDeleted()) {
            return [
                'url' => $this->router->generate('story_undelete', $routeParameters),
                'action' => 'undelete',
            ];
        }

        return [
            'url' => $this->router->generate('story_delete', $routeParameters),
            'action' => 'delete',
        ];
    }

    private function normalizeDomain(string $url): array
    {
        $name = $this->normalizeUrl($url);
        $localUrl = $this->router->generate('story_get_by_domain', ['name' => $name]);

        return [
            'name' => $name,
            'url' => $localUrl,
        ];
    }

    private function normalizeTags(array $tags): array
    {
        $this->setSerializer(new TagNormalizer($this->router, $this->security));

        return array_map(fn($tag) => $this->serializer->normalize($tag, null, []), $tags);
    }
}
