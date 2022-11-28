<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Registration;
use App\Repository\EventRepository;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/events/{id}/registrations', name: 'registration_list', methods: ['GET'])]
    public function getRegistrationList(Event $event, SerializerInterface $serializer): JsonResponse
    {
        $jsonRegistration = $serializer->serialize($event, 'json', ['groups' => 'getRegistrations']);
        return new JsonResponse($jsonRegistration, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/events/{idEvent}/registrations/{idRegistration}', name: 'registration_details', methods: ['GET'])]
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

    #[Route('/api/events/{id}/registrations', name: 'create_registration', methods: ['POST'])]
    public function createRegistration(
        Event $event, Request $request, SerializerInterface $serializer, 
        EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator
        ): JsonResponse
    {
        if ($event->isFull()) {
            // TODO better error message
            return new JsonResponse("event is full", Response::HTTP_METHOD_NOT_ALLOWED);
        }
        
        $registration = new Registration();
        $registration = $serializer->deserialize($request->getContent(), Registration::class, 'json');
        $registration->setEvent($event);

        $em->persist($registration);
        $em->flush();

        $jsonRegistration = $serializer->serialize($registration, 'json', ['groups' => 'getRegistrations']);
        $location = $urlGenerator->generate(
            'registration_details',[
                'idEvent' => $event->getId(),
                'idRegistration' => $registration->getId()
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($jsonRegistration, Response::HTTP_CREATED, ['location' => $location], true);
    }

    #[Route('/api/events/{idEvent}/registrations/{idRegistration}', name: 'update_registration', methods: ['PUT'])]
    public function updateRegistration(
        int $idEvent, int $idRegistration, Request $request, SerializerInterface $serializer, 
        RegistrationRepository $registrationRepository, EventRepository $eventRepository,
        EntityManagerInterface $em
        ): JsonResponse
    {
        // TODO can't change the event linked to the registration

        $registration = $registrationRepository->find($idRegistration);
        $event = $eventRepository->find($idEvent);

        if (!$event) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
        
        // check if event and registration are linked
        if (!$event->getRegistrations()->contains($registration)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $updatedRegistration = $serializer->deserialize(
            $request->getContent(), 
            Registration::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $registration]
        );

        $em->persist($updatedRegistration);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/events/{idEvent}/registrations/{idRegistration}', name: 'delete_registration', methods: ['DELETE'])]
    public function deleteRegistration(
        int $idEvent, int $idRegistration, EntityManagerInterface $em,
        RegistrationRepository $registrationRepository, EventRepository $eventRepository,
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

        $em->remove($registration);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
