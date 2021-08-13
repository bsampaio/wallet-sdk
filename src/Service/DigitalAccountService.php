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
use Lifepet\Wallet\SDK\Exception\AmountIsLowerThanMinimum;
use Lifepet\Wallet\SDK\Exception\HeimdallKeyIsMissing;

class DigitalAccountService extends BasicService
{
    const MINIMUM_WITHDRAW_AMOUNT = 1000;

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

    /**
     * Requires an withdraw of the total amount available and transferrable
     * in Juno Digital Account.
     *
     * Requires an configured endpoint to notify success.
     *
     * @param string $walletKey
     * @param int $amount
     * @return mixed|null
     * @throws AmountIsLowerThanMinimum
     */
    public function withdrawToDefaultBankAccount(string $walletKey, int $amount)
    {
        if($amount < self::MINIMUM_WITHDRAW_AMOUNT) {
            throw new AmountIsLowerThanMinimum();
        }

        return $this->client->post('/digital-accounts/withdraw', [
            'json' => [
                'amount' => $amount
            ],
            'headers' => [
                'Wallet-Key' => $walletKey
            ]
        ]);
    }

    /**
     * Retrieves the total amount available on Juno Account.
     * Should be considered for transfer requisitions.
     *
     * Requires an configured endpoint to notify success.
     *
     * @param string $walletKey
     * @return mixed|null
     */
    public function getAvailableWithdrawBalance(string $walletKey)
    {
        return $this->client->post('/digital-accounts/balance', [
            'headers' => [
                'Wallet-Key' => $walletKey
            ]
        ]);
    }

    /**
     * This method requires elevated privileges.
     *
     * @param string $transactionOrder
     * @return mixed|null
     */
    public function cashbackPaymentAuthorization(string $transactionOrder)
    {
        return $this->client->post('/digital-accounts/p2p-transfer', [
            'json' => [
                'order' => $transactionOrder
            ],
            'headers' => [
                'X-Authorization-Key' => env('WALLET_AUTHORIZATION_KEY')
            ]
        ]);
    }
}