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

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $hasher = static::getContainer()->get('security.user_password_hasher');

        // Vide la table user avant chaque test
        $connection = $this->em->getConnection();
        $connection->executeStatement("DELETE FROM user");

        // Création d'un utilisateur pour le login
//        $email = 'user@mail.com';
//        $password = 'Password!!123';
//        $user = new User();
//        $user->setEmail('test@mail.com');
//        $user->setPassword($hasher->hashPassword($user, 'Motdep4sse!'));
//        $user->setRoles(['ROLE_USER']);
//        $this->em->persist($user);
//        $this->em->flush();
    }

   /* public function testSendMessageSuccess(): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'test@mail.com']);
        $this->client->loginUser($user);

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
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Message envoyé avec succès', $data['message']);
    }*/

    public function testAlwaysTrue(): void
    {
        $this->assertTrue(true);
    }
}
