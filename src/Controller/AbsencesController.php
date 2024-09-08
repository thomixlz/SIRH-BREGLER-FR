<?php

namespace App\Controller;

use App\Entity\Absences;
use App\Form\AbsencesType;
use App\Repository\AbsencesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security; 


#[Route('/absences')]
final class AbsencesController extends AbstractController
{
    #[Route(name: 'app_absences_index', methods: ['GET'])]
    public function index(AbsencesRepository $absencesRepository, Security $security): Response
    {

        $user = $security->getUser();

        if ($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_RESPONSABLE_HIERA') || $this->isGranted('ROLE_REFERENT_FRAIS')) {
            $absences = $absencesRepository->findBy(['user' => $user]);
        } else {
            $absences = $absencesRepository->findAll();
        }

        return $this->render('absences/index.html.twig', [
            'absences' => $absences,
        ]);
    }

   
    #[Route('/new', name: 'app_absences_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $absence = new Absences();
        
        $form = $this->createForm(AbsencesType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            
            $file = $form->get('document')->getData();
            
            if($file != 'ok') {
                $path = '/';
                $fileName = str_replace(' ','_',$file->getClientOriginalName());

                $file->move(
                    $this->getParameter('justificatif_absence_directory').$path,
                    $fileName
                );
                $absence->setDocument($fileName);
            }
            
            
            $user = $security->getUser();
            
            if (!$user) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour créer un déplacement.');
            }
    
            // Vérification si la date de création n'a pas été définie
            if (null === $absence->getCreatedAt()) {
                $absence->setCreatedAt(new \DateTime());
            }

            // Définir également la date de mise à jour à la création
            $absence->setUpdatedAt(new \DateTime());

            $absence->setEtat('Non');
            $absence->setUser($user);


            $entityManager->persist($absence);
            $entityManager->flush();

            return $this->redirectToRoute('app_absences_index');
        }

        return $this->render('absences/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_absences_show', methods: ['GET'])]
    public function show(Absences $absence): Response
    {
        $user = $absence->getUser();

        return $this->render('absences/show.html.twig', [
            'absence' => $absence,
            'user' => $user,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_absences_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Absences $absence, EntityManagerInterface $entityManager): Response
    {

        $user = $absence->getUser();
        $oldDocument = $absence->getDocument();

        $form = $this->createForm(AbsencesType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('document')->getData();

            if ($file && $file != 'ok') {
                if ($oldDocument && $oldDocument != 'ok') {
                    $oldDocPath = $this->getParameter('justificatif_absence_directory') . '/' . $oldDocument;
                    if (file_exists($oldDocPath)) {
                        unlink($oldDocPath);
                    }
                }

                $fileName = str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(
                    $this->getParameter('justificatif_absence_directory'),
                    $fileName
                );
                $absence->setDocument($fileName);
            } else {
                $absence->setDocument($oldDocument);
            }

            
            
            // Met à jour explicitement l'heure de modification
            $absence->setUpdatedAt(new \DateTime());

            $entityManager->flush();

            return $this->redirectToRoute('app_absences_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('absences/edit.html.twig', [
            'absence' => $absence,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_absences_delete', methods: ['POST'])]
    public function delete(Request $request, Absences $absence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$absence->getId(), $request->getPayload()->getString('_token'))) {
            
            if($absence->getDocument() and $absence->getDocument() != "ok")
            {
                $lien = '../public/uploads/absences/'.$absence->getDocument();
                unlink($lien);
            }


            $entityManager->remove($absence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_absences_index', [], Response::HTTP_SEE_OTHER);
    }
}
