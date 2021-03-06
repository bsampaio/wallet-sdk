<?php

namespace Shots\Wallet\SDK;

use Dotenv\Dotenv;
use GuzzleHttp\Exception\GuzzleException;
use Shots\Wallet\SDK\Exception\HeimdallKeyIsMissing;

class Client {

    const BASE_URI = 'https://wallet.shots.com.br/api';
    //const BASE_URI = 'http://localhost:8000/api';

    /**
     * @var Client
     */
    private static $instance;

    /**
     * @var mixed
     */
    private $debug = true;

    private $userAgent;

    /**
     * WalletService constructor.
     * @throws HeimdallKeyIsMissing
     */
    private function __construct($heimdall = null)
    {
        $dotEnvBase = __DIR__ . "/../";
        if(file_exists($dotEnvBase . '.env')) {
            $dotenv = Dotenv::createImmutable($dotEnvBase);
            $dotenv->load();
        }

        if($heimdall) {
            $heimdallKey = $heimdall;
        } else {
            $heimdallKey = env('HEIMDALL_KEY', null);
            if(!$heimdallKey) {
                throw new HeimdallKeyIsMissing();
            }
        }


        $this->debug = env('DEBUG', false);
        $this->setUserAgent();

        $base_uri = env('WALLET_SDK_BASE_URI', static::BASE_URI);

        $this->client = new \GuzzleHttp\Client([
            'verify'          => false,
            'base_uri'        => $base_uri,
            'allow_redirects' => false,
            'debug'           => $this->debug,
            'json'            => true,
            'headers'         => [
                'User-Agent'    => $this->userAgent,
                'Accept'        => 'application/json',
                'Heimdall-Key'  => $heimdallKey,
                'Content-Type'  => 'application/json'
            ]
        ]);
    }

    /**
     * @return Client
     * @throws HeimdallKeyIsMissing
     */
    public static function getInstance($heimdall = null): Client
    {
        if(!static::$instance) {
            static::$instance = new Client($heimdall);
        }

        return static::$instance;
    }


    /**
     * @param $uri
     * @param array $options
     * @throws GuzzleException
     */
    public function get($uri, array $options = [])
    {
        $uri = "/api" . $uri;
        $response = $this->client->get($uri, $options);

        if($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }

    public function post($uri, $options = [])
    {
        $uri = "/api" . $uri;
        $response = $this->client->post($uri, $options);
        if($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }

    private function setUserAgent()
    {
        $this->userAgent = 'shots-wallet-sdk';
        $appName = env('APP_NAME', null);
        if ($appName) {
            $appName = str_replace(' ', '-', $appName);
            $appName = strtolower($appName);
            $this->userAgent = $appName;
        }
    }
}