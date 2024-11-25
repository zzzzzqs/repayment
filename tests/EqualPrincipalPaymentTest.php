<?php

namespace Zzzzzqs\Repayment\Tests;

use PHPUnit\Framework\TestCase;
use Zzzzzqs\Repayment\Contracts\PaymentCalculatorInterface;
use Zzzzzqs\Repayment\EqualPrincipalPaymentCalculator;

class EqualPrincipalPaymentTest extends TestCase
{
    public function test_config_repayment()
    {
        $this->assertEquals('2', config('repayment.digit'));
    }

    public function test_total_principal_and_interest()
    {
        $principal = 120000;
        $yearInterestRate = "0.0486";
        $year = 10;

//        $principal = new EqualPrincipalPaymentCalculator($principal, $yearInterestRate, $year);
        $principal = app(PaymentCalculatorInterface::class, ['epc', $principal, $yearInterestRate, $year]);

        $result = $principal->getResult();

        $totalPrincipal = collect($result)->sum('total_money');
        $totalInterest = collect($result)->sum('interest');

        $this->assertEquals('149403', $totalPrincipal);
        $this->assertEquals('29403', $totalInterest);
    }
}