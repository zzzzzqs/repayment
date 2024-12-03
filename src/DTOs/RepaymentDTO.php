<?php

namespace Zzzzzqs\Repayment\DTOs;

use JsonSerializable;

class RepaymentDTO implements JsonSerializable
{
    public function __construct(
        private float $totalMoney,
        private float $totalInterest,
        private array $schedule // 每期的还款计划
    ) {}

    public function getSchedule(): array
    {
        return $this->schedule;
    }

    public function getTotalMoney(): float
    {
        return $this->totalMoney;
    }

    public function getTotalInterest(): float
    {
        return $this->totalInterest;
    }

    /**
     * 转换为数组
     */
    public function toArray(): array
    {
        return [
            'schedule' => array_map(fn($item) => $item->toArray(), $this->schedule)
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
} 