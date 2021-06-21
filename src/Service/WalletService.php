<?php


namespace Lifepet\Wallet\SDK\Service;


use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Validation\ValidationException;
use Lifepet\Wallet\SDK\Client;
use Lifepet\Wallet\SDK\Exception\HeimdallKeyIsMissing;

class WalletService extends BasicService
{
    const ORIGIN__CUSTOMER = 'CUSTOMER';
    const ORIGIN__PARTNER = 'PARTNER';

    const TYPE__PERSONAL = 1;
    const TYPE__BUSINESS = 2;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string $origin
     */
    private $origin;

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
            'email'    => $email,
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

        if($this->origin === self::ORIGIN__PARTNER) {
            $params['type'] = self::TYPE__BUSINESS;
        }

        $validator = $this->validator->make($params, $rules);

        $validator->validate();

        return $this->client->post('/wallet', [
            'json' => $params
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
     * @param int|null $tax Indicates if the transfer has a custom tax
     * @param int|null $cashback Indicates if the transfer has a custom cashback
     * @return mixed|null
     * @throws ValidationException
     */
    public function transfer($key, $to, int $amount, string $reference = null, int $tax = null, int $cashback = null)
    {
        $params = [
            'transfer_to'     => $to,
            'amount' => $amount
        ];

        if($reference) {
            $params['reference'] = $reference;
        }
        if($tax) {
            $params['tax'] = $tax;
        }
        if($cashback) {
            $params['cashback'] = $cashback;
        }

        $validator = $this->validator->make($params, [
            'transfer_to' => 'required|string|regex:/^[A-Za-z.-]+$/|max:255',
            'amount' => 'required|numeric|integer',
            'reference' => 'sometimes|string',
            'tax' => 'sometimes|numeric|integer',
            'cashback' => 'sometimes|numeric|integer',
        ]);

        $validator->validate();

        return $this->client->post('/wallet/transfer', [
            'json' => $params,
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
     * @param string|null $base_url If informed, the given URL is appended with the reference info and generates the QRCode
     * @return mixed|null
     * @throws ValidationException
     */
    public function makeCharge(string $key, string $from, int $amount, string $base_url = null)
    {
        $params = [
            'from'    => $from,
            'amount'  => $amount
        ];

        if($base_url) {
            $params['base_url'] = $base_url;
        }

        $validator = $this->validator->make($params, [
            'from' => 'required|string|regex:/^[A-Za-z.-]+$/|max:255',
            'amount' => 'required|numeric|integer',
            'base_url' => 'sometimes|url'
        ]);

        $validator->validate();

        return $this->client->post('/wallet/charge', [
            'json' => $params,
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

    /**
     * @throws Exception
     */
    private function setOrigin($origin)
    {
        if(in_array($origin, [self::ORIGIN__CUSTOMER, self::ORIGIN__PARTNER])) {
            $this->origin = $origin;
        } else {
            throw new Exception("This origin is not allowed. Use one of the given origin.");
        }
    }


    /**
     * @param string $key
     * @param int $tax Integer number of the tax percentage
     * @return mixed|null
     * @throws ValidationException
     */
    public function setDefaultTax(string $key, int $tax)
    {
        $params = [
            'tax'    => $tax,
        ];

        $validator = $this->validator->make($params, [
            'tax' => 'required|numeric|integer|min:1',
        ]);

        $validator->validate();

        return $this->client->post('/wallet/tax', [
            'json' => $params,
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    /**
     * @param string $key
     * @param int $cashback Integer number of the cashback percentage
     * @return mixed|null
     * @throws ValidationException
     */
    public function setDefaultCashback(string $key, int $cashback)
    {
        $params = [
            'cashback' => $cashback,
        ];

        $validator = $this->validator->make($params, [
            'cashback' => 'required|numeric|integer|min:1',
        ]);

        $validator->validate();

        return $this->client->post('/wallet/cashback', [
            'json' => $params,
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }
}