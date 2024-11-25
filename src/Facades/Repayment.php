<?php

namespace Zzzzzqs\Repayment\Facades;

use Illuminate\Support\Facades\Facade;

class Repayment extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'repayment';
    }
}