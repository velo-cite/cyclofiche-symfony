<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Model\UserCreated;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $userCreated = new UserCreated('paul@velo-cite.org', 'paul', 'lopez', '0620301030');
        $user = User::create($userCreated);
        $pass = $this->hasher->hashPassword($user, 'test');
        $user->updatePassword($pass);

        $manager->persist($user);
        $manager->flush();
    }
}
