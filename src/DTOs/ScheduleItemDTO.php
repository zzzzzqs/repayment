<?php

namespace Zzzzzqs\Repayment\DTOs;

use JsonSerializable;

class ScheduleItemDTO implements JsonSerializable
{
    public function __construct(
        private int $period,             // 期数
        private float $principal,        // 每期还款本金
        private float $interest,         // 每期还款利息
        private float $payment,          // 每期还款总金额
        private float $remainingBalance, // 剩余本金
        private float $remainingInterest // 剩余利息
    ) {}

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function getPrincipal(): float
    {
        return $this->principal;
    }

    public function getInterest(): float
    {
        return $this->interest;
    }

    public function getPayment(): float
    {
        return $this->payment;
    }

    public function getRemainingBalance(): float
    {
        return $this->remainingBalance;
    }

    public function getRemainingInterest (): float
    {
        return $this->remainingInterest ;
    }

    /**
     * 转换为数组
     */
    public function toArray(): array
    {
        return [
            'period' => $this->period,
            'principal' => round($this->principal, 2),
            'interest' => round($this->interest, 2),
            'payment' => round($this->payment, 2),
            'remaining_balance' => round($this->remainingBalance, 2),
            'remaining_interest' => round($this->remainingInterest, 2)
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
} 