<?php


namespace Lifepet\Wallet\SDK\Integration\EXPO;


class Client
{
    const URL = 'https://exp.host/--/api/v2/push/send';
    private $requester;

    /**
     * Client constructor.
     * @throws \Exception
     */
    public function __construct() {
        $this->requester = new Requester(self::URL);
    }

    /**
     * @param array $fields
     * @throws \Exception
     * @return bool
     */
    public function request(array $fields) {
        try {
            $payload = new Payload($fields);
            return $this->requester->request($payload);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}