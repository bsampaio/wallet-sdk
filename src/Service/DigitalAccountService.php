<?php


namespace Lifepet\Wallet\SDK\Service;


use Lifepet\Wallet\SDK\Client;
use Lifepet\Wallet\SDK\Exception\HeimdallKeyIsMissing;

class DigitalAccountService extends BasicService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * WalletService constructor.
     * @param null $heimdall
     * @throws HeimdallKeyIsMissing
     */
    public function __construct($heimdall = null)
    {
        parent::__construct();
        $this->client = Client::getInstance($heimdall);
    }
}