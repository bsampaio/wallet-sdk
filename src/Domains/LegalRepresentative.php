<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 07/07/2021
 * Time: 16:57
 */

namespace Shots\Wallet\SDK\Domains;


class LegalRepresentative extends Model
{
    const TYPES = ["INDIVIDUAL", "ATTORNEY", "DESIGNEE", "MEMBER", "DIRECTOR", "PRESIDENT"];

    protected $name;
    protected $document;
    protected $birthDate;
    protected $motherName;
    protected $type;

    /**
     * LegalRepresentative constructor.
     * @param $name
     * @param $document
     * @param $birthDate
     * @param $motherName
     * @param $type
     */
    public function __construct($name, $document, $birthDate, $motherName, $type)
    {
        $this->name = $name;
        $this->document = $document;
        $this->birthDate = $birthDate;
        $this->motherName = $motherName;
        $this->type = $type;
    }

    public function getBirthDate()
    {
        return $this->birthDate->format('Y-m-d');
    }
}