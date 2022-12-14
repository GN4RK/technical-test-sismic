<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use OpenApi\Annotations as OA;

class EventController extends AbstractController
{
    /**
     * Read event list
     * @OA\Tag(name="Events")
     */
    #[Route('/api/events', name: 'event_list', methods: ['GET'])]
    public function getEventList(
        EventRepository $eventRepository, SerializerInterface $serializer
        ): JsonResponse
    {
        $eventList = $eventRepository->findAll();
        $context = SerializationContext::create()->setGroups(['getEvents']);
        $jsonEventList = $serializer->serialize($eventList, 'json', $context);

        return new JsonResponse($jsonEventList, Response::HTTP_OK, [], true);
    }
    
    /**
     * Read an event details
     * @OA\Tag(name="Events")
     */
    #[Route('/api/events/{id}', name: 'event_details', methods: ['GET'])]
    public function getEvent(Event $event, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['getEvents']);
        $jsonEvent = $serializer->serialize($event, 'json', $context);
        return new JsonResponse($jsonEvent, Response::HTTP_OK, ['accept' => 'json'], true);
    }
    
    /**
     * Delete an event
     * @OA\Tag(name="Events")
     */
    #[Route('/api/events/{id}', name: 'delete_event', methods: ['DELETE'])]
    public function deleteEvent(Event $event, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($event);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    /**
     * Create a new event
     * @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *           example={
     *               "name":       "show",
     *               "start":      "2000-01-01T00:00:00+00:00",
     *               "ending":     "2000-01-01T00:00:00+00:00",
     *               "places":     15
     *           },
     *      )
     * )
     * @OA\Tag(name="Events")
     */
    #[Route('/api/events', name: 'create_event', methods: ['POST'])]
    public function createEvent(
        Request $request, SerializerInterface $serializer, EntityManagerInterface $em, 
        UrlGeneratorInterface $urlGenerator
        ): JsonResponse
    {
        $event = $serializer->deserialize($request->getContent(), Event::class, 'json');
        $em->persist($event);
        $em->flush();

        $context = SerializationContext::create()->setGroups(['getEvents']);
        $jsonEvent = $serializer->serialize($event, 'json', $context);
        $location = $urlGenerator->generate(
            'event_details', ['id' => $event->getId()], UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($jsonEvent, Response::HTTP_CREATED, ['location' => $location], true);
    }
    
    /**
     * Update an event
     * @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *           example={
     *               "name":       "showUpDate",
     *               "start":      "2000-01-01T00:00:00+00:00",
     *               "ending":     "2000-01-01T00:00:00+00:00",
     *               "places":     15
     *           },
     *      )
     * )
     * @OA\Tag(name="Events")
     */
    #[Route('/api/events/{id}', name: 'update_event', methods: ['PUT'])]
    public function updateEvent(
        Request $request, SerializerInterface $serializer, 
        Event $currentEvent, EntityManagerInterface $em,
        ): JsonResponse
    {
        $newEvent = $serializer->deserialize($request->getContent(), Event::class, 'json');
        $currentEvent->setName($newEvent->getName());
        $currentEvent->setStart($newEvent->getStart());
        $currentEvent->setEnding($newEvent->getEnding());
        $currentEvent->setPlaces($newEvent->getPlaces());

        $em->persist($currentEvent);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }


}
