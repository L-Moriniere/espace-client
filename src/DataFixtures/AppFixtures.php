<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->passwordHasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $timezone = new \DateTimeZone('Europe/Paris');

        $usersData = [
            [
                'email' => 'user1@example.com',
                'password' => 'Password123!',
                'lastLogin' => new \DateTime('-30 minutes', $timezone), // actif récemment
            ],
            [
                'email' => 'user2@example.com',
                'password' => 'Password456!',
                'lastLogin' => new \DateTime('-2 hours', $timezone), // inactif
            ],
            [
                'email' => 'user3@example.com',
                'password' => 'Password789!',
                'lastLogin' => new \DateTime('-10 minutes', $timezone), // actif récemment
            ],
            [
                'email' => 'user4@example.com',
                'password' => 'PasswordABC!',
                'lastLogin' => new \DateTime('-2 hours', $timezone), // inactif
            ],
            [
                'email' => 'user5@example.com',
                'password' => 'PasswordXYZ!',
                'lastLogin' => new \DateTime('-50 minutes', $timezone), // actif récemment
            ],
        ];

        foreach ($usersData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles(['ROLE_USER']);
            $user->setLastLogin($data['lastLogin']);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $data['password'])
            );

            $manager->persist($user);
        }

        $manager->flush();
    }
}
