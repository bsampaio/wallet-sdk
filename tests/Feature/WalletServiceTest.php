<?php


use Shots\Wallet\SDK\Service\WalletService;
use PHPUnit\Framework\TestCase;

class WalletServiceTest extends TestCase
{
    const TEST_WALLET_USER = 'customer';
    const HEIMDALL_TEST_KEY = '3bb353bda7b61873c9ebd084c10f2e00718522c4';
    private $service;
    private $key;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new WalletService(self::HEIMDALL_TEST_KEY);
        $this->key = $this->service->getKey(self::TEST_WALLET_USER);
    }

    public function testGetKey()
    {
        $key = $this->service->getKey(self::TEST_WALLET_USER);
        $this->assertNotNull($key);
    }

    public function testGetInfo()
    {
        $info = $this->service->info($this->key);
        $this->assertNotNull($info);
    }

    public function testGetBalance()
    {
        $balance = $this->service->balance($this->key);
        $this->assertNotNull($balance);
    }

    public function testGetStatement()
    {
        $statement = $this->service->statement($this->key);
        $this->assertNotNull($statement);
    }
}
