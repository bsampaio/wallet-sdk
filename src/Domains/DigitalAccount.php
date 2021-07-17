<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 07/07/2021
 * Time: 16:29
 */

namespace Lifepet\Wallet\SDK\Domains;


abstract class DigitalAccount extends Model
{
    const COMPANY_TYPES = ["MEI", "EI", "EIRELI", "SA", "LTDA", "INSTITUITION_NGO_ASSOCIATION"];

    protected $type = "PAYMENT";
    protected $accountType = "PJ";
    protected $name, $document, $email, $phone;
    protected $businessArea, $linesOfBusiness;
    protected $address;
    protected $bankAccount;
    protected $monthlyIncomeOrRevenue;
    protected $pep;
    protected $emailOptOut = true;
    protected $autoTransfer = false;

    /**
     * DigitalAccount constructor.
     * @param string $type
     * @param $name
     * @param $document
     * @param $email
     * @param $phone
     * @param $businessArea
     * @param $linesOfBusiness
     * @param $address
     * @param BankAccount $bankAccount
     * @param $monthlyIncomeOrRevenue
     * @param $pep
     * @param bool $emailOptOut
     * @param bool $autoTransfer
     */
    public function __construct(string $type,string $accountType, string $name, string $document, string $email, string $phone, string $businessArea, string $linesOfBusiness, Address $address, BankAccount $bankAccount, float $monthlyIncomeOrRevenue, $pep = false, bool $emailOptOut = false, bool $autoTransfer = false)
    {
        $this->type = $type;
        $this->name = $name;
        $this->accountType = $accountType;
        $this->document = $document;
        $this->email = $email;
        $this->phone = $phone;
        $this->businessArea = $businessArea;
        $this->linesOfBusiness = $linesOfBusiness;
        $this->address = $address;
        $this->bankAccount = $bankAccount;
        $this->monthlyIncomeOrRevenue = $monthlyIncomeOrRevenue;
        $this->pep = $pep;
        $this->emailOptOut = $emailOptOut;
        $this->autoTransfer = $autoTransfer;
    }

    /**
     * @return array
     */
    public function getAddress(): array
    {
        return $this->address->toArray();
    }

    /**
     * @return array
     */
    public function getBankAccount(): array
    {
        return $this->bankAccount->toArray();
    }


}