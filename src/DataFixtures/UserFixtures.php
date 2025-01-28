<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $superAdmin = new User();
        $superAdmin->setEmail('evan.moreau@etik.com');
        $plaintextPassword = 'admin';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $superAdmin,
            $plaintextPassword
        );
        $superAdmin->setPassword($hashedPassword);
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdmin->setIsVerified(true);
        $superAdmin->setLastConnection(new \DateTime()); // Ajoute la date de dernière connexion
        $superAdmin->setUserName('Lloyd Le Vrai');
        $superAdmin->setDiscordName("evanmoreau");
        $manager->persist($superAdmin);
        $manager->flush();
    }
}