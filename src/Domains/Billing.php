<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 24/06/2021
 * Time: 16:22
 */

namespace Lifepet\Wallet\SDK\Domains;


use Carbon\Carbon;

class Billing extends Model
{
    protected $name;
    protected $document;
    protected $email;
    protected $phone;
    protected $birthDate;
    protected $address;

    public function __construct($name, $document, $email, $phone, Carbon $birthDate, Address $address)
    {
        $this->name = $name;
        $this->document = $document;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->setBirthDate($birthDate);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param mixed $document
     */
    public function setDocument($document): void
    {
        $this->document = $document;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param mixed $birthDate
     */
    public function setBirthDate(Carbon $birthDate): void
    {
        $this->birthDate = $birthDate->format(self::DATE_FORMAT);
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function transformForPayment($delayed = false): array
    {
        return [
            'email' => $this->getEmail(),
            'address' => $this->address->toArray(),
            'delayed' => $delayed,
        ];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'document' => $this->document,
            'email' => $this->email,
            'phone' => $this->phone,
            'birthDate' => $this->birthDate,
            'address' => $this->address->toArray(),
        ];
    }
}