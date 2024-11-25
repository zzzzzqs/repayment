<?php

namespace Zzzzzqs\Repayment;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Zzzzzqs\Repayment\Contracts\PaymentCalculatorInterface;

class RepaymentServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * 注册所有的应用服务
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PaymentCalculatorInterface::class, function ($app, $params) {
            return match ($params[0]) {
                'epc' => new EqualPrincipalPaymentCalculator($params[1], $params[2], $params[3]),
                'etc' => new EqualTotalPaymentCalculator($params[1], $params[2], $params[3]),
                default => '',
            };
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/repayment.php' => config_path('repayment.php'),
        ]);
    }

    /**
     * 延迟加载
     * @return array
     */
    public function provides()
    {
        return [PaymentCalculatorInterface::class];
    }
}