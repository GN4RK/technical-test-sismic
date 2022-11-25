<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\RegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/registrations/{id}', name: 'registration_list', methods: ['GET'])]
    public function getRegistrationList(Event $event, RegistrationRepository $registrationRepository, SerializerInterface $serializer): JsonResponse
    {
        $jsonRegistration = $serializer->serialize($event, 'json', ['groups' => 'getRegistrations']);
        return new JsonResponse($jsonRegistration, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
