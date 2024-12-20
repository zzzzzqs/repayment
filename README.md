# Repayment #

[![Run Tests](https://github.com/zzzzzqs/repayment/actions/workflows/main.yml/badge.svg)](https://github.com/zzzzzqs/repayment/actions/workflows/main.yml)

A laravel package for calculating equal principal interest and equal principal repayment.

### Installation

If you're using Composer to manage dependencies, you can use:

    composer require zzzzzqs/repayment


### Add service provider
Add the service provider to the providers array in the config/app.php config file as follows:

    'providers' => [

        ...

        \Zzzzzqs\Repayment\RepaymentServiceProvider::class,
    ]


### Publish the config
Run the following command to publish the package config file:

    php artisan vendor:publish --provider="Zzzzzqs\Repayment\RepaymentServiceProvider"

You should now have a config/repayment.php file that allows you to configure the basics of this package.


### Usage

    // epc means: matching the principal repayment 
    // etc means: average capital plus interest

    public function __construct(PaymentCalculatorFactory $calculatorFactory)
    {
        $this->calculatorFactory = $calculatorFactory;
    }

    public function calculate($type, $principal, $interestRate, $years)
    {
        $calculator = $this->calculatorFactory->create($type, $principal, $interestRate, $years);
        return $calculator->getResult();
    }

    // param like this:
    // $principal = 120000;
    // $yearInterestRate = "0.0486";
    // $year = 10;

    // the result is a object like RepaymentDTO;
    // if you want a array, you can $calculator->getSchedule return the new result like:
    [
        1 => [
            "period" => 1
            "principal" => "1000.00"
            "interest" => "486.00"
            "total_money" => "1486.00"
        ],
        2 => [
            "period" => 2
            "principal" => "1000.00"
            "interest" => "481.95"
            "total_money" => "1481.95"
        ],

        ……
    ]
