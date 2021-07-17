<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 07/07/2021
 * Time: 16:34
 */

namespace Lifepet\Wallet\SDK\Domains;


use Carbon\Carbon;

class CompanyDigitalAccount extends DigitalAccount
{
    protected $companyType;
    protected $legalRepresentative;
    protected $cnae;
    protected $establishmentDate;
    protected $companyMembers;

    /**
     * CompanyDigitalAccount constructor.
     * @param string $type PF for personal and PJ for business
     * @param string $name
     * @param string $document Document number without dots. Ex.: 00000000000
     * @param string $email Valid email
     * @param string $phone Phone number without spaces or symbols
     * @param string $businessArea Loaded from API
     * @param string $linesOfBusiness Personal description of the business area
     * @param Address $address
     * @param BankAccount $bankAccount
     * @param float $monthlyIncomeOrRevenue Numeric amount of income
     * @param string $companyType One of COMPANY_TYPES constant option
     * @param LegalRepresentative $legalRepresentative
     * @param string $cnae
     * @param Carbon $establishmentDate
     * @param array $companyMembers
     * @param bool $pep Tells if the person is public exposed
     * @param bool $emailOptOut True if Juno is not meant to send emails
     * @param bool $autoTransfer
     */
    public function __construct(string $type, string $name, string $document, string $email, string $phone, string $businessArea, string $linesOfBusiness, Address $address, BankAccount $bankAccount, float $monthlyIncomeOrRevenue, string $companyType, LegalRepresentative $legalRepresentative, string $cnae, Carbon $establishmentDate, array $companyMembers = [], $pep = false, bool $emailOptOut = true, bool $autoTransfer = false)
    {
        parent::__construct($type, $name, $document, $email, $phone, $businessArea, $linesOfBusiness, $address, $bankAccount,  $monthlyIncomeOrRevenue, $pep, $emailOptOut, $autoTransfer);
        $this->companyType = $companyType;
        $this->legalRepresentative = $legalRepresentative;
        $this->cnae = $cnae;
        $this->establishmentDate = $establishmentDate;
        $this->companyMembers = $companyMembers;
    }

    /**
     * @return array
     */
    public function getLegalRepresentative(): array
    {
        return $this->legalRepresentative->toArray();
    }

    public function getEstablishmentDate()
    {
        return $this->establishmentDate->format('Y-m-d');
    }
}