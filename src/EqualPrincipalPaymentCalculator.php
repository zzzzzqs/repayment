<?php

namespace Zzzzzqs\Repayment;

use Zzzzzqs\Repayment\Abstracts\PaymentCalculatorAbstract;
use Zzzzzqs\Repayment\Contracts\PaymentCalculatorInterface;

/**
 * 等额本金还款
 * @note 计算公式：
 * @note 每月还款 = ( 本金 / 还款月数 ) + ( 本金 - 累计已还本金 ) * 月利率
 * @note 每月本金 = 总本金 / 还款月数
 * @note 每月利息 = ( 本金 - 累计已还本金 ) * 月利率
 * @note 还款总利息 = ( 还款月数 + 1 ) * 本金 * 月利率 / 2
 * @note 还款总额 = ( 还款月数 + 1 ) * 本金 * 月利率 / 2 + 本金
 */
class EqualPrincipalPaymentCalculator extends PaymentCalculatorAbstract implements PaymentCalculatorInterface
{
    /**
     * 每月本金
     * @return string
     */
    public function monthlyPrincipal(): string
    {
        // 每月本金 = 总本金 / 还款月数
        return bcdiv($this->principal, $this->totalPeriod, $this->decimalDigits);
    }

    /**
     * 每月利息
     * @param $repaidPrincipal // 已还本金
     * @return string
     */
    public function monthlyInterest($repaidPrincipal): string
    {
        // 每月利息 = ( 本金 - 累计已还本金 ) * 月利率
        return bcmul(bcsub($this->principal, $repaidPrincipal, $this->decimalDigits), $this->monthInterestRate, $this->decimalDigits);
    }

    /**
     * 获取总利息
     * @return string
     */
    public function getTotalInterest(): string
    {
        // 还款总利息 = ( 还款月数 + 1 ) * 本金 * 月利率 / 2
        return
            bcdiv( // ( 还款月数 + 1 ) * 本金 * 月利率 / 2
                bcmul(
                    bcmul( // ( 还款月数 + 1 ) * 本金
                        $this->totalPeriod + 1,
                        $this->principal,
                        $this->decimalDigits
                    ),
                    $this->monthInterestRate,
                    $this->decimalDigits
                ),
                2,
                $this->decimalDigits
            );
    }

    /**
     * 等额本金还款计划
     * @return array
     */
    public function getResult(): array
    {
        $paymentPlanLists = [];
        $monthlyPaymentPrincipal = $this->monthlyPrincipal(); // 每月还款本金
        $totalInterest = $this->getTotalInterest(); // 总还款利息
        $repaidPrincipal = 0; // 已还本金
        $repaidInterest = 0; // 已还利息
        $period = 0; // 期数

        for($i = 0; $i < $this->totalPeriod; $i ++) {
            $period ++;

            // 每月还款利息
            $monthlyInterest = $this->monthlyInterest($repaidPrincipal);

            // 从新计算最后一期，还款本金和利息
            if ($period == $this->totalPeriod) {
                $monthlyPaymentPrincipal = bcsub($this->principal, $repaidPrincipal, $this->decimalDigits);
                $monthlyInterest = bcsub($totalInterest, $repaidInterest, $this->decimalDigits);
            }

            $repaidPrincipal = bcadd($repaidPrincipal, $monthlyPaymentPrincipal, $this->decimalDigits); // 已还本金
            $repaidInterest = bcadd($repaidInterest, $monthlyInterest, $this->decimalDigits); // 已支付利息
            $rowRemainPrincipal = bcsub($this->principal, $repaidPrincipal, $this->decimalDigits); // 剩余还款本金
            $rowRemainInterest = bcsub($totalInterest, $repaidInterest, $this->decimalDigits); // 剩余还款利息
            $rowTotalMoney = bcadd($monthlyPaymentPrincipal, $monthlyInterest, $this->decimalDigits); // 本期还款总额

            $paymentPlanLists[$period] = $this->returnFormal($period, $monthlyPaymentPrincipal, $monthlyInterest, $rowTotalMoney, $rowRemainPrincipal, $rowRemainInterest);
        }
        return $paymentPlanLists;
    }
}