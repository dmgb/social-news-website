<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\IdenticonGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private IdenticonGenerator $identiconGenerator,
    ){}

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('dev');
        $user->setEmail('dev@gmail.com');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setAvatarPath($this->identiconGenerator->generate($user->getUserIdentifier()));
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($password);
        $manager->persist($user);
        $manager->flush();
    }
}
