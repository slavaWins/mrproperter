<?php


namespace MrProperter\Library;

use App\Models\User;
use http\Params;

class PropertyBuilderStructure
{
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
    public string $typeData = "int";
    public $postfix;
    private string $format = "";
    /**
     * @var true
     */
    public bool $is_hidden_property = false;
    public array $options;
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




    public function SetOptions(array $array)
    {
        $this->options = $array;
        return $this;
    }


    public function SetDefault($val)
    {
        $this->default = $val;
        $this->value = $val;
        return $this;
    }


    public function Hidden()
    {
        $this->is_hidden_property = true;
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
}


