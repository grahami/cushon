<?php

namespace App\Entity\Interface;

interface EntityInterface
{
    /**
     * @return array<mixed>
     */
    public function getAttributes(): array;
}