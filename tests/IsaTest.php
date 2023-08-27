<?php

use App\Entity\RetailCustomer;
use App\Entity\Isa;
use App\Entity\Jisa;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class IsaTest extends KernelTestCase
{

    public function testIsaCustomerIsEligible(): void
    {
        $now = new DateTimeImmutable();
        $dob = $now->sub(new DateInterval('P20Y'));
        $retailCustomer = new RetailCustomer();
        $retailCustomer = $retailCustomer->create(
            -1,
            'Test',
            'Customer',
            'test@test.com',
            $dob
        );

        $isa = new Isa();
        $this->assertTrue($isa->isCustomerEligible($retailCustomer));
    }

    public function testIsaCustomerIsNotEligible(): void
    {
        $now = new DateTimeImmutable();
        $dob = $now->sub(new DateInterval('P12Y'));
        $retailCustomer = new RetailCustomer();
        $retailCustomer = $retailCustomer->create(
            -1,
            'Test',
            'Customer',
            'test@test.com',
            $dob
        );

        $isa = new Isa();
        $this->assertFalse($isa->isCustomerEligible($retailCustomer));
    }

    public function testJisaCustomerIsEligible(): void
    {
        $now = new DateTimeImmutable();
        $dob = $now->sub(new DateInterval('P15Y'));
        $retailCustomer = new RetailCustomer();
        $retailCustomer = $retailCustomer->create(
            -1,
            'Test',
            'Customer',
            'test@test.com',
            $dob
        );

        $isa = new Jisa();
        $this->assertTrue($isa->isCustomerEligible($retailCustomer));
    }

    public function testJisaCustomerIsNotEligible(): void
    {
        $now = new DateTimeImmutable();
        $dob = $now->sub(new DateInterval('P17Y'));
        $retailCustomer = new RetailCustomer();
        $retailCustomer = $retailCustomer->create(
            -1,
            'Test',
            'Customer',
            'test@test.com',
            $dob
        );

        $isa = new Jisa();
        $this->assertFalse($isa->isCustomerEligible($retailCustomer));
    }

}