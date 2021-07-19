<?php


namespace Lifepet\Wallet\SDK\Integration\EXPO\Service;


use Lifepet\Wallet\SDK\Integration\EXPO\Client;

class PushService
{

    private $client;
    private $data;

    /**
     * PushService constructor.
     * @param string $identifier
     * @param string $email
     * @throws \Exception
     */
    public function __construct(string $receiver_token, string $title, string $body)
    {

        $this->client = new Client();
        $this->data = [
            'to' => $receiver_token,
            'title' => $title,
            'body' => $body,
        ];
        $validator = $this->validator->make($this->data, [
            'to' => 'required',
            'title' => 'required',
            'body' => 'required'
        ]);
        $validator->validate();
    }

    public function compose(array $fields = [])
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