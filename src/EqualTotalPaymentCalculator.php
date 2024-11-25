<?php

namespace Zzzzzqs\Repayment;

use Zzzzzqs\Repayment\Abstracts\PaymentCalculatorAbstract;
use Zzzzzqs\Repayment\Contracts\PaymentCalculatorInterface;

/**
 * 等额本息还款
 * @note 计算公式：
 * @note 每月还款 = [ 本金 * 月利率 * ( 1 + 月利率 ) ^ 还款月数 ] / [ ( 1 + 月利率 ) ^ 还款月数 - 1 ]
 * @note 每月利息 = 本金 * 月利率 * [ ( 1 + 月利率 ) ^ 还款月数 - ( 1 + 月利率 ) ^ ( 还款月序号 - 1 ) ] / [ ( 1 + 月利率 ) ^ 还款月数 - 1 ]
 * @note 每月本金 = 本金 * 月利率 * ( 1 + 月利率 ) ^ ( 还款月序号 - 1 ) / [ ( 1 + 月利率 ) ^ 还款月数 - 1 ]
 * @note 还款总利息 = 还款月数 * 每月还款 - 本金
 */
class EqualTotalPaymentCalculator extends PaymentCalculatorAbstract implements PaymentCalculatorInterface
{

    /**
     * 每月本金
     * @param $period
     * @return string
     */
    public function monthlyPrincipal($period): string
    {
        // 每月本金 = 本金 * 月利率 * ( 1 + 月利率 ) ^ ( 还款月序号 - 1 ) / [ ( 1 + 月利率 ) ^ 还款月数 - 1 ]
        return bcdiv(
            bcmul(
                bcmul( // 本金 * 月利率
                    $this->principal,
                    $this->monthInterestRate,
                    $this->decimalDigits
                ),
                bcpow( // ( 1 + 月利率 ) ^ ( 还款月序号 - 1 )
                    1 + $this->monthInterestRate,
                    $period -1,
                    $this->decimalDigits
                ),
                $this->decimalDigits // 保留小数点后位数
            ),
            bcsub(
                bcpow( // ( 1 + 月利率 ) ^ 还款月数
                    1 + $this->monthInterestRate,
                    $this->totalPeriod,
                    $this->decimalDigits
                ),
                1, // -1
                $this->decimalDigits
            ),
            $this->decimalDigits
        );
    }

    /**
     * 获取总利息
     * @return string
     */
    public function getTotalInterest(): string
    {
        // 总利息 = 还款月数 * 每月月供额 - 本金
        return bcsub(
            bcmul( // 还款月数 * 每月月供额
                $this->monthlyTotal($this->principal, $this->totalPeriod),
                $this->totalPeriod,
                $this->decimalDigits
            ),
            $this->principal, // 本金
            $this->decimalDigits
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
        return bcdiv(
            bcmul(  // 本金 * 月利率 * ( 1 + 月利率 ) ^ 还款月数
                bcmul( // 本金 * 月利率
                    $principal,
                    $this->monthInterestRate,
                    $this->decimalDigits
                ),
                bcpow(1 + $this->monthInterestRate, $period, $this->decimalDigits), // ( 1 + 月利率 ) ^ 还款月数
                $this->decimalDigits
            ),
            bcsub( // ( 1 + 月利率 ) ^ 还款月数 - 1
                bcpow(1 + $this->monthInterestRate, $period, $this->decimalDigits),
                1,
                $this->decimalDigits
            ),
            $this->decimalDigits
        );
    }

    public function getResult(): array
    {
        $paymentPlanLists = [];
        // 每月总还款
        $monthlyPaymentMoney = $this->monthlyTotal($this->principal, $this->totalPeriod);
        // 总利息
        $totalInterest = $this->getTotalInterest();

        // 已还本金
        $repaidPrincipal = 0;
        // 已还利息
        $repaidInterest = 0;
        // 期数
        $period = 0;

        for($i = 0; $i < $this->totalPeriod; $i ++) {
            $period ++;
            // 每月还款本金
            $monthlyPrincipal = $this->monthlyPrincipal($period);
            // 每月还款利息
            $monthlyInterest = bcsub($monthlyPaymentMoney, $monthlyPrincipal, $this->decimalDigits);
            // 从新计算最后一期，还款本金和利息
            if ($period == $this->totalPeriod) {
                $monthlyPrincipal = bcsub($this->principal, $repaidPrincipal, $this->decimalDigits);
                $monthlyInterest = bcsub($totalInterest, $repaidInterest, $this->decimalDigits);
            }
            // 利息小于0，设置未0
            $monthlyInterest = $monthlyInterest < 0 ? "0.00" : $monthlyInterest;

            $repaidInterest = bcadd($repaidInterest, $monthlyInterest, $this->decimalDigits);
            $repaidPrincipal = bcadd($repaidPrincipal, $monthlyPrincipal, $this->decimalDigits);
            // 剩余还款本金
            $rowRemainPrincipal = bcsub($this->principal, $repaidPrincipal, $this->decimalDigits);
            // 剩余还款利息
            $rowRemainInterest = bcsub($totalInterest, $repaidInterest, $this->decimalDigits);
            // 剩余本利息小于0，设置未0
            $rowRemainInterest = $rowRemainInterest < 0 ? "0.00" : $rowRemainInterest;

            $rowTotalMoney = bcadd($monthlyPrincipal, $monthlyInterest, $this->decimalDigits);

            // 返回每期还款计划
            $paymentPlanLists[$period] = $this->returnFormal($period, $monthlyPrincipal, $monthlyInterest, $rowTotalMoney, $rowRemainPrincipal, $rowRemainInterest);
        }
        return $paymentPlanLists;
    }
}