<?php


namespace Lifepet\Wallet\SDK\Integration\EXPO;


class Payload
{
    
    public $sound;
    public $priority;

    public function __construct(array $fields)
    {
        $this->sound = 'default';
        $this->priority = 'high';
    }

    public function toArray(): array
    {
        return array_merge($this->fields, [
            'sound' => $this->sound,
            'priority' => $this->priority
        ]);
    }
}