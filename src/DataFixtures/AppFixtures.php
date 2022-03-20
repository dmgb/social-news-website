<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ){}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('dev');
        $user->setEmail('dev@gmail.com');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($password);
        $manager->persist($user);
        $manager->flush();
    }
}
