<?php



use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    public function testCanAccessService()
    {
        $service = \Shots\Wallet\SDK\Client::getInstance();
        $this->assertNotNull($service->get('/'));
    }

}
