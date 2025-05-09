<?php

namespace App\DataFixtures;

use App\Entity\Admin\Moderator;
use App\Entity\Area;
use App\Entity\IssueCategory;
use App\Entity\Organisation;
use App\Entity\User;
use App\Model\Admin\ModeratorCreated;
use App\Model\UserCreated;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const MODERATOR_USER_REFERENCE = 'moderator-user';

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $userCreatedModerator = new ModeratorCreated('moderator@velo-cite.org', 'paul', 'lopez', '0620301030');
        $moderator = Moderator::create($userCreatedModerator);
        $pass = $this->hasher->hashPassword($moderator, 'test');
        $moderator->definePassword($pass);

//        $userCreatedAdmin = new AdminCreated('admin@velo-cite.org', 'paul', 'lopez', '0620301030');
//        $admin = Admin::create($userCreatedAdmin);
//        $pass = $this->hasher->hashPassword($admin, 'test');
//        $admin->updatePassword($pass);

        $organisationBM = new Organisation('Bordeaux Métropole');
        $organisationDepartementGironde = new Organisation('Gironde');

        $manager->persist($organisationBM);
        $manager->persist($organisationDepartementGironde);
        $manager->persist($moderator);

        $issueChantiers = new IssueCategory('Chantiers');
        $issueSignalisationMarquageAuSol = new IssueCategory('Signalisation / Marquage au sol');
        $issueStationnementSauvage = new IssueCategory('Stationnement sauvage');
        $issueDeteriorationDeLaChaussee = new IssueCategory('Déterioration de la chaussée');
        $issueStationnementVelo = new IssueCategory('Stationnement vélo');
        $issueDoubleSensCyclable = new IssueCategory('Double sens cyclable');
        $issueVegetationSauvage = new IssueCategory('Végétation sauvage');
        $issueManifestation = new IssueCategory('Manifestation');
        $issueAutres = new IssueCategory('Autres');
        $issueRondPoint = new IssueCategory('Rond-point');
        $issueAmenagementsDeCarrefours = new IssueCategory('Aménagements de carrefours');
        $issuePistesOuBandeCyclables = new IssueCategory('Pistes ou bande cyclables');
        $issueM12Manquant = new IssueCategory('M12 manquant');

        $manager->persist($issueChantiers);
        $manager->persist($issueSignalisationMarquageAuSol);
        $manager->persist($issueStationnementSauvage);
        $manager->persist($issueDeteriorationDeLaChaussee);
        $manager->persist($issueStationnementVelo);
        $manager->persist($issueDoubleSensCyclable);
        $manager->persist($issueVegetationSauvage);
        $manager->persist($issueManifestation);
        $manager->persist($issueAutres);
        $manager->persist($issueRondPoint);
        $manager->persist($issueAmenagementsDeCarrefours);
        $manager->persist($issuePistesOuBandeCyclables);
        $manager->persist($issueM12Manquant);

        $manager->flush();

        $this->addReference(self::MODERATOR_USER_REFERENCE, $moderator);
//        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);
    }
}
