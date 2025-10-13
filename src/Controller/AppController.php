<?php

namespace App\Controller;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, ValidatorInterface $validator, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($userRepository->findOneBy(['email' => $data['email'] ?? ''])) {
            return $this->json(['message' => 'Email déjà utilisé'], 422);
        }

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

    #[Route('/request-reset-password', name: 'app_request_reset_password', methods: ['POST'])]
    public function requestResetPassword(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy(['email' => $data['email'] ?? '']);
        if (!$user) {
            return $this->json(['message' => 'Utilisateur non trouvé'], 422);
        }

        return $this->json(['message' => 'Lien de réinitialisation du mot de passe envoyé à votre adresse email'], 200);

    }


    #[Route('/reset-password', name: 'app_reset_password')]
    public function resetPassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher,
                                  ValidatorInterface $validator, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy(['email' => $data['email'] ?? '']);
        if (!$user) {
            return $this->json(['message' => 'Utilisateur non trouvé'], 422);
        }

        $user->setPassword($data['password'] ?? '');
        $violations = $validator->validate($user);
        $errorResponse = $this->validateUser($violations);
        if ($errorResponse) {
            return $errorResponse;
        }

        return $this->persistUser($em, $user, $data['password'], $hasher, 'Mot de passe réinitialisé avec succès');
    }

    #[Route('/send-message', name: 'app_send_message', methods: ['POST'])]
    public function sendMessage(Request $request, MailerInterface $mailer, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        //recupere le user connecté
        $userConnected = $this->getUser();
        $user = $userRepository->findOneBy(['email' => $userConnected->getUserIdentifier()]);

        //recupere le sujet et le message
        $subject = (string) $request->request->get('subject', '');
        $content = (string) $request->request->get('message', '');
        $data = [
            'subject' => $subject,
            'message' => $content
        ];
        $emailSender = $user->getEmail();

        $file = $request->files->get('attachment');

        // crée l'email
        $email = (new Email())
            ->from($emailSender)
            ->to('test@localhost') // faux destinataire
            ->subject($subject)
            ->text($content);

        // ajout de message pour persister dans la bdd
        $message = new Message();
        $message->setUser($user);
        $message->setSubject($subject);
        $message->setMessage($content);
        $message->setSentAt(new \DateTime('now'));

        // vérifications sur le fichier (type, taille, existence)
        if ($file){

            $fileSize = $file->getSize();
            $maxSize = 2 * 1024 * 1024; //2Mo max

            if (!$fileSize) {
                return $this->json(['message' => 'Le fichier joint est vide ou n’a pas été transmis correctement.'], 422);
            }
            if ($fileSize > $maxSize) {
                return $this->json(['message' => 'Le fichier joint dépasse la taille maximale de 2 Mo.'], 422);
            }

            $allowedMimeTypes = [
                'application/pdf',
            ];
            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, $allowedMimeTypes)) {
                return $this->json([
                    'message' => 'Seuls les fichiers PDF sont autorisés.',
                    'mimeType' => $mimeType
                ], 422);
            }

            $filePath = $file->getPathname();
            if (!$filePath) {
                return $this->json(['message' => 'Le fichier joint est invalide ou n’a pas été transmis correctement.'], 422);
            }
            if (!file_exists($filePath) || !is_readable($filePath)) {
                return $this->json(['message' => 'Le fichier joint n’existe pas ou n’est pas lisible.'], 422);
            }
            $email->attachFromPath($filePath, $file->getClientOriginalName());
            $message->setAttachmentFileName($file->getClientOriginalName());
            $message->setAttachmentSize($file->getSize());
        }

        // limite d'envoi à 1 message par heure
        $lastEmailSent = $user->getLastEmailSent();
        $now = new \DateTime('now');
        if ($lastEmailSent && $now->getTimestamp() - $lastEmailSent->getTimestamp() < 3600) {
            return $this->json(['message' => 'Vous ne pouvez envoyer qu\'un message toutes les heures.'], 429);
        }
        else{
            //ajout de la date d'envoi et envoi du message
            $user->setLastEmailSent(new \DateTime('now'));
            $em->persist($user);
            $mailer->send($email);
            $em->persist($message);
            $em->flush();
        }

        return $this->json(['message' => 'Message envoyé avec succès', 'data' => $data], 200);
    }

}
