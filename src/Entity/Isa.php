<?php

namespace App\Entity;

use App\Exception\InvalidIsaYearException;

class Isa extends AbstractIsa
{
    public function __construct()
    {
        $this->type = 'ISA';
    }

    public function isCustomerEligible(RetailCustomer $retailCustomer): bool
    {
        $age = $retailCustomer->getAge();
        if ($age < 18) {
            return false;
        }
        return true;
    }

    public function getLimit(int $startYear): int
    {
        switch ($startYear) {
            case 2019:
                return 20000;
            case 2020:
                return 20000;
            case 2021:
                return 20000;
            case 2022:
                return 20000;
            case 2023:
                return 20000;
            default:
                throw new InvalidIsaYearException();
        }
    }
}