<?php

namespace App\Event;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class UpdateLoginUserSubscriber implements EventSubscriberInterface
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        //Ecouter l'evenement de connexion reussie et appeler la methode onLoginSuccess
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        //Récupérer l'utilisateur connecté
        $user = $event->getUser();

        //Vérifier que l'utilisateur est bien un objet et qu'il possède la méthode setLastLogin
        if (!is_object($user) || !method_exists($user, 'setLastLogin')) {
            return;
        }

        //Mettre à jour la date de dernière connexion
        $user->setLastLogin(new \DateTime());
        $this->entityManager->flush();
    }

}
