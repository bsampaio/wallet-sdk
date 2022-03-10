<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 28/06/2021
 * Time: 17:02
 */

namespace Shots\Wallet\SDK\Integration\RD;


class Payload
{
    const TOKEN = "0eb70ce4d806faa1a1a23773e3d174d4";

    public $email;
    public $fields;

    public function __construct(array $fields)
    {
        $this->email = $fields['email'];
        $this->fields = $fields;
    }

    public function toArray(): array
    {
        return array_merge($this->fields, [
            'email' => $this->email,
            'token_rdstation' => self::TOKEN
        ]);
    }
}