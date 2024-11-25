<?php

namespace Zzzzzqs\Repayment\Abstracts;

use Zzzzzqs\Repayment\Exceptions\InvalidArgumentException;

abstract class PaymentCalculatorAbstract
{
    /**
     * 还款计划列表 本期还款期数键名
     * @var string
     */
    const PLAN_LISTS_KEY_PERIOD = 'period';
    /**
     * 还款计划列表 本期还款本金键名
     * @var string
     */
    const PLAN_LISTS_KEY_PRINCIPAL = 'principal';
    /**
     * 还款计划列表 本期还款利息键名
     * @var string
     */
    const PLAN_LISTS_KEY_INTEREST = 'interest';

    /**
     * 本期还款总金额
     * @var string
     */
    const PLAN_LISTS_KEY_TOTAL_MONEY = 'total_money';

    /**
     * 本期还款后 剩余还款本金
     * @var string
     */
    const PLAN_LISTS_KEY_REMAIN_PRINCIPAL = 'remain_principal';

    /**
     * 本期还款后 剩余还款利息
     * @var string
     */
    const PLAN_LISTS_KEY_REMAIN_INTEREST = 'remain_interest';

    // 本金
    protected float $principal;

    // 年利率
    protected float $yearInterestRate;

    // 月利率
    protected float $monthInterestRate;

    // 贷款年限
    protected int $years;

    // 总期数
    protected int $totalPeriod;

    // 保留小数点后几位
    protected int $decimalDigits;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(float $principal, float $yearInterestRate, int $years)
    {
        $this->check($principal, $yearInterestRate, $years);

        $this->decimalDigits = config('repayment.digit');
        $this->principal = $principal;
        $this->yearInterestRate = $yearInterestRate;
        $this->years = $years;

        $this->setPeriod();
        $this->setMonthInterestRate();
    }

    /**
     * @param $principal
     * @param $yearInterestRate
     * @param $years
     * @return void
     * @throws InvalidArgumentException
     */
    public function check($principal, $yearInterestRate, $years)
    {
        // 校验本金
        if ($principal <= 0) {
            throw new InvalidArgumentException("本金必须大于0");
        }

        // 校验年利率
        if ($yearInterestRate < 0) {
            throw new InvalidArgumentException("年利率不能为负数");
        }

        // 校验贷款年限
        if ($years <= 0) {
            throw new InvalidArgumentException("贷款年限必须大于0");
        }
    }

    /**
     * 设置总期数
     * @return void
     */
    public function setPeriod()
    {
        $this->totalPeriod = $this->years * 12;
    }

    /**
     * 设置月利率，月利率保存小数点后7位
     * @return void
     */
    public function setMonthInterestRate()
    {
        $this->monthInterestRate = bcdiv($this->yearInterestRate, 12, $this->decimalDigits + 5);
    }

    /**
     * @param $period
     * @param $monthlyPaymentPrincipal
     * @param $monthlyInterest
     * @param $rowTotalMoney
     * @param $rowRemainPrincipal
     * @param $rowRemainInterest
     * @return array
     */
    public function returnFormal($period, $monthlyPaymentPrincipal, $monthlyInterest, $rowTotalMoney, $rowRemainPrincipal, $rowRemainInterest): array
    {
        $arr = [];
        if (config('repayment.format.period')) {
            $arr[self::PLAN_LISTS_KEY_PERIOD] = $period;
        }

        if (config('repayment.format.principal')) {
            $arr[self::PLAN_LISTS_KEY_PRINCIPAL] = $monthlyPaymentPrincipal;
        }

        if (config('repayment.format.interest')) {
            $arr[self::PLAN_LISTS_KEY_INTEREST] = $monthlyInterest;
        }

        if (config('repayment.format.total_money')) {
            $arr[self::PLAN_LISTS_KEY_TOTAL_MONEY] = $rowTotalMoney;
        }

        if (config('repayment.format.remain_principal')) {
            $arr[self::PLAN_LISTS_KEY_REMAIN_PRINCIPAL] = $rowRemainPrincipal;
        }

        if (config('repayment.format.remain_interest')) {
            $arr[self::PLAN_LISTS_KEY_REMAIN_INTEREST] = $rowRemainInterest;
        }

        return $arr;
    }
}