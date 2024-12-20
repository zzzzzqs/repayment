<?php

namespace Zzzzzqs\Repayment\Tests\Feature;

use Zzzzzqs\Repayment\Factories\PaymentCalculatorFactory;
use Zzzzzqs\Repayment\Tests\TestCase;
use function app;
use function config;

class PaymentTest extends TestCase
{
    protected float $delta = 0.001;
    protected $calculatorFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->calculatorFactory = app(PaymentCalculatorFactory::class);
    }

    public function test_config_repayment()
    {
        // 断言是否能读到配置
        $this->assertEquals(2, config('repayment.digit'));
    }

    /**
     * 等额本金还款
     * @dataProvider epcDataProvider
     */
    public function test_epc($principal, $yearInterestRate, $year, $expectedTotalPrincipal, $expectedTotalInterest)
    {
        $result = $this->calculatorFactory->create('epc', $principal, $yearInterestRate, $year)->getResult();

        $totalPrincipal = $result->getTotalMoney();
        $totalInterest = $result->getTotalInterest();

        $this->assertEqualsWithDelta($expectedTotalPrincipal, $totalPrincipal, $this->delta);
        $this->assertEqualsWithDelta($expectedTotalInterest, $totalInterest, $this->delta);
    }

    public function epcDataProvider(): array
    {
        return [
            'p_1' => ['principal' => 120000, 'yearInterestRate' => 0.0486, 'year' => 10, 'expectedTotalPrincipal' => 149403, 'expectedTotalInterest' => 29403],
            'p_2' => ['principal' => 50000, 'yearInterestRate' => 0.036, 'year' => 2, 'expectedTotalPrincipal' => 51875, 'expectedTotalInterest' => 1875],
        ];
    }

    /**
     * 等额本息还款
     * @dataProvider etcDataProvider
     */
    public function test_etc($principal, $yearInterestRate, $year, $expectedTotalPrincipal, $expectedTotalInterest)
    {
        $result = $this->calculatorFactory->create('etc', $principal, $yearInterestRate, $year)->getResult();

        $totalPrincipal = $result->getTotalMoney();
        $totalInterest = $result->getTotalInterest();

        $this->assertEqualsWithDelta($expectedTotalPrincipal, $totalPrincipal, $this->delta);
        $this->assertEqualsWithDelta($expectedTotalInterest, $totalInterest, $this->delta);
    }

    public function etcDataProvider()
    {
        return [
            't_1' => ['principal' => 120000, 'yearInterestRate' => 0.0486, 'year' => 10, 'expectedTotalPrincipal' => 151750.8, 'expectedTotalInterest' => 31750.8],
            't_2' => ['principal' => 120000, 'yearInterestRate' => 0.0352, 'year' => 10, 'expectedTotalPrincipal' => 142530, 'expectedTotalInterest' => 22530],
            't_3' => ['principal' => 50000, 'yearInterestRate' => 0.036, 'year' => 2, 'expectedTotalPrincipal' => 51896.64, 'expectedTotalInterest' => 1896.64],
            't_4' => ['principal' => 80000, 'yearInterestRate' => 0.0889, 'year' => 3, 'expectedTotalPrincipal' => 91435.68, 'expectedTotalInterest' => 11435.68],
            't_5' => ['principal' => 80000, 'yearInterestRate' => 0.02993, 'year' => 3, 'expectedTotalPrincipal' => 83745, 'expectedTotalInterest' => 3745],
            't_6' => ['principal' => 120000, 'yearInterestRate' => 0.0352, 'year' => 10, 'expectedTotalPrincipal' => 142530, 'expectedTotalInterest' => 22530],
        ];
    }

}