<?php


namespace Lifepet\Wallet\SDK\Service;


use GuzzleHttp\Exception\GuzzleException;
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
    public function __construct($heimdall = null)
    {
        $this->client = Client::getInstance($heimdall);
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
        $rules = [
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|regex:/^[A-Za-z.-]+$/|max:255',
            'email' => 'required|string|email|max:255',
        ];

        if($password) {
            $params['password'] = $password;
            $params['password_confirmation'] = $password;
            $params['automatic_password'] = false;

            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $validator = Validator::make($params, $rules);

        $validator->validate();

        return $this->client->post('/wallet', [
            'form_params' => $params
        ]);
    }

    /**
     * @param $key
     * @return mixed|null
     * @throws GuzzleException
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
     * @param string|null $reference Indicates if the transfer pays a charge.
     * @return mixed|null
     * @throws ValidationException
     */
    public function transfer($key, $to, int $amount, string $reference = null)
    {
        $params = [
            'to'     => $to,
            'amount' => $amount
        ];
        if($reference) {
            $params['reference'] = $reference;
        }

        $validator = Validator::make($params, [
            'to' => 'required|string|regex:/^[A-Za-z.-]+$/|max:255',
            'amount' => 'required|numeric|integer',
            'reference' => 'sometimes|string'
        ]);

        $validator->validate();

        return $this->client->post('/wallet', [
            'form_params' => $params,
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    /**
     * Creates a base64 PNG image with the QRCode of the charge.
     *
     * @param string $key
     * @param string $from User to charge from
     * @param int $amount Amount to charge in cents
     * @return mixed|null
     * @throws ValidationException
     */
    public function makeCharge(string $key, string $from, int $amount)
    {
        $params = [
            'from' => $from,
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

    /**
     * @param string $key
     * @param string $reference Reference of charge. Code in the following format xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
     * @return mixed|null
     * @throws GuzzleException
     */
    public function chargeInfo(string $key, string $reference)
    {
        return $this->client->get("/charge/{$reference}", [
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }
}