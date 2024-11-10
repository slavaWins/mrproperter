<?php

namespace MrProperter\Helpers;

use ReflectionClass;

class ReadAttributesConfig
{


    public static function ReadClass($classExamle)
    {

        $class = new ReflectionClass( ($classExamle));
        $result = [];

        foreach ($class->getProperties() as $property) {
            $propertyName = $property->getName();
            $attributesData = [];

            // Получение атрибутов для каждого свойства
            foreach ($property->getAttributes() as $attribute) {

                $name = $attribute->getName();
                $name = basenames($name);
                $val = $attribute->getArguments()[0];
                if(count($attribute->getArguments())>1){
                    $val = $attribute->getArguments();
                }
                $attributesData[$name] = $val;
            }

            $result[$propertyName] = $attributesData;
        }
        return $result;
    }
}
