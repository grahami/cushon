<?php

namespace App\Entity;

use App\Exception\InvalidIsaYearException;

class Lisa extends AbstractIsa
{
    public function __construct()
    {
        $this->type = 'LISA';
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
        // The limit for a LISA appears to be a flat £4000
        return 4000;
    }

}