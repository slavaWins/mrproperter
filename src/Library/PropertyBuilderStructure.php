<?php


namespace MrProperter\Library;

use App\Models\User;
use http\Params;
use MrProperter\Models\MPModel;

class PropertyBuilderStructure
{
    public ?MPModel $model = null;
    public string $name;
    public string $label = "";
    public ?string $customValidationRule = null;
    public string $descr = "";
    public string $placeholder = "";
    public $tags = null;
    public string $icon = "";
    public $default = null;
    public bool|null $required =null;
    public bool|null $isCanEmpty =null;
    public $listClassGeneric =null;
    public $listClassGenericAfterValidationAction = null;


    public $max = null;
    public $min = null;
    public $value = 0;
    public $belongsToClass = null;
    public $belongsMethod = null;
    public $belongsPropertyText = null;
    public string $typeData = "int";
    public $prefix;
    public $visibleRule=null;
    public array $labelsWithTag = [];
    public $postfix;
    public ?string $frontendMask = null;
    public ?bool $frontendMaskReverse = null;
    private string $format = "";
    /**
     * @var true
     */
    public bool $is_hidden_property = false;
    public bool $is_nonEditable = false;
    public array $options;
    public  $dynamicOptionCallbale = null;
    public $comment;

    /**
     * @return \App\Library\MrProperter\PropertyBuilderStructure
     */


    public function Comment($val)
    {
        $this->comment = $val;
        return $this;
    }

    public function SetDescr($val)
    {
        $this->descr = $val;
        return $this;
    }

    public function SetPlaceholder($val)
    {
        $this->placeholder = $val;
        return $this;
    }

    public function SetLabel($val)
    {
        $this->label = $val;
        return $this;
    }

    public function SetValidationRule($val)
    {
        $this->customValidationRule = $val;
        return $this;
    }

    public function AddTag($val)
    {
        if ($this->tags == null) $this->tags = [];

        if (is_array($val)) {
            foreach ($val as $V) $this->tags[$V] = $V;
            return $this;
        }

        $this->tags[$val] = $val;
        return $this;
    }


    /**
     * @param callable $call
     * @return $this
     */
    public function SetDynamicOption(callable $call)
    {
        $this->options=[];
        $this->dynamicOptionCallbale = $call;

        return $this;
    }

    public function SetOptions(array $array)
    {
        $this->options = $array;
        return $this;
    }

    public function GetOptions()
    {


        if($this->dynamicOptionCallbale && empty($this->options)){
            $f = $this->dynamicOptionCallbale;
            $this->options = $f($this->model);
        }
        return $this->options;
    }


    public function SetRequired(bool $val)
    {
        $this->required = $val;
        return $this;
    }
    public function CanEmpty(bool $val = true)
    {
        $this->isCanEmpty = $val;
        return $this;
    }

    public function SetDefault($val)
    {
        $this->default = $val;
        $this->value = $val;

        if($val===null){
            $this->required=false;
        }

        return $this;
    }

    public function ListClassGeneric($classType)
    {
        $this->listClassGeneric = $classType;
        return $this;
    }


    public function ListClassGenericAfterValidation($action)
    {
        $this->listClassGenericAfterValidationAction = $action;
        return $this;
    }


    public function SetBelong($className, $method, $otherNameProperty)
    {
        $this->belongsToClass = $className;
        $this->belongsMethod = $method;
        $this->belongsPropertyText = $otherNameProperty;
        return $this;
    }


    public function Hidden()
    {
        $this->is_hidden_property = true;
        return $this;
    }

    public function NonEditable()
    {
        $this->is_nonEditable = true;
        return $this;
    }


    public function SetMax($val)
    {
        $this->max = $val;
        return $this;
    }

    public function SetMin($val)
    {
        $this->min = $val;
        return $this;
    }

    public function FrontendMask($mask, $isReverse = false)
    {
        $this->frontendMask = $mask;
        $this->frontendMaskReverse = $isReverse;
        return $this;
    }

    public function FormatMoney()
    {
        $this->format = 'money';
        return $this;
    }

    public function SetPostfix($val)
    {
        $this->postfix = $val;
        return $this;
    }

    public function SetPrefix($val)
    {
        $this->prefix = $val;
        return $this;
    }


    public function VisibleRuleIs($targetInput, $targetValue)
    {
        $this->visibleRule = [
            "key"=>$targetInput,
            "val"=>$targetValue,
        ];
        return $this;
    }

    public function SetLabelsWithTag(array|string $tag, $label, $placeholder = "", $description = "")
    {
        $_tags = $tag;

        if(is_string($tag)){
            $_tags = [$tag];
        }

        foreach ($_tags as $tag) {
            $this->labelsWithTag[$tag] = [
                'label' => $label,
                'placeholder' => $placeholder,
                'description' => $description,
            ];
        }
        return $this;
    }

    public function RenderValue()
    {


        if ($this->value === null) return "Не указано";


        if ($this->belongsToClass) {
            if (!$this->model) return "Не инциализировано";
            $meth = $this->belongsMethod;
            if (!isset($this->model->$meth)) return "Нет данных";

            $objTo = $this->model->$meth;
            if (!$objTo) return "Нет связи";

            $toProp = $this->belongsPropertyText;
            if(substr_count($toProp,'()')){
                $toProp = str_replace("()", "", $toProp);
                if(!method_exists($objTo, $toProp)){
                    return "У связи нет метода " . $toProp;
                }
                return  $objTo->$toProp() .' #' .$objTo->id;
            }else {
                if (!isset($objTo->$toProp)) return "У связи нет переменной " . $toProp;

                return  $objTo->$toProp .' #' .$objTo->id;
            }
        }

        if ($this->typeData == "checkbox") {
            if ($this->value) return "Да";
            return "Нет";
        }

        if ($this->typeData == "select") {
            if (!isset($this->GetOptions()[$this->value])) return "Опция не найдена";
            return $this->GetOptions()[$this->value];
        }


        if ($this->typeData == "multioption") {
            $val = $this->value;
            if (is_string($val)) {
                $val = json_decode($val, true);
            }

            if(empty( $this->value))return  "Не указано";
            if(empty($val))return  "Не указано";

            $text = "";
            foreach ($val as $K=>$V){
                if (isset($this->GetOptions()[$K])){
                    $text.=", ".$this->GetOptions()[$K];
                }else {
                    $text .= ", " . $K;
                }
            }
            $text = trim($text, ", ");
             return $text;
        }

        $val = $this->value;

        if ($this->postfix) $val = $val . $this->postfix;
        if ($this->prefix) $val = $this->prefix . $val;
        return $val;
    }
}


