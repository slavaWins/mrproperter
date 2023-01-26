<?php

namespace MrProperter\Models;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use MrProperter\Library;
use App\Library\MrProperter\MigrationRender;
use App\Library\MrProperter\PropertyBuilderStructure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use MrProperter\Library\PropertyConfigStructure;
use SlavaWins\Formbuilder\Library\FElement;
use Illuminate\Validation\Rule;

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

    /**
     * @param $requestArray
     * @param $tag
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function GetValidatorRequest($requestArray, $tag = null){
        $cln = get_called_class();
        $validator = Validator::make($requestArray, $cln::GetValidateRules($tag), [],$cln::GetValidateRulesFailedNames($tag));
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

    private static function RenderValidateRuleByPropertyData(Library\PropertyBuilderStructure $propertyData, $isRequired)
    {
        if ($propertyData->customValidationRule) return $propertyData->customValidationRule;

        $text = "";
        if ($isRequired) $text = "required|";

        $text .= Library\MigrationRender::GetType($propertyData->typeData);
        if ($propertyData->max) $text .= "|max:" . $propertyData->max;
        if ($propertyData->min) $text .= "|min:" . $propertyData->min;
        if ($propertyData->typeData == "select") {
            $text .= "|in:";
            foreach ($propertyData->options as $K => $V) $text .= '"' . $K . '",';
            $text = trim($text, ",");
        }
        $text = trim($text, "|");
        return $text;
    }

    public static function GetValidateRuleProperty($propertyName, $isRequired = true)
    {
        /** @var MPModel $cl */
        $cln = get_called_class();
        $cl = new $cln();
        $props = $cl->GetProperties();
        if (!isset($props[$propertyName])) {
            throw new \Exception("Не найден параметр " . $propertyName . " в моделе " . $cln);
        }
        $rules = [];

        $rules[$K] = self::RenderValidateRuleByPropertyData($props[$propertyName], $isRequired);
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
        foreach ($this->toArray() as $K => $V) {
            if (!isset($p[$K])) continue;

            if ($tag) {
                if (!$p[$K]->tags) continue;
                if (!isset($p[$K]->tags[$tag])) continue;
            }
            $html .= $this->BuildInput($K);
        }
    }

    public
    function BuildInput($ind)
    {
        $p = $this->GetProperties();
        if (!isset($p[$ind])) return null;
        $prop = $p[$ind];

        $inp = FElement::NewInputText();

        if ($prop->typeData <> "checkbox") {

        }

        if ($prop->typeData == "checkbox") {
            $inp = FElement::NewInputText()->SetView()->InputBoolRow();
        } elseif ($prop->typeData == "text" or $prop->typeData == "int") {
            $inp = FElement::NewInputText();
        } elseif ($prop->typeData == "select") {
            $inp = FElement::NewInputText()->SetView()->InputSelect()->AddOptionFromArray($prop->options);
        }

        $inp = $inp
            ->SetLabel($prop->label ?? $prop->name ?? "na")
            ->SetPlaceholder($prop->descr ?? null)
            ->SetName($ind)
            ->SetDescr($prop->descr ?? null); //->FrontendValidate()->String(0, 75)


        if ($prop->typeData == "string") {
            $inp->FrontendValidate()->String($prop->min, $prop->max ?? 999999);
        }

        $html = $inp->SetValue(old($ind, $this->$ind ?? ""))
            ->RenderHtml(true);

    }

    private
        $propertestConfig;

    public function PropertyFillebleByTag($data, $tag = null)
    {
        $pros = $this->GetByTag($tag);
        foreach ($pros as $K => $V) {
            if(!isset($data[$K]))continue;
            $this->$K = $data[$K];
        }
    }

    /**
     * @return PropertyBuilderStructure[]
     */
    public function GetProperties()
    {
        if ($this->propertestConfig) return $this->propertestConfig;
        $d = $this->PropertiesSetting();

        if (isset($d->isPropertyConfigStructure))$d= $d->GetConfig();

        $this->propertestConfig = $d;
        return $this->propertestConfig;
    }

}
