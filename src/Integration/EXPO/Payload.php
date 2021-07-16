<?php


namespace Lifepet\Wallet\SDK\Integration\EXPO;


class Payload
{
    
    public $sound;
    public $priority;
    public $fields;

    public function __construct(array $fields)
    {
        $this->sound = 'default';
        $this->priority = 'high';
        $this->fields = $fields;
    }

    public function toArray(): array
    {
        return $this->fields;

    }
}