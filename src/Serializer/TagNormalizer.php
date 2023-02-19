<?php declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Tag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TagNormalizer extends AbstractNormalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Tag $tag */
        $tag = $object;
        $name = $tag->getName();
        $url = $this->router->generate('story_get_by_tag', ['name' => $name]);

        return [
            'name' => $name,
            'url' => $url,
        ];
    }
}
