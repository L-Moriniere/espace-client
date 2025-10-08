<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class AppMailTest extends WebTestCase
{

    private $client;
    private EntityManagerInterface $em;
    private string $jwtToken;

    protected function setUp():void{
        $this->client = AppRegisterTest::createClient();
        $this->em = AppRegisterTest::getContainer()->get(EntityManagerInterface::class);
        $hasher = AppRegisterTest::getContainer()->get('security.user_password_hasher');

        // Vide la table user avant chaque test
        $connection = $this->em->getConnection();
        $connection->executeStatement("DELETE FROM user");

        // Création d'un utilisateur pour le login
        $email = 'user@mail.com';
        $password = 'Password!!123';
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($hasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_USER']);
        $this->em->persist($user);
        $this->em->flush();

        // Récupération du token JWT
        $this->jwtToken = $this->getJwtToken($email, $password);
    }

    private function getJwtToken(string $email, string $password): string
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => $password
            ])
        );
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        var_dump($data);
        return $data['token'];
    }

    public function testSendMessageSuccess(): void
    {

        $testFile = tempnam(sys_get_temp_dir(), 'test_fichier');
        file_put_contents($testFile, "%PDF-1.4\n%EOF");

        $uploadedFile = new UploadedFile(
            $testFile,
            'test.pdf',
            'application/pdf',
            null,
            true
        );

        $this->client->request(
            'POST',
            '/api/send-message',
            [
                'subject' => 'Sujet du message',
                'message' => 'Corps du message'
            ],
            [
                'attachment' => $uploadedFile
            ],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwtToken,
            ],
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Message envoyé avec succès', $data['message']);

    }


}
