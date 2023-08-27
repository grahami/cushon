<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Exception\UnknownAgeException;
use DateTime;
use DateTimeImmutable;

class RetailCustomer implements EntityInterface
{
    // Assume a 64 bit platform to support $id as equivalent of MySQL BIGINT
    // An id of -1 means that the RetailCustomer has not been saved to the database and assigned an id value
    protected int $id = -1;
    protected string $firstName = '';
    protected string $lastName = '';
    protected string $emailAddress = '';
    protected ?DateTimeImmutable $dob;
    protected ?string $telephone;
    protected ?string $mobile;
    protected ?string $niNumber;
    protected ?string $address1;
    protected ?string $address2;
    protected ?string $city;
    protected ?string $county;
    protected ?string $postCode;

    // For full implementation there would be a complete list of parameters in constructor
    public function create(
        int                $id,
        string             $firstName,
        string             $lastName,
        string             $emailAddress,
        ?DateTimeImmutable $dob
    ): RetailCustomer
    {
        $retailCustomer = new self();
        $retailCustomer->setId($id);
        $retailCustomer->setFirstName($firstName);
        $retailCustomer->setLastName($lastName);
        $retailCustomer->setEmailAddress($emailAddress);
        $retailCustomer->setDob($dob);
        // call remaining setters as appropriate

        return $retailCustomer;
    }

    public function getAttributes(): array
    {
        return [
            'id' => $this->getId(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'emailAddress' => $this->getEmailAddress(),
            'dob' => $this->getDob()->format('Y-m-d'),
        ];
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    public function setDob(DateTimeImmutable $dob): void
    {
        $this->dob = $dob;
    }

    // Other setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function getDob(): ?DateTimeImmutable
    {
        return $this->dob ?? null;
    }

    // Other getters

    public function getAge(): int
    {
        $dob = $this->getDob();
        if (empty($dob)) {
            throw new UnknownAgeException();
        }
        $now = new DateTime();
        $difference = $now->diff($this->getDob());
        return $difference->y;
    }
}