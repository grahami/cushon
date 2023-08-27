<?php

namespace App\Exception;

use Exception;
class InvestmentException extends Exception
{

    /**
     * @var array<string>
     */
    protected array $errors = [];

    /**
     * @param array<string> $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}