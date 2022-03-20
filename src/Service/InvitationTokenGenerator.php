<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Invitation;
use App\Helper\RandomStringGenerator;
use Doctrine\Persistence\ObjectManager;
use Exception;

class InvitationTokenGenerator
{
    public function __construct(
        private ObjectManager $manager,
    ){}

    /**
     * @throws Exception
     */
    public function generate(): string
    {
        do {
            $token = RandomStringGenerator::generate(15);
        } while (null !== $this->manager->getRepository(Invitation::class)->findOneBy(['token' => $token, 'isUsed' => false]));

        return $token;
    }
}
