<?php

namespace Zzzzzqs\Repayment;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Zzzzzqs\Repayment\Factories\PaymentCalculatorFactory;

class RepaymentServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * 注册所有的应用服务
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PaymentCalculatorFactory::class, function () {
            return new PaymentCalculatorFactory();
        });
    }

    public function boot()
    {
        $path =__DIR__ . '/../config/repayment.php';
        $this->publishes([$path => config_path('repayment.php')], 'config');

        $this->mergeConfigFrom($path, 'repayment');
    }

    /**
     * 延迟加载
     * @return array
     */
    public function provides(): array
    {
        return [PaymentCalculatorFactory::class];
    }
}