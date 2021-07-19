<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 19/07/2021
 * Time: 16:34
 */

namespace Lifepet\Wallet\SDK\Service;


class FinanceService
{
    const JUNO_INTERMEDIATION_TAX = 0.0459;
    const JUNO_ANTIFRAUD_TARIFF = 0.35;
    const JUNO_ANTICIPATION_TAX = 0.01;

    public static function calculateInstallment(float $original, int $installments = 0): float
    {
        if(!$installments) {
            return $original;
        }

        $a = self::JUNO_INTERMEDIATION_TAX;
        $b = self::JUNO_ANTIFRAUD_TARIFF;
        $c = self::JUNO_ANTICIPATION_TAX * $installments;
        $d = $original;

        $adjusted = (-$b * $c + $b + $d)/($a * $c - $a - $c + 1);
        return round($adjusted, 2, PHP_ROUND_HALF_UP);
    }
}