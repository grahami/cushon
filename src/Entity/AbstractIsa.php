<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Exception\UnknownIsaTypeException;

abstract class AbstractIsa implements EntityInterface
{
    // Assume a 64 bit platform to support $id as equivalent of MySQL BIGINT
    // An id of -1 means that the Isa has not been saved to the database and assigned an id value
    protected int $id = -1;
    protected string $name = '';
    protected string $type = '';
    protected string $riskDetails = '';
    protected string $chargeDetails = '';

    public static function create(
        int    $id,
        string $name,
        string $type,
        string $riskDetails,
        string $chargeDetails,
    ): AbstractIsa
    {
        switch ($type) {
            case 'ISA':
                $isa = new Isa();
                $isa->setId($id);
                $isa->setName($name);
                $isa->setRiskDetails($riskDetails);
                $isa->setChargeDetails($chargeDetails);
                break;
            case 'JISA':
                $isa = new Jisa();
                $isa->setId($id);
                $isa->setName($name);
                $isa->setRiskDetails($riskDetails);
                $isa->setChargeDetails($chargeDetails);
                break;
            case 'LISA':
                $isa = new Lisa();
                $isa->setId($id);
                $isa->setName($name);
                $isa->setRiskDetails($riskDetails);
                $isa->setChargeDetails($chargeDetails);
                break;
            default:
                // Log that we have an unknown ISA type
                throw new UnknownIsaTypeException();
        }

        return $isa;
    }

    abstract public function isCustomerEligible(RetailCustomer $retailCustomer): bool;

    abstract public function getLimit(int $startYear): int;

    public function getAttributes(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'riskDetails' => $this->getRiskDetails(),
            'chargeDetails' => $this->getChargeDetails()
        ];
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setRiskDetails(string $riskDetails): void
    {
        $this->riskDetails = $riskDetails;
    }

    public function setChargeDetails(string $chargeDetails): void
    {
        $this->chargeDetails = $chargeDetails;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRiskDetails(): string
    {
        return $this->riskDetails;
    }

    public function getChargeDetails(): string
    {
        return $this->chargeDetails;
    }
}