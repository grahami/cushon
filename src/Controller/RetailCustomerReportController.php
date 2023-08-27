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

class RetailCustomerReportController extends AbstractController
{

    public function __construct(
        protected RetailCustomerRepository $retailCustomerRepository
    )
    {
    }

    #[Route('/report/retail-customer-investments/{id}', name: 'app_retail_customer_report', methods: ['GET'])]
    public function getRetailCustomerReport(Request $request, int $id): JsonResponse
    {
        try {
            $retailCustomerInvestments = $this->retailCustomerRepository->getFullInvestmentReport($id);
        } catch (Throwable $exception) {
            // Log details of unexpected exception
            return $this->json([
                'errors' => [ApiException::GENERIC_ERROR_MESSAGE]
            ]);
        }
        return $this->json([
            'data' => $retailCustomerInvestments,
        ]);

    }
}
