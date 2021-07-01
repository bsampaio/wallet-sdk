<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 24/06/2021
 * Time: 16:22
 */

namespace Lifepet\Wallet\SDK\Domains;


class Address extends Model
{
    protected $street;
    protected $number;
    protected $complement;
    protected $neighborhood;
    protected $city;
    protected $state;
    protected $postCode;

    /**
     * Address constructor.
     * @param $street
     * @param $number
     * @param $complement
     * @param $neighborhood
     * @param $city
     * @param $state
     * @param $postCode
     */
    public function __construct($street, $number, $neighborhood, $city, $state, $postCode, $complement = null)
    {
        $this->street = $street;
        $this->number = $number;
        $this->neighborhood = $neighborhood;
        $this->city = $city;
        $this->state = strtoupper($state);
        $this->postCode = $postCode;
        $this->complement = $complement;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street): void
    {
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    /**
     * @return mixed|null
     */
    public function getComplement()
    {
        return $this->complement;
    }

    /**
     * @param mixed|null $complement
     */
    public function setComplement($complement): void
    {
        $this->complement = $complement;
    }

    /**
     * @return mixed
     */
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    /**
     * @param mixed $neighborhood
     */
    public function setNeighborhood($neighborhood): void
    {
        $this->neighborhood = $neighborhood;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param mixed $postCode
     */
    public function setPostCode($postCode): void
    {
        $this->postCode = $postCode;
    }
}