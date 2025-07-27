<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $street;

    #[ORM\Column(length: 100)]
    private string $city;

    #[ORM\Column(length: 10)]
    private string $zip;

    #[ORM\Column(length: 100)]
    private string $country;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private Company $company;

    // Getter/Setter
}
