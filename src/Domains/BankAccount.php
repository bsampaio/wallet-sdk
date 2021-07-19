<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 07/07/2021
 * Time: 16:39
 */

namespace Lifepet\Wallet\SDK\Domains;


class BankAccount extends Model
{
    const COMPLEMENT_NUMBERS = ["001", "002", "003", "006", "007", "013", "022", "023", "028", "043", "031"];
    const ACCOUNT_TYPES = ["CHECKINGS", "SAVINGS"];

    protected $bankNumber;
    protected $agencyNumber;
    protected $accountNumber;
    protected $accountComplementNumber;
    protected $accountHolder;
    protected $accountType;

    /**
     * BankAccount constructor.
     * @param $bankNumber
     * @param $agencyNumber
     * @param $accountNumber
     * @param string $accountComplementNumber Only for Caixa accounts
     * @param AccountHolder $accountHolder
     */
    public function __construct(string $bankNumber, string $agencyNumber, string $accountNumber, string $accountType, AccountHolder $accountHolder, string $accountComplementNumber = null)
    {
        $this->bankNumber = $bankNumber;
        $this->agencyNumber = $agencyNumber;
        $this->accountNumber = $accountNumber;
        $this->accountComplementNumber = $accountComplementNumber;
        $this->accountHolder = $accountHolder;
        $this->accountType = $accountType;
    }

    /**
     * @return string
     */
    public function getBankNumber(): string
    {
        return $this->bankNumber;
    }

    /**
     * @return string
     */
    public function getAgencyNumber(): string
    {
        return $this->agencyNumber;
    }

    /**
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @return string
     */
    public function getAccountComplementNumber(): ?string
    {
        return $this->accountComplementNumber;
    }

    /**
     * @return array
     */
    public function getAccountHolder(): array
    {
        return $this->accountHolder->toArray();
    }
}