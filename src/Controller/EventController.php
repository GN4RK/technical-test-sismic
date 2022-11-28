<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class EventController extends AbstractController
{
    #[Route('/api/events', name: 'event_list', methods: ['GET'])]
    public function getEventList(EventRepository $eventRepository, SerializerInterface $serializer): JsonResponse
    {
        $eventList = $eventRepository->findAll();
        $jsonEventList = $serializer->serialize($eventList, 'json', ['groups' => 'getEvents']);

        return new JsonResponse($jsonEventList, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/events/{id}', name: 'event_details', methods: ['GET'])]
    public function getEvent(Event $event, SerializerInterface $serializer): JsonResponse
    {
        $jsonEvent = $serializer->serialize($event, 'json', ['groups' => 'getEvents']);
        return new JsonResponse($jsonEvent, Response::HTTP_OK, ['accept' => 'json'], true);
    }
    
    #[Route('/api/events/{id}', name: 'delete_event', methods: ['DELETE'])]
    public function deleteEvent(Event $event, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($event);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    
    #[Route('/api/events', name: 'create_event', methods: ['POST'])]
    public function createEvent(
        Request $request, SerializerInterface $serializer, EntityManagerInterface $em, 
        UrlGeneratorInterface $urlGenerator
        ): JsonResponse
    {
        $event = $serializer->deserialize($request->getContent(), Event::class, 'json');
        $em->persist($event);
        $em->flush();

        $jsonEvent = $serializer->serialize($event, 'json', ['groups' => 'getEvents']);
        $location = $urlGenerator->generate(
            'event_details', ['id' => $event->getId()], UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($jsonEvent, Response::HTTP_CREATED, ['location' => $location], true);
    }
    
    #[Route('/api/events/{id}', name: 'update_event', methods: ['PUT'])]
    public function updateEvent(
        Request $request, SerializerInterface $serializer, Event $currentEvent, 
        EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator
        ): JsonResponse
    {
        $updatedEvent = $serializer->deserialize(
            $request->getContent(), 
            Event::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentEvent]
        );

        $em->persist($updatedEvent);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }


}
