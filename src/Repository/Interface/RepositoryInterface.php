<?php

namespace App\Repository\Interface;

use App\Entity\Interface\EntityInterface;

interface RepositoryInterface
{
    public function findById(int $id): EntityInterface;

}