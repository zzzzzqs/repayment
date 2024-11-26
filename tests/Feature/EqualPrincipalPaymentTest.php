<?php

namespace Zzzzzqs\Repayment\Tests\Feature;

use Zzzzzqs\Repayment\Contracts\PaymentCalculatorInterface;
use Zzzzzqs\Repayment\Tests\TestCase;
use function app;
use function collect;
use function config;

class EqualPrincipalPaymentTest extends TestCase
{
    public function test_config_repayment()
    {
        // 断言是否能读到配置
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