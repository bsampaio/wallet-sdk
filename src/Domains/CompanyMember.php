<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 07/07/2021
 * Time: 16:48
 */

namespace Lifepet\Wallet\SDK\Domains;


use Carbon\Carbon;

class CompanyMember extends Model
{
    protected $name;
    protected $document;
    protected $birthDate;

    /**
     * CompanyMember constructor.
     * @param $name
     * @param $document
     * @param $birthDate
     */
    public function __construct(string $name, string $document, Carbon $birthDate)
    {
        $this->name = $name;
        $this->document = $document;
        $this->birthDate = $birthDate;
    }

    public function getBirthDate()
    {
        return $this->birthDate->format('Y-m-d');
    }
}