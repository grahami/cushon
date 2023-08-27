<?php

namespace App\Entity;

use App\Exception\InvalidIsaYearException;

class Jisa extends AbstractIsa
{
    public function __construct()
    {
        $this->type = 'JISA';
    }

    public function isCustomerEligible(RetailCustomer $retailCustomer): bool
    {
        $age = $retailCustomer->getAge();
        if ($age > 16) {
            return false;
        }
        return true;
    }

    public function getLimit(int $startYear): int
    {
        switch ($startYear) {
            case 2019:
                return 4368;
            case 2020:
                return 9000;
            case 2021:
                return 9000;
            case 2022:
                return 9000;
            case 2023:
                return 9000;
            default:
                throw new InvalidIsaYearException();
        }
    }

}