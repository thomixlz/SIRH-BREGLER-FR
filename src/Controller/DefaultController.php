<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Repository\EquipeRepository;
use App\Entity\Equipe;






class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Security $security): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/calendrier', name: 'app_calendrier')]
    public function calendrier(EquipeRepository $equipeRepo, UserRepository $userRepo,Security $security, EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les utilisateurs et les équipes
        $users = $userRepo->findAll();
        $equipes = $equipeRepo->findAll();;

        return $this->render('default/calendrier.html.twig', [
            'users' => $users,
            'equipes' => $equipes,
        ]);
    }
}
