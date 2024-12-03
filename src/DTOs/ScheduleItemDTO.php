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

    public function jsonSerialize(): array
    {
        return [
            'period' => $this->period,
            'principal' => number_format($this->principal, 2),
            'interest' => number_format($this->interest, 2),
            'payment' => number_format($this->payment, 2),
            'remaining_balance' => number_format($this->remainingBalance, 2)
        ];
    }
} 