<?php

namespace App\Controller;

use App\Entity\Deplacement;
use App\Form\DeplacementType;
use App\Repository\DeplacementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security; 


#[Route('/deplacement')]
final class DeplacementController extends AbstractController
{

    
    #[Route(name: 'app_deplacement_index', methods: ['GET'])]
    public function index(DeplacementRepository $deplacementRepository): Response
    {
        return $this->render('deplacement/index.html.twig', [
            'deplacements' => $deplacementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_deplacement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $deplacement = new Deplacement();
        $form = $this->createForm(DeplacementType::class, $deplacement);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'utilisateur connecté
            $user = $security->getUser();
    
            if (!$user) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour créer un déplacement.');
            }
    
            // Récupérer les valeurs des champs visibles depuis la requête
            $adresseDepartVisible = $request->request->get('adresseDepartVisible');
            $adresseArriveVisible = $request->request->get('adresseArriveVisible');
            $distance = $request->request->get('distanceCache');  // Récupérer la distance depuis le champ caché
    
            // Calculer le coût : 0,50 € par kilomètre et arrondir à 2 chiffres après la virgule
            $cout = round($distance * 0.50, 2);
    
            // Assigner les valeurs récupérées aux champs de l'entité
            $deplacement->setAdresseDepart($adresseDepartVisible);
            $deplacement->setAdresseArrive($adresseArriveVisible);
            $deplacement->setDistance($distance);
            $deplacement->setCout($cout);
    
            // Définir l'état à "Non" par défaut lors de la création du déplacement
            $deplacement->setEtat('Non');
    
            // Associer l'utilisateur connecté au déplacement
            $deplacement->setUser($user);
    
            // Sauvegarder l'entité dans la base de données
            $entityManager->persist($deplacement);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_deplacement_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('deplacement/new.html.twig', [
            'deplacement' => $deplacement,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_deplacement_show', methods: ['GET'])]
    public function show(Deplacement $deplacement): Response
    {
        return $this->render('deplacement/show.html.twig', [
            'deplacement' => $deplacement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_deplacement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Deplacement $deplacement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DeplacementType::class, $deplacement);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les nouvelles adresses si elles ont été modifiées
            $adresseDepartVisible = $request->request->get('adresseDepartVisible');
            $adresseArriveVisible = $request->request->get('adresseArriveVisible');
            $distance = $request->request->get('distanceCache');  // Recalculer la distance si modifiée
    
            // Convertir la distance en nombre flottant
            $distance = floatval($distance);
    
            // Calculer le nouveau coût : 0,50 € par kilomètre et arrondir à 2 chiffres après la virgule
            $cout = round($distance * 0.50, 2);
    
            // Assigner les nouvelles valeurs récupérées aux champs de l'entité si elles ont changé
            if ($adresseDepartVisible !== $deplacement->getAdresseDepart()) {
                $deplacement->setAdresseDepart($adresseDepartVisible);
            }
            if ($adresseArriveVisible !== $deplacement->getAdresseArrive()) {
                $deplacement->setAdresseArrive($adresseArriveVisible);
            }
    
            // Mettre à jour la distance et le coût
            $deplacement->setDistance($distance);
            $deplacement->setCout($cout);
    
            // Enregistrer les modifications dans la base de données
            $entityManager->flush();
    
            return $this->redirectToRoute('app_deplacement_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('deplacement/edit.html.twig', [
            'deplacement' => $deplacement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_deplacement_delete', methods: ['POST'])]
    public function delete(Request $request, Deplacement $deplacement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deplacement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($deplacement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_deplacement_index', [], Response::HTTP_SEE_OTHER);
    }
}
