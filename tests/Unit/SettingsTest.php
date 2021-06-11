<?php



use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    public function testCanAccessService()
    {
        $service = \Lifepet\Wallet\SDK\Client::getInstance();
        $this->assertNotNull($service->get('/'));
    }

}
