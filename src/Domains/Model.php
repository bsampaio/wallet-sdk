<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 24/06/2021
 * Time: 16:35
 */

namespace Lifepet\Wallet\SDK\Domains;


abstract class Model
{
    public function toArray($hideNull = false): array
    {
        $reflection = new \ReflectionClass($this);
        $array = [];
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property) {
            $propertyName = $property->getName();
            $getterMethod = "get" . ucfirst($propertyName);
            $hasGetter = $reflection->hasMethod($getterMethod);

            $value = null;
            if($hasGetter) {
                $value = $this->$getterMethod();
            } else {
                $value = $this->$propertyName;
            }
            if(!$hideNull) {
                $array[$propertyName] = $value;
            }
        }

        return $array;
    }
}