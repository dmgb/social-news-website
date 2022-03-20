<?php declare(strict_types=1);

namespace App\Service;

use App\Helper\RandomStringGenerator;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ShortIdGenerator
{
    public function __construct(
        private ObjectManager $manager,
    ){}

    /**
     * @throws Exception
     */
    public function generate(string $class): string
    {
        do {
            $shortId = RandomStringGenerator::generate(6);
        } while (null !== $this->manager->getRepository($class)->findOneBy(['shortId' => $shortId]));

        return $shortId;
    }
}
