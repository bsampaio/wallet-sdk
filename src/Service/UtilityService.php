<?php


namespace Lifepet\Wallet\SDK\Service;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Lifepet\Wallet\SDK\Client;
use Lifepet\Wallet\SDK\Exception\HeimdallKeyIsMissing;

class UtilityService
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

    /**
     * @throws ValidationException
     */
    public function qrcode($url)
    {
        $params = [
            'url' => $url,
        ];

        $validator = Validator::make($params, [
            'url' => 'required|url',
        ]);

        $validator->validate();

        return $this->client->post('/utility/qrcode', [
            'form_params' => $params
        ]);
    }
}