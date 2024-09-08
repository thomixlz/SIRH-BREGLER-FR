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
use App\Repository\InfoRepository;
use App\Entity\Info;
use App\Repository\EquipeRepository;
use App\Entity\Equipe;

use App\Repository\DeplacementRepository;
use App\Repository\AbsencesRepository;
use App\Repository\ContactRepository;







class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Security $security, 
        InfoRepository $infoRepo, 
        AbsencesRepository $absencesRepo, 
        DeplacementRepository $deplacementRepo, 
        ContactRepository $contactRepo
    ): Response {
        // Récupérer les informations de la base de données
        $info = $infoRepo->findAll();
        
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Compter les absences par état
        $absencesNon = $absencesRepo->count(['etat' => 'Non']);
        $absencesAttente = $absencesRepo->count(['etat' => 'Attente']);
        $absencesOui = $absencesRepo->count(['etat' => 'Oui']);
        
        // Compter les déplacements par état
        $deplacementsNon = $deplacementRepo->count(['etat' => 'Non']);
        $deplacementsAttente = $deplacementRepo->count(['etat' => 'Attente']);
        $deplacementsOui = $deplacementRepo->count(['etat' => 'Oui']);
        
        // Compter les contacts par état
        $contactsNon = $contactRepo->count(['etat' => 'Non']);
        $contactsAttente = $contactRepo->count(['etat' => 'Attente']);
        $contactsOui = $contactRepo->count(['etat' => 'Oui']);

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'info' => $info,
            'absencesNon' => $absencesNon,
            'absencesAttente' => $absencesAttente,
            'absencesOui' => $absencesOui,
            'deplacementsNon' => $deplacementsNon,
            'deplacementsAttente' => $deplacementsAttente,
            'deplacementsOui' => $deplacementsOui,
            'contactsNon' => $contactsNon,
            'contactsAttente' => $contactsAttente,
            'contactsOui' => $contactsOui,
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
