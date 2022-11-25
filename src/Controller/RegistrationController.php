<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\RegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/registrations/{id}', name: 'registration_list', methods: ['GET'])]
    public function getRegistrationList(Event $event, SerializerInterface $serializer): JsonResponse
    {
        $jsonRegistration = $serializer->serialize($event, 'json', ['groups' => 'getRegistrations']);
        return new JsonResponse($jsonRegistration, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/registrations/{idEvent}/{idRegistration}', name: 'registration_details', methods: ['GET'])]
    public function getRegistrationDetails(
        int $idEvent, int $idRegistration, RegistrationRepository $registrationRepository, EventRepository $eventRepository, SerializerInterface $serializer
    ): JsonResponse
    {
        $registration = $registrationRepository->find($idRegistration);
        $event = $eventRepository->find($idEvent);
        
        if (!$event) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
        
        // check if event and registration are linked
        if (!$event->getRegistrations()->contains($registration)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        if ($registration) {
            $jsonRegistration = $serializer->serialize($registration, 'json', ['groups' => 'getRegistrations']);
            return new JsonResponse($jsonRegistration, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
