<?php


namespace Shots\Wallet\SDK\Exception;


class AmountIsLowerThanMinimum extends \Exception
{
    public function __construct()
    {
        parent::__construct("The given amount doen't reach the minimum value.", 0, null);
    }
}