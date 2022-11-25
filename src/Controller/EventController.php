<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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


}
