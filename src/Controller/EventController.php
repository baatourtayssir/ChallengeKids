<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventForm;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Route('/api/event', name: 'event_')]
class EventController extends AbstractController
{
    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    #[OA\Response(
        response: 200,
        description: 'Returns list of all events',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Event::class))
        )
    )]
    #[OA\Tag(name: 'Event')]
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function index()
    {
        $list = $this->eventService->getEvents();

        $jsonEvents = [];
        foreach ($list as $key => $event) {
            $jsonEvents[$key] = $this->eventService->toJson($event);
        }

        return new JsonResponse($jsonEvents);
    }

    #[OA\Response(
        response: 200,
        description: 'Creates a new event',
        content: new OA\JsonContent(ref: new Model(type: Event::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            example: [
                'title' => 'Atelier Créatif - Dessine ton héros',
                'description' => 'Un atelier ludique où les enfants peuvent exprimer leur créativité ',
                'date' => '2025-07-10T10:00:00',
                'category' => 1
            ]
        )
    )]
    #[OA\Tag(name: 'Event')]
    #[Route('/add', name: '_add', methods: ['POST'])]
    public function createEvent(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $event = new Event();
        $form = $this->createForm(EventForm::class, $event);
        $form->submit($data);

        $event = $this->eventService->persist($event);
        return new JsonResponse($this->eventService->toJson($event));
    }

    #[OA\Response(
        response: 200,
        description: 'Updates an event',
        content: new OA\JsonContent(ref: new Model(type: Event::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                'title' => 'Atelier Créatif - Dessine ton héros',
                'description' => 'Un atelier ludique où les enfants peuvent exprimer leur créativité ',
                'date' => '2025-07-10T10:00:00',
                'category' => 1
            ]
        )
    )]
    #[OA\Tag(name: 'Event')]
    #[Route('/{id}/edit', name: '_edit', methods: ['PUT'])]
    public function updateEvent(Event $event, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(EventForm::class, $event);
        $form->submit($data, false);

        $event = $this->eventService->persist($event);
        return new JsonResponse($this->eventService->toJson($event));
    }

    #[OA\Response(
        response: 200,
        description: 'Deletes an event',
        content: new OA\JsonContent(type: 'string')
    )]
    #[OA\Tag(name: 'Event')]
    #[Route('/{id}/delete', name: '_delete', methods: ['DELETE'])]
    public function deleteEvent($id, Request $request)
    {
        $event = $this->eventService->get($id);
        try {
            $this->eventService->remove($event);
        } catch (\Exception $e) {
            // handle exceptions
        }

        return new JsonResponse(['message' => 'Event deleted']);
    }
}
