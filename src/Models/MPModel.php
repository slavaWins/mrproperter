<?php

namespace MrProperter\Models;

use App\Library\MrProperter\MigrationRender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use MrProperter\Library;
use MrProperter\Library\PropertyBuilderStructure;
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
        return $this->_propertyConfigStructure;
    }

    public ?PropertyConfigStructure $_propertyConfigStructure = null;

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

        $validator = Validator::make($requestArray, $this->GetValidateRulesInModel($tag), [], $this->GetValidateRulesFailedNamesInner($tag));


        Library\MrpValidateCommon::ValidateListGenerics($this, $validator, $requestArray, $tag);


        return $validator;

    }

    public  function GetValidateRulesFailedNamesInner($tag = null)
    {

        $props = $this->GetByTag($tag);

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

    public static function GetValidateRulesFailedNames($tag = null)
    {
        /** @var MPModel $cl */
        $cln = get_called_class();
        $cl = new $cln();
        return $cl->GetValidateRulesFailedNamesInner($tag);
    }

    public static function RenderValidateRuleByPropertyData(Library\PropertyBuilderStructure $propertyData, $isRequired)
    {
        if ($propertyData->customValidationRule) return $propertyData->customValidationRule;


        if(!is_null($propertyData->required)) {
            if ($propertyData->required === false) $isRequired = false;
        }

        $text = "";
        if ($isRequired) $text = "required|";





        $columTypeOriginal = Library\MigrationRender::GetType($propertyData->typeData);
        $columType = $columTypeOriginal;
        if ($columType == "text") $columType = "string";
        if ($columType == "float") $columType = "numeric";
        $text .= $columType;


        if ($propertyData->typeData == 'checkbox') {
            return "nullable";
        }

        if ($propertyData->typeData == 'multioption') {
            return "array";
        }

        if ($columType == "string") {
            if (!$isRequired){
                $text .= "|nullable";
            }
            if ($propertyData->isCanEmpty===true){
                $text = str_replace("required|","", $text);
                $text .= "|nullable";
            }
        }

        if ($propertyData->listClassGeneric) return null;
        if ($propertyData->max>0) $text .= "|max:" . $propertyData->max;

        if (!$propertyData->max &&  $columTypeOriginal == "string") {
            $text .= "|max:255";
        }

        if (!is_null( $propertyData->min)) $text .= "|min:" . $propertyData->min;
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
    function BuildInputAll($tag = null, $isEcho = true)
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

            $html .= $this->BuildInput($K, $tag, $isEcho);
        }

        if(!$isEcho)return $html;
        return null;
    }


    /**
     * @param $ind
     * @param PropertyBuilderStructure $prop
     * @param $value
     * @param $fromTag
     * @return FElement[]
     */
    public static function BuildFElementByStruct($ind, $prop, $value, $fromTag = null)
    {

        $felements = [];

        $label = $prop->label ?? $prop->name ?? "n/a";
        $placeholder=   $prop->placeholder ?? null;
        $descr =   $prop->descr ?? null;

        if(isset($prop->labelsWithTag[$fromTag])){
            $label = $prop->labelsWithTag[$fromTag]['label']  ?? $label;
            $descr = $prop->labelsWithTag[$fromTag]['description'] ?? $placeholder;
            $placeholder = $prop->labelsWithTag[$fromTag]['placeholder']  ?? $descr;
        }


        if ($prop->typeData == "multioption") {
            $felements[] = FElement::New()->SetView()->H()->SetLabel($label);

            if (is_string($value)) $value = json_decode($value, true);
            foreach ($prop->GetOptions() as $K => $V) {
                $inp = FElement::NewInputText()->SetView()->InputBoolRow()->SetLabel($V ?? "na")
                    ->SetName($ind . '[]')
                    ->AddValueAttributeCheckbox($K)
                    ->SetValue(isset($value[$K]));

                $felements[] = $inp;
            }
            return $felements;
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
            ->SetLabel($label)
            ->SetPlaceholder($placeholder)
            ->SetName($ind)
            ->SetDescr($descr);

        $inp->data->visibleRule = $prop->visibleRule;
        $inp->data->prefix = $prop->prefix;
        $inp->data->postfix = $prop->postfix;
        $inp->data->dataMask = $prop->frontendMask;
        $inp->data->dataMaskReverse = $prop->frontendMaskReverse===true;


        if ($prop->max) {
            $inp->FrontendValidate()->String($prop->min, $prop->max);
        }

        if ($prop->typeData == "string") {
            if (is_object($value)) {
                if (get_class($value) == 'Illuminate\Support\Carbon') {
                    $value = $value->format("d.m.Y");
                }
            }
        }

        if ($prop->listClassGeneric) {
            if (!is_array($value)) $value = [];

            $templateElement = new $prop->listClassGeneric();
            $templateElement = (array)$templateElement;

            $templateElement['__templateData'] = true;

            $value[] = $templateElement;
        }

        $felements[] = $inp;

        return $felements;

    }

    /**
     * @param $ind
     * @param Library\PropertyBuilderStructure $prop
     * @param $value
     * @return void
     */
    public static function BuildInputByStruct($ind, $prop, $value, $fromTag = null, $isEcho = true)
    {

        $felements = self::BuildFElementByStruct($ind, $prop, $value, $fromTag);
        $html = '';

        foreach ($felements as $felement){
            $html.= $felement->SetValue(old($ind, $value))
                ->RenderHtml($isEcho);

        }


        if(!$isEcho)return $html;
        return null;

    }


    public function BuildInput($ind, $fromTag = null, $isEcho = true)
    {
        $p = $this->GetProperties();
        if (!isset($p[$ind])) return null;
        $prop = $p[$ind];
        $value = $this->$ind ?? $prop->default ?? "";

        $html = self::BuildInputByStruct($ind, $prop, $value, $fromTag, $isEcho);
        if(!$isEcho)return $html;
        return null;
    }


    private $propertestConfig;


    public function ValidateAndFilibleByRequest($data, $tag = null)
    {

        $cl = get_called_class();

        $validator = $this->GetValidatorRequestInModel($data, $tag);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $data = $validator->validated();

        Library\MrpValidateCommon::PropertyFillebleByTag($this, $data, $tag);



        $this->save();
        return true;
    }

    public function PropertyFillebleByTag($data, $tag)
    {
        return Library\MrpValidateCommon::PropertyFillebleByTag($this, $data, $tag);
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
