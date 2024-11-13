<?php

namespace MrProperter\Library;

use MrProperter\Helpers\ReadAttributesConfig;
use MrProperter\Models\MPModel;

class MrpValidateCommon
{


    public static function PropertyFillebleByTag(MPModel $mrpModel, $data, $tag = null)
    {
        $pros = $mrpModel->GetByTag($tag);

        foreach ($pros as $K => $prop) {



            if ($prop->listClassGeneric) {


                $classAttributes = ReadAttributesConfig::ReadClass($prop->listClassGeneric);

                $resultValueArray = [];

                $minVal = 1110;

                foreach ($classAttributes as $attr_Key => $attr_Data) {
                    $keyInReq = $prop->name . '__' . $attr_Key;
                    $minVal = min($minVal, count($data[$keyInReq]));
                }

                for ($i = 0; $i < $minVal; $i++) {
                    $resultValueArray[$i] = [];
                }

                foreach ($classAttributes as $attr_Key => $attr_Data) {
                    $keyInReq = $prop->name . '__' . $attr_Key;
                    if (empty($data[$keyInReq])) continue;

                    $vals = $data[$keyInReq];

                    for ($i = 0; $i < $minVal; $i++) {
                        if(isset(  $vals[$i])) {
                            $resultValueArray[$i][$attr_Key] = $vals[$i];
                        }
                    }
                }
                //$data[$K] = $resultValueArray;

                $mrpModel->$K =  $resultValueArray;
                continue;
            }

            if (!isset($data[$K])) {
                if ($prop->typeData == "checkbox") {
                    $data[$K] = false;
                } else {
                    continue;
                }
            }


            if ($prop->typeData == "checkbox") {
                $_val = false;
                if ($data[$K] == "on") $_val = true;
                $data[$K] = $_val;
            }

            if ($prop->typeData == "multioption") {

                $valueArray = [];
                foreach ($prop->GetOptions() as $key => $_label) {
                    if (in_array($key, $data[$K])) {

                        $valueArray[$key] = true;
                    }
                }

                $data[$K] = $valueArray;
            }

            $mrpModel->$K = $data[$K];
        }
    }

    public static function GetValidateRuleProperty(MPModel $mrpModel, $propertyName, $isRequired = true)
    {
        $props = $mrpModel->GetProperties();
        if (!isset($props[$propertyName])) {
            throw new \Exception("Не найден параметр " . $propertyName . " в моделе " . $cln);
        }
        $rules = [];

        $rules[$K] = self::RenderValidateRuleByPropertyData($props[$propertyName], $isRequired);
        return $rules[$K];
    }


    public static function ValidateListGenerics(MPModel $mrpModel, $validator, $requestArray, $tag = null)
    {

        foreach ($mrpModel->GetByTag($tag) as $K => $prop) {
            if (empty($prop->listClassGeneric)) continue;
            $msg = null;

            $classAttributes = ReadAttributesConfig::ReadClass($prop->listClassGeneric);


            $countLists = 0;

            foreach ($classAttributes as $attr_Key => $attr_Data) {
                $keyInReq = $prop->name . '__' . $attr_Key;
                if (empty($requestArray[$keyInReq])) continue;

                $vals = $requestArray[$keyInReq];
                $countLists = count($vals);



                if (isset($attr_Data['Options'])) {
                    foreach ($vals as $valueVariant) {
                        if (!isset($attr_Data['Options'][$valueVariant])) {

                            $msg = "В поле " . $attr_Data['Name'] . ' указан не верный тип';
                            break;
                            break;
                        }
                    }
                }

                if (isset($attr_Data['Type'])) {
                    if (isset($attr_Data['Lengh']) and $attr_Data['Type'] == "string") {
                        foreach ($vals as $valueVariant) {
                            $len = strlen($valueVariant);
                            if ($len < $attr_Data['Lengh'][0] or $len > $attr_Data['Lengh'][1]) {
                                $msg = "Поле " . $attr_Data['Name'] . ' должно иметь длинну от ' . $attr_Data['Lengh'][0] . ' до ' . $attr_Data['Lengh'][1];
                                break;
                                break;
                            }
                        }
                    }

                    if (isset($attr_Data['Lengh']) and $attr_Data['Type'] == "int") {
                        foreach ($vals as $valueVariant) {
                            $len = intval($valueVariant);
                            if ($len < $attr_Data['Lengh'][0] or $len > $attr_Data['Lengh'][1]) {
                                $msg = "Поле " . $attr_Data['Name'] . ' должно иметь длинну от ' . $attr_Data['Lengh'][0] . ' до ' . $attr_Data['Lengh'][1];
                                break;
                                break;
                            }
                        }
                    }

                    if (isset($attr_Data['Lengh']) and $attr_Data['Type'] == "float") {
                        foreach ($vals as $valueVariant) {
                            $len = floatval($valueVariant);
                            if ($len < $attr_Data['Lengh'][0] or $len > $attr_Data['Lengh'][1]) {
                                $msg = "Поле " . $attr_Data['Name'] . ' должно иметь длинну от ' . $attr_Data['Lengh'][0] . ' до ' . $attr_Data['Lengh'][1];
                                break;
                                break;
                            }
                        }
                    }

                }
            }


            if ($countLists == 0) {
                $msg = "В списке нет данных";
            }
            // $msg = "countLists: $countLists";

            if ($msg) {
                $validator->after(function ($validator) use ($K, $prop, $msg) {
                    $validator->errors()->add($K, $prop->label . ': ' . $msg . ' ');
                });
            }
        }

    }
}
