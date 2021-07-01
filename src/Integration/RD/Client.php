<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 28/06/2021
 * Time: 17:00
 */

namespace Lifepet\Wallet\SDK\Integration\RD;


class Client
{
    const URL = 'https://www.rdstation.com.br/api/1.2/conversions';
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