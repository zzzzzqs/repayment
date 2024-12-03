<?php

namespace Zzzzzqs\Repayment\DTOs;

use JsonSerializable;

class RepaymentDTO implements JsonSerializable
{
    public function __construct(
        private array $schedule // 每期的还款计划
    ) {}

    public function getSchedule(): array
    {
        return $this->schedule;
    }

    public function getTotalMoney()
    {
        return collect($this->schedule)->sum('total_money');
    }

    public function getTotalInterest()
    {
        return collect($this->schedule)->sum('interest');
    }

    public function jsonSerialize(): array
    {
        return [
            'schedule' => array_map(fn($item) => $item->jsonSerialize(), $this->schedule)
        ];
    }
} 