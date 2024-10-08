<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security; 


#[Route('/contact')]
final class ContactController extends AbstractController
{
    #[Route(name: 'app_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository, Security $security): Response
    {

        $user = $security->getUser();

        if ($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_RESPONSABLE_HIERA') || $this->isGranted('ROLE_RTT') || $this->isGranted('ROLE_REFERENT_FRAIS')){
            $contact = $contactRepository->findBy(['user' => $user]);
        } else {
            $contact = $contactRepository->findAll();
        }

        return $this->render('contact/index.html.twig', [
            'contacts' => $contact,
        ]);

       
    }

    #[Route('/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer automatiquement la date actuelle
            $contact->setDate(new \DateTime());
            $contact->setEtat("Non");

            // Associer l'utilisateur connecté au message
            $contact->setUser($this->getUser());

            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        $user = $contact->getUser();

        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
            'user' => $user,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {

        $user = $contact->getUser();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
            'user' => $user,

        ]);
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
