<?php

namespace Zzzzzqs\Repayment\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Zzzzzqs\Repayment\RepaymentServiceProvider;

abstract class TestCase extends BaseTestCase
{
    // 注册服务提供者
    protected function getPackageProviders($app): array
    {
        return [
            RepaymentServiceProvider::class
        ];
    }

    // 注册包别名
    protected function getPackageAliases($app): array
    {
        return [
            // 'Repayment' => Zzzzzqs\Repayment\Facades\Repayment,
        ];
    }

    // 设置测试环境
    protected function getEnvironmentSetUp($app)
    {
        // 设置数据库连接
        // $app['config']->set('database.default', 'testing');
    }
}