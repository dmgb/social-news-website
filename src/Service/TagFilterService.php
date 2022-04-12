<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\TagFilter;
use App\Helper\CollectionHelper;
use App\Repository\TagFilterRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

class TagFilterService
{
    public function __construct(
        private TagFilterRepository $repository,
    ){}

    public function handle(Collection $tags, Collection $currentFilters, UserInterface $user): void
    {
        $currentTags = $this->getTagsFromFilters($currentFilters);
        $newlySelectedTags = CollectionHelper::filterIfDoesNotContain($tags, $currentTags);
        $deselectedTags = CollectionHelper::filterIfDoesNotContain($currentTags, $tags);
        $newFilters = $newlySelectedTags->map(fn($tag) => new TagFilter($tag, $user));
        $deselectedFilters = $currentFilters->filter(fn($filter) => $deselectedTags->contains($filter->getTag()));

        $this->repository->save($newFilters);
        $this->repository->remove($deselectedFilters);
    }

    public function getTagsFromFilters(Collection $filters): Collection
    {
        return $filters->map(fn($filter) => $filter->getTag());
    }
}
