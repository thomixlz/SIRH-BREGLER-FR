<?php
// src/EventSubscriber/RequestSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

// use Doctrine\ORM\EntityManagerInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $user = $this->security->getUser();

        if ($user && !in_array('ROLE_USER', $user->getRoles())) {
            $user->setRoles(['ROLE_USER']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->security->setToken($token);
        }

    }
}
