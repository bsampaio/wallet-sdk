<?php


use Lifepet\Wallet\SDK\Service\WalletService;
use PHPUnit\Framework\TestCase;

class UtilityServiceTest extends TestCase
{
    const TEST_WALLET_USER = 'staging';
    const HEIMDALL_TEST_KEY = '3bb353bda7b61873c9ebd084c10f2e00718522c4';
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new \Lifepet\Wallet\SDK\Service\UtilityService(self::HEIMDALL_TEST_KEY);
    }

    public function testGenerateQrCodeFromURL()
    {
        $url = "https://www.lifepet.com.br/";

        $response = $this->service->qrcode($url);
        $this->assertNotNull($response);
        $this->assertObjectHasAttribute('image', $response);
    }
}
