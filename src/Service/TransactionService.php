<?php


namespace Lifepet\Wallet\SDK\Service;


use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Lifepet\Wallet\SDK\Client;
use Lifepet\Wallet\SDK\Exception\HeimdallKeyIsMissing;

class TransactionService extends BasicService
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

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param int $page
     * @param int $amountPerPage
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getAllByDate(Carbon $start, Carbon $end, int $page = 1, int $amountPerPage = 20)
    {
        return $this->client->get('/transactions', [
            'query' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'page' => $page,
                'amount' => $amountPerPage
            ]
        ]);
    }
}