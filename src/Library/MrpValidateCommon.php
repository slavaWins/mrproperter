<?php

namespace MrProperter\Library;

use Mews\Purifier\Facades\Purifier;
use MrProperter\Helpers\ReadAttributesConfig;
use MrProperter\Models\MPModel;

class MrpValidateCommon
{

    /** Максимальная длина строки в колонке ListClassGeneric, если у атрибута не задан Lengh */
    public const LIST_GENERIC_STRING_MAX = 2000;

    /**
     * Чистит значение перед записью в модель.
     * Строку без угловых скобок не трогаем: HTML в ней быть не может, а Purifier
     * превращал бы «Tom & Jerry» в «Tom &amp; Jerry». Строки с < или > идут через Purifier как раньше.
     */
    public static function CleanValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) $value[$k] = self::CleanValue($v);
            return $value;
        }
        if (!is_string($value) || $value === '') return $value;
        if (strpbrk($value, '<>') === false) return $value;

        return Purifier::clean($value, ['AutoFormat.AutoParagraph' => false]);
    }


    /**
     * Записывает в модель поля указанного тега. ВАЖНО: данные не валидируются —
     * используйте MPModel::ValidateAndFilibleByRequest(), если данные пришли от пользователя.
     * Поля NonEditable() пропускаются всегда.
     */
    public static function PropertyFillebleByTag(MPModel $mrpModel, $data, $tag = null)
    {
        $pros = $mrpModel->GetByTag($tag);
        $data = is_array($data) ? $data : [];


        foreach ($pros as $K => $prop) {

            if ($prop->is_nonEditable) continue;


            if ($prop->listClassGeneric) {


                $classAttributes = ReadAttributesConfig::ReadClass($prop->listClassGeneric);

                $resultValueArray = [];

                $minVal = empty($classAttributes) ? 0 : PHP_INT_MAX;

                foreach ($classAttributes as $attr_Key => $attr_Data) {
                    $keyInReq = $prop->name . '__' . $attr_Key;
                    $cnt = (isset($data[$keyInReq]) && is_array($data[$keyInReq])) ? count($data[$keyInReq]) : 0;
                    $minVal = min($minVal, $cnt);
                }

                for ($i = 0; $i < $minVal; $i++) {
                    $resultValueArray[$i] = [];
                }

                foreach ($classAttributes as $attr_Key => $attr_Data) {
                    $keyInReq = $prop->name . '__' . $attr_Key;
                    if (empty($data[$keyInReq]) || !is_array($data[$keyInReq])) continue;

                    $vals = array_values($data[$keyInReq]);

                    for ($i = 0; $i < $minVal; $i++) {
                        if (isset($vals[$i])) {
                            $_v =  $vals[$i];
                            if (!is_scalar($_v)) continue;
                            $_v = self::CleanValue((string)$_v);

                            if($classAttributes[$attr_Key]["Type"]=="float"){
                                $_v = str_replace(",",".",$_v);
                                $_v = floatval($_v);
                            }
                            if($classAttributes[$attr_Key]["Type"]=="int")$_v = intval($_v);
                            $resultValueArray[$i][$attr_Key] = $_v;
                        }
                    }
                }
                //$data[$K] = $resultValueArray;

                $mrpModel->$K = $resultValueArray;
                continue;
            }


            if (!isset($data[$K])) {

                if ($prop->typeData == "checkbox") {
                    $data[$K] = false;
                } elseif ($prop->isCanEmpty === true && ($prop->typeData == "string" || $prop->typeData == "text")) {
                    $data[$K] = "";
                } else {
                    continue;
                }

            }


            if ($prop->typeData == "checkbox") {
                $_val = false;
                if (is_scalar($data[$K]) && in_array(strtolower((string)$data[$K]), ['on', '1', 'true'], true)) $_val = true;
                $mrpModel->$K  = $_val;
                continue;
            }

            if ($prop->typeData == "multioption") {

                $valueArray = [];
                $selected = is_array($data[$K]) ? array_map('strval', array_filter($data[$K], 'is_scalar')) : [];
                foreach ($prop->GetOptions() as $key => $_label) {
                    if (in_array((string)$key, $selected, true)) {

                        $valueArray[$key] = true;
                    }
                }

                $data[$K] = $valueArray;
            }




            $mrpModel->$K = self::CleanValue($data[$K]);

        }
    }

    public static function GetValidateRuleProperty(MPModel $mrpModel, $propertyName, $isRequired = true)
    {
        $props = $mrpModel->GetProperties();
        if (!isset($props[$propertyName])) {
            throw new \Exception("Не найден параметр " . $propertyName . " в модели " . get_class($mrpModel));
        }

        return MPModel::RenderValidateRuleByPropertyData($props[$propertyName], $isRequired);
    }


    public static function ValidateListGenerics(MPModel $mrpModel, $validator, $requestArray, $tag = null)
    {

        // dd($requestArray);
        foreach ($mrpModel->GetByTag($tag) as $K => $prop) {
            if (empty($prop->listClassGeneric)) continue;
            $msg = null;

            $classAttributes = ReadAttributesConfig::ReadClass($prop->listClassGeneric);


            $countLists = 0;

            foreach ($classAttributes as $attr_Key => $attr_Data) {
                $keyInReq = $prop->name . '__' . $attr_Key;
                if (empty($requestArray[$keyInReq])) continue;

                $vals = $requestArray[$keyInReq];

                $_attrName = $attr_Data['Name'] ?? $attr_Key;
                if (!is_array($vals)) {
                    $msg = "В поле " . $_attrName . ' некорректные данные';
                    break;
                }
                foreach ($vals as $_v) {
                    if (!is_null($_v) && !is_scalar($_v)) {
                        $msg = "В поле " . $_attrName . ' некорректные данные';
                        break 2;
                    }
                }

                $countLists = count($vals);


                $_msgTemplate = "В столбце " . $_attrName . ', в строчке ';

                // Строковая колонка без Lengh — раньше длина не ограничивалась вовсе
                if (($attr_Data['Type'] ?? 'string') == 'string' && !isset($attr_Data['Lengh']) && !isset($attr_Data['Options'])) {
                    foreach ($vals as $line => $valueVariant) {
                        if (mb_strlen((string)$valueVariant) > self::LIST_GENERIC_STRING_MAX) {
                            $msg = $_msgTemplate . ($line + 1) . ' слишком длинное значение (макс. ' . self::LIST_GENERIC_STRING_MAX . ')';
                            break 2;
                        }
                    }
                }


                if (isset($attr_Data['Options'])) {
                    foreach ($vals as $line => $valueVariant) {
                        if ($valueVariant === null || !isset($attr_Data['Options'][$valueVariant])) {

                            $msg = "В поле " . $attr_Data['Name'] . ' указан не верный тип';
                            break;
                            break;
                        }
                    }
                }

                if (isset($attr_Data['Type'])) {
                    if (isset($attr_Data['Lengh']) and $attr_Data['Type'] == "string") {
                        foreach ($vals as $valueVariant) {
                            $len = mb_strlen((string)$valueVariant);
                            if ($len < $attr_Data['Lengh'][0] or $len > $attr_Data['Lengh'][1]) {
                                $msg = "Поле " . $attr_Data['Name'] . ' должно иметь длинну от ' . $attr_Data['Lengh'][0] . ' до ' . $attr_Data['Lengh'][1];
                                break;
                                break;
                            }
                        }
                    }


                    if (isset($attr_Data['Lengh']) and $attr_Data['Type'] == "int") {
                        foreach ($vals as $line => $valueVariant) {
                            $len = intval($valueVariant);


                            if (preg_match('/^[\d]+$/', $valueVariant . '') !== 1) {
                                $msg = $_msgTemplate . ($line + 1) . ' должно быть целое число. Без пробелов и лишних символов';
                                break;
                                break;
                            }


                            if ($len < $attr_Data['Lengh'][0] or $len > $attr_Data['Lengh'][1]) {
                                $msg = $_msgTemplate . ($line + 1) . ' должно быть в пределах от ' . $attr_Data['Lengh'][0] . ' до ' . $attr_Data['Lengh'][1];
                                break;
                                break;
                            }
                        }
                    }

                    if (isset($attr_Data['Lengh']) and $attr_Data['Type'] == "float") {
                        foreach ($vals as $line => $valueVariant) {

                            if (preg_match('/^[\d.,]+$/', $valueVariant . '') !== 1) {
                                $msg = $_msgTemplate . ($line + 1) . ' должно быть число, без букв и лишних символов';
                                break;
                                break;
                            }

                            $valueVariant = str_replace(',', '.', $valueVariant);

                            if (floatval($valueVariant) . '' !== $valueVariant) {
                                $msg = $_msgTemplate . ($line + 1) . ' должно быть корректное значение';
                                break;
                                break;
                            }


                            $len = floatval($valueVariant);
                            if ($len < $attr_Data['Lengh'][0] or $len > $attr_Data['Lengh'][1]) {
                                $msg = $_msgTemplate . ($line + 1) . ' должно быть в пределах от ' . $attr_Data['Lengh'][0] . ' до ' . $attr_Data['Lengh'][1];
                                break;
                                break;
                            }
                        }
                    }

                }
            }


            if(!$msg) {
                if ($prop->listClassGenericAfterValidationAction) {
                    $action =  $prop->listClassGenericAfterValidationAction;
                    $msg = $action($requestArray);
                }
            }


            if ($countLists == 0) {
                $msg = "В списке нет данных";
            }
            // $msg = "countLists: $countLists";

            if ($msg) {
                $validator->after(function ($validator) use ($K, $prop, $msg) {
                    $validator->errors()->add($K,   $msg . ' ');
                });
            }
        }

    }
}
