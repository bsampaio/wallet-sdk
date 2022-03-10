<?php


use Shots\Wallet\SDK\Service\WalletService;
use PHPUnit\Framework\TestCase;

class FinanceServiceTest extends TestCase
{
    const TEST_WALLET_USER = 'staging';
    const HEIMDALL_TEST_KEY = '3bb353bda7b61873c9ebd084c10f2e00718522c4';
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new \Shots\Wallet\SDK\Service\FinanceService();
    }

    public function testCalculateInstallment()
    {
        $installments = 3;
        $original = 100;
        $adjusted = 108.42;
        $result = \Shots\Wallet\SDK\Service\FinanceService::calculateInstallment($original, $installments);

        $this->assertEquals($adjusted, $result);
    }
}
