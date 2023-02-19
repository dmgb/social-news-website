<?php declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Interface\Normalizable;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractNormalizer implements NormalizerInterface, SerializerInterface
{
    use SerializerAwareTrait;

    public function __construct(
        protected readonly UrlGeneratorInterface $router,
        protected readonly Security $security,
    ){}

    abstract public function normalize($object, string $format = null, array $context = []): array;

    public function normalizeUser(User $user): array
    {
        $username = $user->getUserIdentifier();
        $url = $this->router->generate('user_show', ['username' => $username]);

        return [
            'username' => $username,
            'url' => $this->router->generate('user_show', ['username' => $username]),
            'avatarPath' => '/build'.$user->getAvatarPath(),
        ];
    }

    public function normalizeUrl(string $url): string
    {
        return preg_replace('/^www\./', '', parse_url($url)['host']);
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Normalizable;
    }

    public function serialize($data, string $format, array $context = []): string
    {
        return '';
    }

    public function deserialize($data, string $type, string $format, array $context = []): string
    {
        return '';
    }
}
