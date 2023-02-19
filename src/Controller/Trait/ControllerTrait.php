<?php declare(strict_types=1);

namespace App\Controller\Trait;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

trait ControllerTrait
{
    public function assureIsAjax(Request $request): void
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @throws ExceptionInterface
     */
    public function getData(NormalizerInterface $normalizer, array $objects): array
    {
        return array_map(fn (object $object) => $normalizer->normalize($object), $objects);
    }

    public function getFormErrors(ConstraintViolationList $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }

    public function createPaginator(array $items, int $currentPage, int $maxPerPage): Pagerfanta
    {
        return Pagerfanta::createForCurrentPageWithMaxPerPage(new ArrayAdapter($items), $currentPage, $maxPerPage);
    }

    public function getAdminActions($user = null): array
    {
        if ($user instanceof UserInterface) {
            return [
                'user' => [
                    'ban' => $this->generateUrl('user_ban', ['username' => $user->getUserIdentifier()]),
                    'unban' => $this->generateUrl('user_unban', ['username' => $user->getUserIdentifier()]),
                ]
            ];
        }

        return [
            'user' => [
                'invite' => $this->generateUrl('user_invite'),
            ]
        ];
    }
}
