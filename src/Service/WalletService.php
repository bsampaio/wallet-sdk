<?php


namespace Lifepet\Wallet\SDK\Service;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Lifepet\Wallet\SDK\Client;
use Lifepet\Wallet\SDK\Exception\HeimdallKeyIsMissing;

class WalletService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * WalletService constructor.
     * @param $walletKey
     * @throws HeimdallKeyIsMissing
     */
    public function __construct()
    {
        $this->client = Client::getInstance();
    }

    public function checkNickname($nickname)
    {
        return $this->client->get('/nickname', [
            'query' => [
                'nickname' => $nickname
            ]
        ]);
    }

    public function activeUsers()
    {
        return $this->client->get('/users/available', []);
    }

    /**
     * @param $name
     * @param $email
     * @param $nickname
     * @param null $password
     * @return mixed|null
     * @throws ValidationException
     */
    public function makeUserEnablingWallet($name, $email, $nickname, $password = null)
    {
        $params = [
            'name'     => $name,
            'nickname' => $nickname,
            'email'    => $email
        ];
        if($password) {
            $params['password'] = $password;
            $params['password_confirmation'] = $password;
            $params['automatic_password'] = false;
        }

        $validator = Validator::make($params, [
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|regex:/^[A-Za-z.-]+$/|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validator->validate();

        return $this->client->post('/wallet', [
            'form_params' => $params
        ]);
    }

    /**
     * @param $key
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function info($key)
    {
        return $this->client->get('/wallet/info', [
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    public function getKey($nickname)
    {
        $data = $this->client->get("/wallet/{$nickname}/key");
        if($data && $data->wallet_key) {
            return $data->wallet_key;
        }

        return null;
    }

    public function balance($key)
    {
        return $this->client->get('/wallet/balance', [
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    public function statement($key)
    {
        return $this->client->get('/wallet/statement', [
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    /**
     * @param $key
     * @param $to
     * @param int $amount
     * @return mixed|null
     * @throws ValidationException
     */
    public function transfer($key, $to, int $amount)
    {
        $params = [
            'to' => $to,
            'amount'    => $amount
        ];

        $validator = Validator::make($params, [
            'to' => 'required|string|regex:/^[A-Za-z.-]+$/|max:255',
            'amount' => 'required|numeric|integer',
        ]);

        $validator->validate();

        return $this->client->post('/wallet', [
            'form_params' => $params,
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }
}