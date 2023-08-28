<?php

namespace App\Service;

use App\Entity\AbstractIsa;
use App\Entity\Investment;
use App\Entity\RetailCustomer;
use App\Exception\ApiException;
use App\Exception\EntityNotFoundException;
use App\Exception\InvestmentException;
use App\Repository\InvestmentRepository;
use App\Repository\IsaRepository;
use App\Repository\RetailCustomerRepository;
use DateTimeImmutable;
use Throwable;

class InvestmentService
{
    public function __construct(
        protected RetailCustomerRepository $retailCustomerRepository,
        protected IsaRepository            $isaRepository,
        protected InvestmentRepository     $investmentRepository
    )
    {
    }

    /**
     * @param array<mixed> $investments
     * @return array<mixed>
     */
    public function invest(int $retailCustomerId, array $investments): array
    {
        try {
            $retailCustomer = $this->retailCustomerRepository->findById($retailCustomerId);
        } catch (EntityNotFoundException $exception) {
            // Early return for unknown retail customer
            $this->throwException([ApiException::RETAIL_CUSTOMER_NOT_FOUND]);
        } catch (Throwable $exception) {
            // Log an unexpected exception
            $this->throwException([ApiException::GENERIC_ERROR_MESSAGE]);
        }

        $investmentResults = [];
        foreach ($investments as $investmentKey => $investment) {
            $isaId = $investment['isa_id'] ?? null;
            $lumpSum = $investment['lump_sum'] ?? 0;
            $monthlySum = $investment['monthly_sum'] ?? 0;

            try {
                $isa = $this->isaRepository->findById($isaId);
            } catch (EntityNotFoundException $exception) {
                $errors[] = 'Investment: ' . $investmentKey . '-' . ApiException::ISA_NOT_FOUND;
            } catch (Throwable $exception) {
                // Log an unexpected exception
                $errors[] = 'Investment: ' . $investmentKey . '-' . ApiException::GENERIC_ERROR_MESSAGE;
            }

            // Early return for unknown retail customer or isa
            if (!empty($errors)) {
                $this->throwException($errors);
            }

            $investedAt = new DateTimeImmutable();

            // TODO: This logic currently only validates that the amount to be invested does not exceed the annual
            // tax free allowance. When multiple investments are supported, expand the logic to make sure
            // that the sum of all investments does not exceed the limit, including existing investments,
            // and other validation as well, such as how many types of each ISA are being purchased, etc
            try {
                /* @phpstan-ignore-next-line */
                $this->validateInvestment($retailCustomer, $isa, $lumpSum, $investedAt);

                $investment = $this->investmentRepository->create([
                    'id' => -1,
                    'retail_customer_id' => $retailCustomerId,
                    'isa_id' => $isaId,
                    'invested_at' => $investedAt,
                    'lump_sum' => $lumpSum,
                    'monthly_sum' => $monthlySum,
                ]);
                $investmentResults[] = $investment->getAttributes();
            } catch (InvestmentException $exception) {
                $investmentResults[] = ['investment' => $investmentKey, 'errors' => $exception->getErrors()];
            }

        }
        return $investmentResults;
    }

    protected function validateInvestment(
        RetailCustomer    $retailCustomer,
        AbstractIsa       $isa,
        int               $lumpSum,
        DateTimeImmutable $investedAt
    ): void
    {
        $errors = [];
        // Do appropriate validation for Retail Customer, Isa and Amounts
        if (!$isa->isCustomerEligible($retailCustomer)) {
            $errors[] = 'Customer is not eligible to invest in this ISA';
        }

        $investmentDay = (int)$investedAt->format('j');
        $investmentMonth = (int)$investedAt->format('n');
        // The isa start year is from 6 April - 5 April of the next year
        $isaYear = $investedAt->format('Y');
        if ($investmentMonth < 4) {
            $isaYear = $isaYear - 1;
        } elseif ($investmentMonth == 4 && $investmentDay < 6) {
            $isaYear = $isaYear - 1;
        }

        if ($lumpSum > $isa->getLimit($isaYear)) {
            $errors[] = 'The investment amount exceeds the ISA tax free allowance';
        }
        if (!empty($errors)) {
            $this->throwException($errors);
        }
    }

    /**
     * @param array<string> $errors
     */
    protected function throwException(array $errors): void
    {
        $investmentException = new InvestmentException();
        $investmentException->setErrors($errors);
        throw $investmentException;
    }
}