<?php

namespace Zzzzzqs\Repayment\Factories;

use Zzzzzqs\Repayment\Contracts\PaymentCalculatorInterface;
use Zzzzzqs\Repayment\EqualPrincipalPaymentCalculator;
use Zzzzzqs\Repayment\EqualTotalPaymentCalculator;
use Zzzzzqs\Repayment\Exceptions\InvalidArgumentException;

class PaymentCalculatorFactory
{
    /**
     * @param $type
     * @param $principal
     * @param $yearInterestRate
     * @param $year
     * @return PaymentCalculatorInterface
     * @throws InvalidArgumentException
     */
    public function create($type, $principal, $yearInterestRate, $year): PaymentCalculatorInterface
    {
        return match ($type) {
            'epc' => new EqualPrincipalPaymentCalculator($principal, $yearInterestRate, $year),
            'etc' => new EqualTotalPaymentCalculator($principal, $yearInterestRate, $year),
            default => throw new \InvalidArgumentException('Invalid calculator type'),
        };
    }
}