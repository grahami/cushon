<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Repository\IsaRepository;
use App\Repository\RetailCustomerRepository;
use DateTimeImmutable;

class Investment implements EntityInterface
{
    // Assume a 64 bit platform to support $id as equivalent of MySQL BIGINT
    // An id of -1 means that the Investment has not been saved to the database and assigned an id value
    protected int $id = -1;
    protected int $retailCustomerId;
    protected int $isaId;
    protected DateTimeImmutable $investedAt;
    protected int $lumpSum;     // value in pence to avoid any floating point issues
    protected int $monthlySum;  // value in pence to avoid any floating point issues

    public function __construct(
        protected RetailCustomerRepository $retailCustomerRepository,
        protected IsaRepository            $isaRepository
    )
    {
    }

    public function create(
        int               $id,
        int               $retailCustomerId,
        int               $isaId,
        DateTimeImmutable $investedAt,
        float             $lumpSum,
        float             $monthlySum
    ): Investment
    {
        $investment = new self($this->retailCustomerRepository, $this->isaRepository);
        $investment->setId($id);
        $investment->setRetailCustomerId($retailCustomerId);
        $investment->setIsaId($isaId);
        $investment->setInvestedAt($investedAt);
        $investment->setLumpSum((int)($lumpSum * 100));
        $investment->setMonthlySum((int)($monthlySum * 100));

        return $investment;
    }

    public function getAttributes(): array
    {
        return [
            'id' => $this->getId(),
            'retailCustomerId' => $this->getRetailCustomerId(),
            'isaId' => $this->getIsaId(),
            'investedAt' => $this->getInvestedAt()->format('Y-m-d H:i:s'),
            'lumpSum' => $this->getLumpSum() / 100,
            'monthlySum' => $this->getMonthlySum() / 100,
        ];
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setRetailCustomerId(int $retailCustomerId): void
    {
        $this->retailCustomerId = $retailCustomerId;
    }

    public function setIsaId(int $isaId): void
    {
        $this->isaId = $isaId;
    }

    public function setInvestedAt(DateTimeImmutable $investedAt): void
    {
        $this->investedAt = $investedAt;
    }

    public function setLumpSum(int $lumpSum): void
    {
        $this->lumpSum = $lumpSum;
    }

    public function setMonthlySum(int $monthlySum): void
    {
        $this->monthlySum = $monthlySum;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getRetailCustomerId(): int
    {
        return $this->retailCustomerId;
    }

    public function getIsaId(): int
    {
        return $this->isaId;
    }

    public function getInvestedAt(): DateTimeImmutable
    {
        return $this->investedAt;
    }

    public function getLumpSum(): int
    {
        return $this->lumpSum;
    }

    public function getMonthlySum(): int
    {
        return $this->monthlySum;
    }

    public function getRetailCustomer(): EntityInterface
    {
        return $this->retailCustomerRepository->findById($this->getRetailCustomerId());
    }

    public function getIsa(): EntityInterface
    {
        return $this->isaRepository->findById($this->getIsaId());
    }

}