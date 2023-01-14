<?php declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Story;
use App\Helper\DateTimeFormatter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class StoryNormalizer extends AbstractNormalizer
{
    public function __construct(
        private readonly UrlGeneratorInterface $router,
        private readonly Security $security,
    )
    {
        parent::__construct($router, $security);
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Story) {
            throw new \TypeError('Argument $object must be of type ' . Story::class);
        }

        return [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'url' => $object->getUrl(),
            'domain' => $this->normalizeDomain($object->getUrl()),
            'user' => $this->normalizeUser($object->getUser()),
            'createdAt' => DateTimeFormatter::timeAgo($object->getCreatedAt()),
            'score' => $object->getScore(),
            'tags' => $this->normalizeTags($object->getTags()->toArray()),
            'commentsCount' => count($object->getComments()),
            'hasVoteOfCurrentUser' => $object->hasVoteOfUser($this->security->getUser()),
            'urls' => $this->getUrls($object),
        ];
    }

    private function getUrls(Story $story): array
    {
        $params = ['shortId' => $story->getShortId(), 'slug' => $story->getSlug()];
        $show = $this->router->generate('story_show', $params);
        $edit = $this->security->isGranted('edit', $story) ? $this->router->generate('story_update', $params) : null;
        $delete = $this->security->isGranted('delete', $story) ? $this->getDeleteUrl($story, $params) : null;
        $comment = $this->router->generate('comment_create', ['story' => $story->getId()]);
        $vote = $this->router->generate('story_vote_create');
        $approve = $this->security->isGranted('ROLE_ADMIN') && !$story->isDeleted() ? $this->router->generate('story_approve', $params) : null;
        $disapprove = $this->security->isGranted('ROLE_ADMIN') && !$story->getDissapprovedReason() && !$story->isDeleted() ? $this->router->generate('story_disapprove', $params) : null;

        return array_filter([
            'show' => $show,
            'comment' => $comment,
            'vote' => $vote,
            'edit' => $edit,
            'delete' => $delete,
            'approve' => $approve,
            'disapprove' => $disapprove,
        ]);
    }

    private function getDeleteUrl(Story $story, array $routeParameters): ?array
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
        $url = $this->router->generate('story_get_by_domain', ['name' => $name]);

        return [
            'name' => $name,
            'url' => $url,
        ];
    }

    private function normalizeTags(array $tags): array
    {
        $this->setSerializer(new TagNormalizer($this->router, $this->security));
        $fn = fn($tag) => $this->serializer->normalize($tag, null, []);

        return array_map($fn, $tags);
    }
}
