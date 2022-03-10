<?php


use Shots\Wallet\SDK\Service\WalletService;
use PHPUnit\Framework\TestCase;

class RDStationTest extends TestCase
{
    /**
     * @var \Shots\Wallet\SDK\Integration\RD\Service\SenderService $service
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new Shots\Wallet\SDK\Integration\RD\Service\SenderService('sdk__test_trigger', "breno.grillo@shots.com.br");
    }

    public function testSendTrigger()
    {
        $this->service->compose([
            'name' => 'Breno Grillo',
            'nickname' => 'brenshots'
        ]);

        $response = $this->service->send();
        $this->assertTrue($response);
    }
}
