<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api', name: 'api_')]
final class AppController extends AbstractController
{

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }
        $user->setPassword($hasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();
        return $this->json(['message' => 'Utilisateur créé avec succès'], 201);
    }


    #[Route('/reset-password', name: 'app_reset_password')]
    public function reset_password(): Response
    {
        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout (): Response
    {
        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
