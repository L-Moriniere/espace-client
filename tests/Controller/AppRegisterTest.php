<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AppRegisterTest extends WebTestCase
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

    public function testRegisterSuccess(): void
    {

        $this->client->request(
            'POST',
            '/api/register',
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
        $this->assertSame('Utilisateur créé avec succès', $data['message']);

        //verifie que l'utilisateur a été créé en base de données
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'user@mail.com']);
        $this->assertNotNull($user);
        $this->assertSame('user@mail.com', $user->getEmail());

    }

    public function testRegisterFailNotAnEmail(): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'pas-un-email',
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
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'short@mail.com',
                'password' => 'Pass!1'
            ])
        );
        $response = $this->client->getResponse();
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testRegisterFailPasswordNoUppercase(): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'noUpper@mail.com',
                'password' => 'password!!123'
            ])
        );
        $response = $this->client->getResponse();
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testRegisterFailPasswordNoDigit(): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'noDigit@mail.com',
                'password' => 'Password!!'
            ])
        );
        $response = $this->client->getResponse();
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testRegisterFailPasswordNoSpecialChar(): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'noSpecial@mail.com',
                'password' => 'Password123'
            ])
        );
        $response = $this->client->getResponse();
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testRegisterFailPasswordEmpty(): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'empty@mail.com',
                'password' => ''
            ])
        );
        $response = $this->client->getResponse();
        $this->assertEquals(422, $response->getStatusCode());
    }


}
