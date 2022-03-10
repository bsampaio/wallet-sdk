<?php


namespace Shots\Wallet\SDK\Exception;


use Throwable;

class HeimdallKeyIsMissing extends \Exception
{
    public function __construct()
    {
        parent::__construct("A Heimdall Key can't be found and the request can't be done.", 0, null);
    }
}