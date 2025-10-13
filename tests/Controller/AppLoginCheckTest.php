<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppLoginCheckTest extends WebTestCase
{

    private $client;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        // Vide la table user avant chaque test
        $connection = $this->em->getConnection();
        $connection->executeStatement("DELETE FROM user");

        // Création d'un utilisateur pour le login
        $user = new User();
        $user->setEmail('test2@mail.com');
        $user->setPassword($this->hasher->hashPassword($user, 'Motdep4sse!'));
        $this->em->persist($user);
        $this->em->flush();
    }

    public function testLoginSuccess(): void
    {
        // Requête de login
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test2@mail.com', // Bon email
                'password' => 'Motdep4sse!' // Bon mot de passe
            ])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertNotEmpty($data['token']);

    }

    public function testLoginFailWrongEmail(): void
    {

        $user = new User();
        $user->setEmail('test@mail.com');
        $user->setPassword($this->hasher->hashPassword($user, 'Motdep4sse!'));
        $this->em->persist($user);
        $this->em->flush();

        // Requête de login
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test11@mail.com', // Mauvais email
                'password' => 'Motdep4sse!'
            ])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(401,  $response->getStatusCode());

    }

    public function testLoginFailWrongPassword(): void
    {
        $user = new User();
        $user->setEmail('test@mail.com');
        $user->setPassword($this->hasher->hashPassword($user, 'Motdep4sse!'));
        $this->em->persist($user);
        $this->em->flush();

        // Requête de login
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@mail.com',
                'password' => 'mdp' // Mauvais mot de passe
            ])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(401,  $response->getStatusCode());

    }


}
