<?php

namespace tests;

use App\Entity\Investment;
use App\Repository\IsaRepository;
use App\Repository\RetailCustomerRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

final class InvestmentTest extends KernelTestCase
{
    private Investment $investment;

    protected function setUp(): void
    {
        self::bootKernel();
        $retailCustomerRepository = $this->createMock(RetailCustomerRepository::class);
        $isaRepository = $this->createMock(IsaRepository::class);
        $this->investment = new Investment($retailCustomerRepository, $isaRepository);
    }

    public function testGetAttributesWithMoney(): void
    {
        $now = new DateTimeImmutable();
        $investment = $this->investment->create(-1, 10, 20, $now, 9876.54, 123.45);

        $attributes = $investment->getAttributes();
        $expected = [
            'id' => -1,
            'retailCustomerId' => 10,
            'isaId' => 20,
            'investedAt' => $now->format('Y-m-d H:i:s'),
            'lumpSum' => 9876.54,
            'monthlySum' => 123.45,
        ];

        $this->assertSame($expected, $attributes);
    }

    public function testGetAttributesWithFloat(): void
    {
        $now = new DateTimeImmutable();
        $investment = $this->investment->create(-1, 10, 20, $now, 9876.54321, 123.4567);

        $attributes = $investment->getAttributes();
        $expected = [
            'id' => -1,
            'retailCustomerId' => 10,
            'isaId' => 20,
            'investedAt' => $now->format('Y-m-d H:i:s'),
            'lumpSum' => 9876.54,
            'monthlySum' => 123.45,
        ];

        $this->assertSame($expected, $attributes);
    }

}