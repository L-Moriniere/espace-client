<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AppResetPasswordTest extends WebTestCase
{

    private $client;
    private EntityManagerInterface $em;

    protected function setUp():void{
        $this->client = AppRegisterTest::createClient();
        $this->em = AppRegisterTest::getContainer()->get(EntityManagerInterface::class);

        // Vide la table user avant chaque test
        $connection = $this->em->getConnection();
        $connection->executeStatement("DELETE FROM user");
    }

    public function testResetPasswordSuccess(): void
    {
        $user = new User();
        $user->setEmail('user@mail.com');
        $user->setPassword('AncienPassword!123'); // mot de passe initial (hashé si besoin)
        $user->setRoles(['ROLE_USER']);
        $this->em->persist($user);
        $this->em->flush();


        $this->client->request(
            'POST',
            '/api/reset-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user@mail.com',
                'password' => 'Password!!123' // Mot de passe valide
            ])
        );

        $response = $this->client->getResponse();

        //Verifie que la réponse renvoie 201
        $this->assertEquals(201, $response->getStatusCode());

        //Verifie que le message de succès est correct
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertSame('Mot de passe réinitialisé avec succès', $data['message']);
    }

    public function testResetPasswordFailNoValidEmail(): void
    {
        $this->client->request(
            'POST',
            '/api/reset-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'mail@test.fr',
                'password' => 'Motdep4sse!'
            ])
        );

        $response = $this->client->getResponse();

        //Verifie que la réponse renvoie 422
        $this->assertEquals(422, $response->getStatusCode());

    }

    public function testRegisterFailPasswordTooShort(): void
    {
        $this->client->request(
            'POST',
            '/api/reset-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user@mail.com',
                'password' => 'Pass!1'
            ])
        );
        $response = $this->client->getResponse();
        $this->assertEquals(422, $response->getStatusCode());
    }


}
