<?php

namespace App\Entity;

use App\Repository\RegistrationRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "registration_details",
 *          parameters = { "idEvent" = "expr(object.getEvent().getId())", "idRegistration" = "expr(object.getId())",  }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getRegistrations")
 * )
 * 
 * @Hateoas\Relation(
 *      "update",
 *      href = @Hateoas\Route(
 *          "update_registration",
 *          parameters = { "idEvent" = "expr(object.getEvent().getId())", "idRegistration" = "expr(object.getId())",  }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getRegistrations")
 * )
 * 
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "delete_registration",
 *          parameters = { "idEvent" = "expr(object.getEvent().getId())", "idRegistration" = "expr(object.getId())",  }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getRegistrations")
 * )
 *
 * 
 */

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
class Registration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getRegistrations"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getRegistrations"])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getRegistrations"])]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getRegistrations"])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getRegistrations"])]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'registrations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }
}
