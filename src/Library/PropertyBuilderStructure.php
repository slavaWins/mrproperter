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
    public $tags = null;
    public string $icon = "";
    public $default = 0;


    public $max = null;
    public $min = 0;
    public $value = 0;
    public $belongsToClass = null;
    public $belongsMethod = null;
    public $belongsPropertyText = null;
    public string $typeData = "int";
    public $prefix;
    public $postfix;
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
      //  $this->options=[];
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


    public function SetDefault($val)
    {
        $this->default = $val;
        $this->value = $val;
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
            if (!isset($this->options[$this->value])) return "Опция не найдена";
            return $this->options[$this->value];
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
                if (isset($this->options[$K])){
                    $text.=", ".$this->options[$K];
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


