<?php

namespace App\Controller;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api', name: 'api_')]
final class AppController extends AbstractController
{

    /**
     * Valide les violations et retourne une JsonResponse en cas d'erreur, sinon null.
     */
    public function validateUser(ConstraintViolationListInterface $violations): ?JsonResponse
    {
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }
            return $this->json($errors, 422);
        }
        return null;
    }

    /**
     * Persiste l'utilisateur et retourne une JsonResponse de succès personnalisable.
     */
    public function persistUser(EntityManagerInterface $em, User $user, string $plainPassword, UserPasswordHasherInterface $hasher, string $successMessage = 'Utilisateur créé avec succès'): JsonResponse
    {
        $user->setPassword($hasher->hashPassword($user, $plainPassword));
        $em->persist($user);
        $em->flush();
        return $this->json(['message' => $successMessage], 201);
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        $violations = $validator->validate($user);
        $errorResponse = $this->validateUser($violations);
        if ($errorResponse) {
            return $errorResponse;
        }
        $user->setRoles(['ROLE_USER']);
        return $this->persistUser($em, $user, $data['password'], $hasher);
    }


    #[Route('/reset-password', name: 'app_reset_password')]
    public function resetPassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher,
                                  ValidatorInterface $validator, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy(['email' => $data['email'] ?? '']);
        if (!$user) {
            return $this->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $user->setPassword($data['password'] ?? '');
        $violations = $validator->validate($user);
        $errorResponse = $this->validateUser($violations);
        if ($errorResponse) {
            return $errorResponse;
        }

        return $this->persistUser($em, $user, $data['password'], $hasher, 'Mot de passe réinitialisé avec succès');
    }

}
