<?php


namespace Lifepet\Wallet\SDK\Service;


use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Validation\ValidationException;
use Lifepet\Wallet\SDK\Client;
use Lifepet\Wallet\SDK\Exception\HeimdallKeyIsMissing;
use Lifepet\Wallet\SDK\Domains\Billing;

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
    public function __construct($heimdall = null, $origin = 'CUSTOMER')
    {
        parent::__construct();
        $this->client = Client::getInstance($heimdall);
        $this->origin = $origin;
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

    public function userSearch(int $page = 1, string $term = null)
    {
        $params = [
            'page' => $page
        ];

        if($term) {
            $params['term'] = $term;
        }

        return $this->client->get('/users', [
            'json' => $params
        ]);
    }

    public function userByNickname(string $nickname)
    {
        $params = [
            'nickname' => $nickname
        ];

        return $this->client->get('/users/nickname', [
            'query' => $params
        ]);
    }

    /**
     * @param $name
     * @param $email
     * @param $nickname
     * @param null $password
     * @param int type
     * @return mixed|null
     * @throws ValidationException
     */
    public function makeUserEnablingWallet($name, $email, $nickname, $password = null, int $type = 1)
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

        if($this->origin === self::ORIGIN__PARTNER || $type === self::TYPE__BUSINESS) {
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
    public function makeCharge(string $key, int $amount, string $base_url = null, string $from = null, $overwritable = true, int $tax = null, int $cashback = null, $description = null, $customParams = [])
    {
        $params = [
            'amount'  => $amount,
            'overwritable' => $overwritable,
        ];

        if($base_url) {
            $params['base_url'] = $base_url;
        }
        if($tax) {
            $params['tax'] = $tax;
        }
        if($cashback) {
            $params['cashback'] = $cashback;
        }
        if($from) {
            $params['from'] = $from;
        }
        if($description) {
            $params['description'] = $description;
        }
        if($customParams) {
            $params['params'] = $customParams;
        }

        $validator = $this->validator->make($params, [
            'amount' => 'required|numeric|integer',
            'base_url' => 'sometimes|url',
            'from' => 'sometimes|string|regex:/^[A-Za-z.-]+$/|max:255',
            'overwritable' => 'sometimes|boolean',
            'cashback' => 'sometimes|integer',
            'tax' => 'sometimes|integer',
            'description' => 'sometimes|string',
            'params' => 'sometimes|array'
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
        return $this->client->get("/charge", [
            'query' => [
                'reference' => $reference,
            ],
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

    public function addCard(string $key, string $cardNumber, string $holderName, $securityCode, $expirationMonth, $expirationYear, string $cardNickname = null)
    {
        $params = [
            'card_number' => $cardNumber,
            'holder_name' => $holderName,
            'security_code' => $securityCode,
            'expiration_month' => $expirationMonth,
            'expiration_year' => $expirationYear
        ];
        if($cardNickname) {
            $params['card_nickname'] = $cardNickname;
        }

        $validator = $this->validator->make($params, [
            'card_number' => 'required',
            'holder_name' => 'required',
            'security_code' => 'required',
            'expiration_year' => 'required',
            'card_nickname' => 'sometimes|string',
        ]);

        $validator->validate();

        return $this->client->post('/wallet/cards/add', [
            'json' => $params,
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    public function getCards(string $key)
    {
        return $this->client->get('/wallet/cards', [
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    public function removeCard(string $key, int $cardId)
    {
        $params = [
            'card_id' => $cardId
        ];

        $validator = $this->validator->make($params, [
            'card_id' => 'required|numeric|integer',
        ]);

        $validator->validate();

        return $this->client->post('/wallet/cards/delete', [
            'json' => $params,
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    /**
     * @param string $key
     * @param int $cardId
     * @return mixed|null
     */
    public function enableCard(string $key, int $cardId)
    {
        $params = [
            'card_id' => $cardId
        ];

        $validator = $this->validator->make($params, [
            'card_id' => 'required|numeric|integer',
        ]);

        $validator->validate();

        return $this->client->post('/wallet/cards/activate', [
            'json' => $params,
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    /**
     * @param string $key
     * @param int $cardId
     * @return mixed|null
     */
    public function disableCard(string $key, int $cardId)
    {
        $params = [
            'card_id' => $cardId
        ];

        $validator = $this->validator->make($params, [
            'card_id' => 'required|numeric|integer',
        ]);

        $validator->validate();

        return $this->client->post('/wallet/cards/disable', [
            'json' => $params,
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    /**
     * @param string $key
     * @param int $cardId
     * @return mixed|null
     */
    public function setMainCard(string $key, int $cardId)
    {
        $params = [
            'card_id' => $cardId
        ];

        $validator = $this->validator->make($params, [
            'card_id' => 'required|numeric|integer',
        ]);

        $validator->validate();

        return $this->client->post('/wallet/cards/main', [
            'json' => $params,
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }

    /**
     * @param string $key
     * @param $to
     * @param int $cardId
     * @param bool $useBalance
     * @param int $amountToTransfer
     * @param int $amountFromCreditCard
     * @param int $installments
     * @param int $amountFromBalance
     * @param string $description
     * @param Billing $billing
     * @param string|null $reference
     * @param int|null $tax
     * @param int|null $cashback
     * @return mixed|null
     * @throws ValidationException
     */
    public function creditCardPayment(string $key, $to, int $cardId, bool $useBalance, int $amountToTransfer, int $amountFromCreditCard, int $installments, int $amountFromBalance,
                                      string $description, Billing $billing, string $reference = null, int $tax = null, int $cashback = null)
    {
        $params =  [
            //Receiver
            'transfer_to'     => $to,

            //Credit Card
            'use_credit_card' => 1,
            'card_id'         => $cardId,

            //Amount composition
            'amount_to_bill_credit_card' => $amountFromCreditCard,
            'amount_to_bill_balance'     => $amountFromBalance,
            'amount_to_transfer'         => $amountToTransfer,
            'installments'               => $installments,

            //Charge
            'description'        => $description,
            //'due_date'           => now(),

            //Address
            'street'       => $billing->getAddress()->getStreet(),
            'number'       => $billing->getAddress()->getNumber(),
            'neighborhood' => $billing->getAddress()->getNeighborhood(),
            'city'         => $billing->getAddress()->getCity(),
            'state'        => $billing->getAddress()->getState(),
            'post_code'    => $billing->getAddress()->getPostCode(),

            //Billing
            'name'       => $billing->getName(),
            'document'   => $billing->getDocument(),
            'email'      => $billing->getEmail(),
            'phone'      => $billing->getPhone(),
            'birth_date' => $billing->getBirthDate(),

            //Options
            'use_balance' => $useBalance,
        ];

        if($reference) {
            $params['reference'] =  $reference;
        }
        if($tax) {
            $params['tax'] =  $tax;
        }
        if($cashback) {
            $params['cashback'] =  $cashback;
        }

        $complement = $billing->getAddress()->getComplement();
        if($complement) {
            $params['complement'] = $complement;
        }

        $rules = [
            //Receiver
            'transfer_to'     => 'required|string',

            //Credit Card
            'use_credit_card' => 'required|boolean',
            'card_id'         => 'required|numeric',

            //Amount composition
            'amount_to_bill_credit_card' => 'required|numeric|integer|gte:1',
            'amount_to_bill_balance'     => 'sometimes|numeric|integer|gte:0',
            'amount_to_transfer'         => 'required|numeric|gte:1',
            'installments'               => 'required|numeric|max:24',

            //Charge
            'description'        => 'required|string',
            //'due_date'           => 'sometimes|date',

            //Address
            'street'       => 'required|string',
            'number'       => 'required|string',
            'neighborhood' => 'required|string',
            'city'         => 'required|string',
            'state'        => 'required|string',
            'post_code'    => 'required|string',
            'complement'   => 'sometimes|string',

            //Billing
            'name'       => 'required|string',
            'document'   => 'required|string',
            'email'      => 'required|string',
            'phone'      => 'required|string',
            'birth_date' => 'required|date',

            //Options
            'use_balance' => 'required|boolean',
            'reference'   => 'sometimes|string',
            'tax'         => 'sometimes|numeric|min:0',
            'cashback'    => 'sometimes|numeric|min:0'
        ];

        $validator = $this->validator->make($params, $rules);

        $validator->validate();

        return $this->client->post('/wallet/payment/credit-card', [
            'json' => $params,
            'headers' => [
                'Wallet-Key' => $key
            ]
        ]);
    }
}