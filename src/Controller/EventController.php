<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/event')]
final class EventController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_event_index', methods: ['GET'])]
public function index(EventRepository $eventRepository): JsonResponse
{
    // Récupérer les événements depuis la base de données
    $events = $eventRepository->findAll();

    // Préparer les données pour FullCalendar
    $eventData = [];
    foreach ($events as $event) {
        $eventData[] = [
            'id' => $event->getId(),
            'title' => $event->getTitre(),
            'start' => $event->getDateDebut()->format('Y-m-d\TH:i:s'),
            'end' => $event->getDateFin()->format('Y-m-d\TH:i:s'),
        ];
    }

    return new JsonResponse($eventData);
}


    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $event = new Event();
    
    // Récupère les dates sélectionnées depuis les paramètres de l'URL
    $start = $request->query->get('start');
    $end = $request->query->get('end');

    if ($start) {
        $event->setDateDebut(new \DateTime($start));
    }

    if ($end) {
        $event->setDateFin(new \DateTime($end));
    }

    $form = $this->createForm(EventType::class, $event);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($event);
        $entityManager->flush();

        // Après ajout, redirige vers le calendrier
        return $this->redirectToRoute('app_calendrier');
    }

    return $this->render('event/new.html.twig', [
        'event' => $event,
        'form' => $form,
    ]);
}

    

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
}
