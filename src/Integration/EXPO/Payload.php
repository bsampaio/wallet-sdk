<?php


namespace Shots\Wallet\SDK\Integration\EXPO;


class Payload
{

    public $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function toArray(): array
    {
        return $this->fields;

    }
}