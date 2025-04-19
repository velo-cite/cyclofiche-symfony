<?php

namespace App\DataFixtures;

use App\Entity\Area;
use App\Entity\Organisation;
use App\Entity\User;
use App\Model\UserCreated;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $userCreatedAdmin = new UserCreated('paul@velo-cite.org', 'paul', 'lopez', ['ROLE_ADMIN'], '0620301030');
        $userAdmin = User::create($userCreatedAdmin);
        $pass = $this->hasher->hashPassword($userAdmin, 'test');
        $userAdmin->updatePassword($pass);

        $area = new Area('Bordeaux Métropole', '');
        $areaBordeaux = new Area('Bordeaux', '');

        $organisation = new Organisation('Bordeaux Métropole', new ArrayCollection([
            $area,
            $areaBordeaux,
        ]));

        $manager->persist($organisation);
        $manager->persist($userAdmin);
        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $userAdmin);
    }
}
