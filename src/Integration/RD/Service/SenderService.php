<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 28/06/2021
 * Time: 17:11
 */

namespace Shots\Wallet\SDK\Integration\RD\Service;


use Shots\Wallet\SDK\Integration\RD\Client;

class SenderService
{
    private $identifier;
    private $receiver;
    private $client;
    private $data;

    /**
     * SenderService constructor.
     * @param string $identifier
     * @param string $email
     * @throws \Exception
     */
    public function __construct(string $identifier, string $email)
    {
        $this->identifier = $identifier;
        $this->receiver = $email;
        $this->client = new Client();
        $this->data = [
            'identificador' => $identifier,
            'email' => $email,
            'identifier' => $identifier,
        ];
    }

    public function compose(array $fields)
    {
        $this->data = array_merge($this->data, $fields);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function send()
    {
        return $this->client->request($this->data);
    }
}