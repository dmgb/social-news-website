<?php declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Story;
use App\Helper\DateTimeFormatter;
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

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Story $story */
        $story = $object;

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
            'urls' => $this->getUrls($story),
        ];
    }

    private function getUrls(Story $story): array
    {
        $parameters = ['shortId' => $story->getShortId(), 'slug' => $story->getSlug()];
        $canApprove = $this->security->isGranted('ROLE_ADMIN') && !$story->isDeleted();
        $canDissaprove = $this->security->isGranted('ROLE_ADMIN') && !$story->getDissapprovedReason() && !$story->isDeleted();
        $show = $this->router->generate('story_show', $parameters);
        $comment = $this->router->generate('comment_create', ['story' => $story->getId()]);
        $vote = $this->router->generate('story_vote_create');
        $edit = $this->security->isGranted('edit', $story) ? $this->router->generate('story_update', $parameters) : null;
        $delete = $this->security->isGranted('delete', $story) ? $this->getDeleteUrl($story, $parameters) : null;
        $approve = $canApprove ? $this->router->generate('story_approve', $parameters) : null;
        $disapprove = $canDissaprove ? $this->router->generate('story_disapprove', $parameters) : null;

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
