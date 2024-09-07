<?php

namespace App\Controller;

use App\Entity\TypesAbsences;
use App\Form\TypesAbsencesType;
use App\Repository\TypesAbsencesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/types-absences')]
final class TypesAbsencesController extends AbstractController
{
    #[Route(name: 'app_types_absences_index', methods: ['GET'])]
    public function index(TypesAbsencesRepository $typesAbsencesRepository): Response
    {
        return $this->render('types_absences/index.html.twig', [
            'types_absences' => $typesAbsencesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_types_absences_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typesAbsence = new TypesAbsences();
        $form = $this->createForm(TypesAbsencesType::class, $typesAbsence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typesAbsence);
            $entityManager->flush();

            return $this->redirectToRoute('app_types_absences_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('types_absences/new.html.twig', [
            'types_absence' => $typesAbsence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_types_absences_show', methods: ['GET'])]
    public function show(TypesAbsences $typesAbsence): Response
    {
        return $this->render('types_absences/show.html.twig', [
            'types_absence' => $typesAbsence,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_types_absences_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypesAbsences $typesAbsence, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypesAbsencesType::class, $typesAbsence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_types_absences_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('types_absences/edit.html.twig', [
            'types_absence' => $typesAbsence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_types_absences_delete', methods: ['POST'])]
    public function delete(Request $request, TypesAbsences $typesAbsence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typesAbsence->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($typesAbsence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_types_absences_index', [], Response::HTTP_SEE_OTHER);
    }
}
