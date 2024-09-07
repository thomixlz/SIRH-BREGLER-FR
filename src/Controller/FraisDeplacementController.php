<?php

namespace App\Controller;

use App\Entity\FraisDeplacement;
use App\Form\FraisDeplacementType;
use App\Repository\FraisDeplacementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/frais/deplacement')]
final class FraisDeplacementController extends AbstractController
{
    #[Route(name: 'app_frais_deplacement_index', methods: ['GET'])]
    public function index(FraisDeplacementRepository $fraisDeplacementRepository): Response
    {
        return $this->render('frais_deplacement/index.html.twig', [
            'frais_deplacements' => $fraisDeplacementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_frais_deplacement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fraisDeplacement = new FraisDeplacement();
        $form = $this->createForm(FraisDeplacementType::class, $fraisDeplacement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fraisDeplacement);
            $entityManager->flush();

            return $this->redirectToRoute('app_frais_deplacement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frais_deplacement/new.html.twig', [
            'frais_deplacement' => $fraisDeplacement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_frais_deplacement_show', methods: ['GET'])]
    public function show(FraisDeplacement $fraisDeplacement): Response
    {
        return $this->render('frais_deplacement/show.html.twig', [
            'frais_deplacement' => $fraisDeplacement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_frais_deplacement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FraisDeplacement $fraisDeplacement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FraisDeplacementType::class, $fraisDeplacement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_frais_deplacement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frais_deplacement/edit.html.twig', [
            'frais_deplacement' => $fraisDeplacement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_frais_deplacement_delete', methods: ['POST'])]
    public function delete(Request $request, FraisDeplacement $fraisDeplacement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fraisDeplacement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fraisDeplacement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_frais_deplacement_index', [], Response::HTTP_SEE_OTHER);
    }
}
