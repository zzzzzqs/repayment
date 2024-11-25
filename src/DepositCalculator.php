<?php

namespace Zzzzzqs\Repayment;

class DepositCalculator
{
    // 保留小数点后几位
    protected int $decimalDigits = 2;

    /**
     * 本息合计
     * @param float $principal
     * @param float $yearInterestRate
     * @param int $years
     * @return string
     */
    public function getSumOfPrincipalAndInterest(float $principal, float $yearInterestRate, int $years): string
    {
        // 本息 = 本金 x [ 1 + ( 年利率 * 存款年限) ]
        return bcmul(
            $principal, // 本金
            bcadd(
                bcmul( // 年利率 * 存款年限
                    $yearInterestRate,
                    $years,
                    $this->decimalDigits
                ),
                1
            )
        );
    }
}