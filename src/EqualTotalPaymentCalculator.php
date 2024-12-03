<?php

namespace Zzzzzqs\Repayment;

use Zzzzzqs\Repayment\Abstracts\PaymentCalculatorAbstract;
use Zzzzzqs\Repayment\Contracts\PaymentCalculatorInterface;
use Zzzzzqs\Repayment\DTOs\RepaymentDTO;
use Zzzzzqs\Repayment\DTOs\ScheduleItemDTO;

/**
 * 等额本息还款
 * @note 计算公式：
 * @note 每月还款 = [ 本金 * 月利率 * ( 1 + 月利率 ) ^ 还款月数 ] / [ ( 1 + 月利率 ) ^ 还款月数 - 1 ]
 * @note 每月利息 = 剩余本金 * 月利率
 * @note 每月本金 = 每月还款 - 每月利息
 */
class EqualTotalPaymentCalculator extends PaymentCalculatorAbstract implements PaymentCalculatorInterface
{
    /**
     * 获取总利息
     * @param $monthlyPaymentMoney
     * @return string
     */
    public function getTotalInterest($monthlyPaymentMoney): string
    {
        // 总利息 = 还款月数 * 每月月供额 - 本金
        $digit = $this->decimalDigits + 5;
        return bcsub(
            bcmul( // 还款月数 * 每月月供额
                $monthlyPaymentMoney,
                $this->totalPeriod,
                $digit
            ),
            $this->principal, // 本金
            $digit
        );
    }

    /**
     * 每月还款
     * @param $principal
     * @param $period
     * @return string
     */
    public function monthlyTotal($principal, $period): string
    {
        // 每月还款 = [ 本金 * 月利率 * ( 1 + 月利率 ) ^ 还款月数 ] / [ ( 1 + 月利率 ) ^ 还款月数 - 1 ]
        $digit = $this->decimalDigits + 5;
        return bcdiv(
            bcmul(  // 本金 * 月利率 * ( 1 + 月利率 ) ^ 还款月数
                bcmul( // 本金 * 月利率
                    $principal,
                    $this->monthInterestRate,
                    $digit
                ),
                bcpow( // ( 1 + 月利率 ) ^ 还款月数
                    bcadd(
                        1 ,
                        $this->monthInterestRate,
                        $digit
                    ),
                    $period,
                    $digit
                ),
                $digit
            ),
            bcsub( // ( 1 + 月利率 ) ^ 还款月数 - 1
                bcpow(
                    bcadd(
                        1 ,
                        $this->monthInterestRate,
                        $digit
                    ),
                    $period,
                    $digit
                ),
                1,
                $digit
            ),
            $digit
        );
    }

    /**
     * 每月还款利息
     * @param $currentPrincipal
     * @return float
     */
    public function monthInterest($currentPrincipal): float
    {
        return round(bcmul($currentPrincipal, $this->monthInterestRate, 4), 2);
    }

    /**
     * 获取等额本息还款计划
     * @return RepaymentDTO
     */
    public function getResult(): RepaymentDTO
    {
        $paymentPlanLists = [];
        // 每月还款总和
        $monthlyPaymentMoney = round($this->monthlyTotal($this->principal, $this->totalPeriod), 2);
        // 总利息
        $totalInterest = round($this->getTotalInterest($monthlyPaymentMoney), 2);

        // 已还本金
        $repaidPrincipal = 0;
        // 已还利息
        $repaidInterest = 0;

        // 当前本金
        $currentPrincipal = $this->principal;

        // 期数
        $period = 0;

        for($i = 0; $i < $this->totalPeriod; $i ++) {
            $period ++;

            // 每月还款利息，直接按月利率算
            $monthlyInterest = $this->monthInterest($currentPrincipal);

            // 每月还款本金
            $monthlyPrincipal = bcsub($monthlyPaymentMoney, $monthlyInterest, 2);

            // 如果是最后一期
            if ($period == $this->totalPeriod) {
                $monthlyInterest = round(bcsub($totalInterest, $repaidInterest, 4), 2);
                $monthlyPrincipal = round(bcsub($this->principal, $repaidPrincipal, 4), 2);
            }

            // 已还本金
            $repaidPrincipal = bcadd($repaidPrincipal, $monthlyPrincipal, $this->decimalDigits);
            // 已还利息
            $repaidInterest = bcadd($repaidInterest, $monthlyInterest, $this->decimalDigits);

            // 剩余本金
            $currentPrincipal = max(bcsub($currentPrincipal, $monthlyPrincipal, $this->decimalDigits), 0);
            // 剩余还款利息
            $rowRemainInterest = max(bcsub($totalInterest, $repaidInterest, $this->decimalDigits), 0);

            // 返回每期还款计划
            $paymentPlanLists[$period] = $this->returnFormal($period, $monthlyPrincipal, $monthlyInterest, $monthlyPaymentMoney, $currentPrincipal, $rowRemainInterest);
            $paymentPlanLists[] = new ScheduleItemDTO(
                period: $period,
                principal: $monthlyPrincipal,
                interest: $monthlyInterest,
                payment: $monthlyPaymentMoney,
                remainingBalance: $currentPrincipal,
                remainingInterest: $rowRemainInterest
            );
        }

        return new RepaymentDTO(
            schedule: $paymentPlanLists
        );
    }
}