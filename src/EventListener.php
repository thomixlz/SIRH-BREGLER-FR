<?php
namespace App;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment as TwigEnvironment;
use App\Repository\UserRepository;
use App\Repository\EquipeRepository;

class EventListener implements EventSubscriberInterface
{
    private $twig;
    private $userRepository;
    private $equipeRepository;

    public function __construct(TwigEnvironment $twig, UserRepository $userRepository, EquipeRepository $equipeRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->equipeRepository = $equipeRepository;
    }

    public function onKernelController(ControllerEvent $event)
    {
        // Vérifier les utilisateurs sans équipe
        $usersWithoutTeam = $this->userRepository->findBy(['equipe' => null]);
        $hasUsersWithoutTeam = !empty($usersWithoutTeam);

        // Vérifier les équipes sans responsable
        $teamsWithoutResponsable = $this->equipeRepository->findBy(['Responsable' => null]);
        $hasTeamsWithoutResponsable = !empty($teamsWithoutResponsable);

        // Ajouter les variables globales à Twig
        $this->twig->addGlobal('hasUsersWithoutTeam', $hasUsersWithoutTeam);
        $this->twig->addGlobal('hasTeamsWithoutResponsable', $hasTeamsWithoutResponsable);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}