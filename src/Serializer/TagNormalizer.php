<?php declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class TagNormalizer extends AbstractNormalizer
{
    public function __construct(
        protected UrlGeneratorInterface $router,
        protected Security $security,
    )
    {
        parent::__construct($router, $security);
    }

    public function normalize($tag, string $format = null, array $context = []): array
    {
        $name = $tag->getName();
        $route = $this->router->generate('story_get_by_tag', ['name' => $name]);

        return [
            'name' => $name,
            'route' => $route,
        ];
    }
}
