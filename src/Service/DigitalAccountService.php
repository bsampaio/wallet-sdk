<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 16/07/2021
 * Time: 00:21
 */

namespace Lifepet\Wallet\SDK\Service;


use Lifepet\Wallet\SDK\Client;
use Lifepet\Wallet\SDK\Domains\CompanyDigitalAccount;
use Lifepet\Wallet\SDK\Domains\DigitalAccount;
use Lifepet\Wallet\SDK\Exception\HeimdallKeyIsMissing;

class DigitalAccountService extends BasicService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * WalletService constructor.
     * @param $heimdall
     * @throws HeimdallKeyIsMissing
     */
    public function __construct($heimdall = null)
    {
        parent::__construct();
        $this->client = Client::getInstance($heimdall);
    }

    public function openBusinessAccount($walletKey, CompanyDigitalAccount $digitalAccount)
    {
        return $this->client->post('/digital-accounts', [
            'json' => $digitalAccount->toArray(),
            'headers' => [
                'Wallet-Key' => $walletKey
            ]
        ]);
    }

    public function documents(string $walletKey)
    {
        return $this->client->get('/digital-accounts/documents', [
            'headers' => [
                'Wallet-Key' => $walletKey
            ]
        ]);
    }

    public function getDocumentsUploadLink(string $walletKey, string $returnUrl, string $refreshUrl)
    {
        return $this->client->get('/digital-accounts/documents-link', [
            'json' => [
                'return_url' => $returnUrl,
                'refresh_url' => $refreshUrl
            ],
            'headers' => [
                'Wallet-Key' => $walletKey
            ]
        ]);
    }

    public function getBusinessAreas(string $walletKey)
    {
        return $this->client->get('/digital-accounts/business-areas', [
            'headers' => [
                'Wallet-Key' => $walletKey
            ]
        ]);
    }

    public function getBanks(string $walletKey)
    {
        return $this->client->get('/digital-accounts/banks', [
            'headers' => [
                'Wallet-Key' => $walletKey
            ]
        ]);
    }

    public function getCompanyTypes(string $walletKey)
    {
        return $this->client->get('/digital-accounts/company-types', [
            'headers' => [
                'Wallet-Key' => $walletKey
            ]
        ]);
    }
}