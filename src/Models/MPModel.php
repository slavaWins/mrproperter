<?php

namespace MrProperter\Models;

use App\Library\MrProperter\MigrationRender;
use App\Library\MrProperter\PropertyBuilderStructure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use MrProperter\Helpers\ReadAttributesConfig;
use MrProperter\Library;
use MrProperter\Library\PropertyConfigStructure;
use SlavaWins\Formbuilder\Library\FElement;

class MPModel extends Model
{

    /**
     * функция Property settings нужно для того чтобы настроить для модели конфиг в котором будут описаны все её параметры то есть мы просто перечисляем какой параметр какой у него тип как у него дефолтное значение настраиваем валидацию это всё делается в одном месте по сути это заменяет миграцию.
     * @return PropertyConfigStructure
     */
    public function PropertiesSetting()
    {

    }

    /**
     * функция Get validate Rules она нужна для того чтобы сгенерировать массив с данными которые можно будет использовать в валидации.
     * генерации правил валидации используется большая часть данных которых можно заполнить с помощью конфигом в особенности максимальное и минимальное значение тип данных и главное преимущество в том что это не нужно писать каждый раз то есть мы просто используем модель и возвращаем её правила
     * @param $tag
     * @return array
     */
    public static function GetValidateRules($tag = null, $isRequired = true)
    {
        /** @var MPModel $cl */
        $cln = get_called_class();
        $cl = new $cln();
        $props = $cl->GetByTag($tag);

        $rules = [];

        /**
         * @var  $K
         * @var PropertyBuilderStructure $prop
         */
        foreach ($props as $K => $prop) {
            $rules[$K] = self::RenderValidateRuleByPropertyData($prop, $isRequired);
        }

        return $rules;
    }

    public function GetValidateRulesInModel($tag = null, $isRequired = true)
    {

        $props = $this->GetByTag($tag);

        $rules = [];

        /**
         * @var  $K
         * @var PropertyBuilderStructure $prop
         */
        foreach ($props as $K => $prop) {
            $rules[$K] = self::RenderValidateRuleByPropertyData($prop, $isRequired);
            if (!$rules[$K]) unset($rules[$K]);
        }

        return $rules;
    }

    /**
     * @param $requestArray
     * @param $tag
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function GetValidatorRequest($requestArray, $tag = null)
    {
        $cln = get_called_class();

        $validator = Validator::make($requestArray, $cln::GetValidateRules($tag), [], $cln::GetValidateRulesFailedNames($tag));
        return $validator;
    }



    public function GetValidatorRequestInModel($requestArray, $tag = null)
    {
        $cln = get_called_class();

        $validator = Validator::make($requestArray, $this->GetValidateRulesInModel($tag), [], $cln::GetValidateRulesFailedNames($tag));


        Library\MrpValidateCommon::ValidateListGenerics($this, $validator, $requestArray, $tag);


        return $validator;

    }

    public static function GetValidateRulesFailedNames($tag = null)
    {
        /** @var MPModel $cl */
        $cln = get_called_class();
        $cl = new $cln();
        $props = $cl->GetByTag($tag);

        $rules = [];

        /**
         * @var  $K
         * @var PropertyBuilderStructure $prop
         */
        foreach ($props as $K => $prop) {
            $rules[$K] = $prop->label;
        }

        return $rules;
    }

    public static function RenderValidateRuleByPropertyData(Library\PropertyBuilderStructure $propertyData, $isRequired)
    {
        if ($propertyData->customValidationRule) return $propertyData->customValidationRule;

        $text = "";
        if ($isRequired) $text = "required|";


        $columType = Library\MigrationRender::GetType($propertyData->typeData);
        if ($columType == "text") $columType = "string";
        if ($columType == "float") $columType = "numeric";
        $text .= $columType;


        if ($propertyData->typeData == 'checkbox') {
            return "";
        }
        if ($propertyData->typeData == 'multioption') {
            return "array";
        }


        if ($propertyData->listClassGeneric) return null;
        if ($propertyData->max) $text .= "|max:" . $propertyData->max;
        if ($propertyData->min) $text .= "|min:" . $propertyData->min;
        if ($propertyData->typeData == "select" or $propertyData->typeData == "multioption") {
            $text .= "|in:";
            foreach ($propertyData->GetOptions() as $K => $V) $text .= '"' . $K . '",';
            $text = trim($text, ",");
        }
        $text = trim($text, "|");
        return $text;
    }


    /**
     * получить коллекцию полей по определенному тегу то есть нам возвращается только те поля у которых есть стык либо возвращаются все если так равен
     * когда мы заполняем конфиг для каждого поля мы можем использовать один или несколько тегов каждый тег позволяет настроить это поле для анального показа формах Допустим мы хотим сделать форму которая у нас выходит только название и стоимость товара остальные поля выводиться не должны мы должны придумать какое-то название для этой формы вписать ей аргумент тиг и также мы должны для ля для самого импланта тоже вписать этот тег
     * @param $tag
     * @return PropertyBuilderStructure[]
     */
    public
    function GetByTag($tag = null)
    {

        if (!$tag) return $this->GetProperties();
        return collect($this->GetProperties())->filter(function (Library\PropertyBuilderStructure $e) use ($tag) {
            if ($tag == null) return true;
            if (isset($e->tags[$tag])) return true;
            return false;
        });
    }


    public
    function BuildInputAll($tag = null)
    {
        $p = $this->GetProperties();

        $html = "";
        foreach ($p as $K => $V) {
            if (!isset($p[$K])) continue;
            if ($p[$K]->is_nonEditable) continue;

            if ($tag) {
                if (!$p[$K]->tags) continue;
                if (!isset($p[$K]->tags[$tag])) continue;
            }

            $html .= $this->BuildInput($K);
        }
    }

    /**
     * @param $ind
     * @param Library\PropertyBuilderStructure $prop
     * @param $value
     * @return void
     */
    public static function BuildInputByStruct($ind, $prop, $value)
    {

        if ($prop->typeData == "multioption") {
            FElement::New()->SetView()->H()->SetLabel($prop->label ?? $prop->name ?? "n/a")->RenderHtml(true);

            if (is_string($value)) $value = json_decode($value, true);
            foreach ($prop->GetOptions() as $K => $V) {
                $inp = FElement::NewInputText()->SetView()->InputBoolRow()->SetLabel($V ?? "na")
                    ->SetName($ind . '[]')
                    ->AddValueAttributeCheckbox($K)
                    ->SetValue(isset($value[$K]));
                $inp->RenderHtml(true);
            }
            return;
        }

        $inp = FElement::NewInputText();


        if ($prop->typeData == "checkbox") {
            $inp = FElement::NewInputText()->SetView()->InputBoolRow();
        } elseif ($prop->typeData == "text" or $prop->typeData == "int") {

            $inp = FElement::NewInputText();


        } elseif ($prop->typeData == "select") {
            $inp = FElement::NewInputText()->SetView()->InputSelect()->AddOptionFromArray($prop->GetOptions());
        } elseif ($prop->listClassGeneric) {
            $inp = FElement::NewInputText()->SetView()->ListGenericClass()->SetExampleModel($prop->listClassGeneric);
        }


        $inp = $inp
            ->SetLabel($prop->label ?? $prop->name ?? "na")
            ->SetLabel($prop->label ?? $prop->name ?? "na")
            ->SetPlaceholder($prop->descr ?? null)
            ->SetName($ind)
            ->SetDescr($prop->descr ?? null); //->FrontendValidate()->String(0, 75)

        if ($prop->max) {
            $inp->FrontendValidate()->String($prop->min, $prop->max);
        }

        if ($prop->typeData == "string") {
            if (is_object($value)) {
                if (get_class($value) == 'Illuminate\Support\Carbon') {
                    $value = $value->format("d.m.Y");
                }
            }
            //   $inp->FrontendValidate()->String($prop->min, $prop->max ?? 999999);
        }

        if ($prop->listClassGeneric) {
            if (!is_array($value)) $value = [];

            $templateElement = new $prop->listClassGeneric();
            $templateElement = (array)$templateElement;

            $templateElement['__templateData'] = true;

            $value[] = $templateElement;
        }

        $html = $inp->SetValue(old($ind, $value))
            ->RenderHtml(true);
    }

    public function BuildInput($ind)
    {
        $p = $this->GetProperties();
        if (!isset($p[$ind])) return null;
        $prop = $p[$ind];
        $value = $this->$ind ?? $prop->default ?? "";
        self::BuildInputByStruct($ind, $prop, $value);

    }


    private   $propertestConfig;



    public function ValidateAndFilibleByRequest($data, $tag = null)
    {

        $cl = get_called_class();

        $validator = $this->GetValidatorRequestInModel($data, $tag);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        Library\MrpValidateCommon::PropertyFillebleByTag($this, $data, $tag);

        $this->save();
        return true;
    }

    public function PropertyFillebleByTag($data, $tag)
    {
        return   Library\MrpValidateCommon::PropertyFillebleByTag($this, $data, $tag);
    }

    /**
     * @return Library\PropertyBuilderStructure[]
     */
    public function GetProperties()
    {
        // if ($this->propertestConfig) return $this->propertestConfig;
        $d = $this->PropertiesSetting();

        if (isset($d->isPropertyConfigStructure)) $d = $d->GetConfig();

        foreach ($d as $K => $V) {
            $d[$K]->value = $this->$K ?? $V->default ?? "";
            $d[$K]->model = $this;
        }

        $this->propertestConfig = $d;
        return $this->propertestConfig;
    }

    public function GetAllTags()
    {
        $list = [];
        foreach ($this->GetProperties() as $K => $V) {

            if (!$V->tags) continue;
            foreach ($V->tags as $tag) {
                if (in_array($tag, $list)) continue;
                $list[] = $tag;
            }


        }
        return $list;
    }

}
