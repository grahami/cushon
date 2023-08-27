<?php

namespace App\Controller;

use App\Entity\Investment;
use App\Exception\ApiException;
use App\Exception\EntityNotFoundException;
use App\Exception\InvestmentException;
use App\Repository\InvestmentRepository;
use App\Repository\IsaRepository;
use App\Repository\RetailCustomerRepository;
use App\Service\InvestmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;
use Throwable;

class InvestmentController extends AbstractController
{

    public function __construct(
        protected RetailCustomerRepository $retailCustomerRepository,
        protected IsaRepository            $isaRepository,
        protected InvestmentRepository     $investmentRepository,
        protected InvestmentService        $investmentService
    )
    {
    }

    #[Route('/api/retail-customer/{id}', name: 'app_retail_customer', methods: ['GET'])]
    public function getRetailCustomer(Request $request, int $id): JsonResponse
    {
        try {
            $retailCustomer = $this->retailCustomerRepository->findById($id);
        } catch (EntityNotFoundException $exception) {
            // Optionally return appropriate HTTP error codes, such as 406 for Not Acceptable
            return $this->json([
                'errors' => [ApiException::RETAIL_CUSTOMER_NOT_FOUND]
            ]);
        } catch (Throwable $exception) {
            // Log details of unexpected exception
            return $this->json([
                'errors' => [ApiException::GENERIC_ERROR_MESSAGE]
            ]);
        }

        // consider early return pattern ?
        return $this->json([
            'data' => $retailCustomer->getAttributes(),
        ]);
    }

    #[Route('/api/isas', name: 'app_isas', methods: ['GET'])]
    public function getIsaList(Request $request): JsonResponse
    {
        try {
            $isaList = $this->isaRepository->findByParameters([]);

        } catch (Throwable $exception) {
            // Log details of unexpected exception
            return $this->json([
                'errors' => [ApiException::GENERIC_ERROR_MESSAGE]
            ]);
        }

        $data = [];
        foreach ($isaList as $isa) {
            $data[] = $isa->getAttributes();
        }
        return $this->json([
            'data' => $data,
        ]);
    }


    #[Route('/api/investment', name: 'app_investment', methods: ['POST'])]
    public function setInvestment(Request $request): JsonResponse
    {
        $input = $request->toArray();
        $retailCustomerId = $input['retail_customer_id'] ?? null;
        $investments = $input['investments'] ?? [];

        // Validation of input that all required values that have been provided - not complete
        $errors = [];
        if (is_null($retailCustomerId)) {
            $errors[] = 'Retail customer Id is required';
        }

        if (count($investments) !== 1) {
            $errors[] = 'Only one investment currently supported';
        } else {
            // This code written to support multiple investments from one API call in the future
            foreach ($investments as $investmentKey => $investment) {
                $isaId = $investment['isa_id'] ?? null;
                if (is_null($isaId)) {
                    $errors[] = 'ISA Id is required in investment:' . $investmentKey;
                }
                // TODO : Add more validation of the input such as requiring either of lump_sum or monthly_sum
            }
        }

        // Early return for issues with input
        if (!empty($errors)) {
            return $this->json([
                'errors' => $errors,
            ]);
        }

        try {
            $investmentResult = $this->investmentService->invest($retailCustomerId, $investments);
        } catch (InvestmentException $exception) {
            return $this->json([
                'errors' => $exception->getErrors()
            ]);
        } catch (Throwable $exception) {
            // Log details of unexpected exception
            return $this->json([
                'errors' => [ApiException::GENERIC_ERROR_MESSAGE]
            ]);
        }

        return $this->json([
            'data' => $investmentResult
        ]);
    }
}
