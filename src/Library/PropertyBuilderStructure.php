<?php


namespace MrProperter\Library;

use App\Models\User;
use http\Params;

class PropertyBuilderStructure
{
    public string $name;
    public string $descr = "";
    public $tags = null;
    public string $icon = "";
    public $default = 0;
    public $max = null;
    public $min = 0;
    public $value = 0;
    public string $typeData = "int";
    public bool $isShowShort = true;
    public $postfix;
    private string $format = "";
    private bool $isShowProgressBar = false;
    private $progressBarIcon_On = '▰';
    private $progressBarIcon_Off = '▱';
    /**
     * @var true
     */
    public bool $is_hidden_property = false;


    public $preapendLabel; //добавить лейбл перед этим параметром
    public bool $shopCategory = false;
    public array $options;
    public $comment;

    /**
     * @return \App\Library\MrProperter\PropertyBuilderStructure
     */
    public static function Int($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'int';
        return $s;
    }    /**
 * @return PropertyBuilderStructure
 */
    public static function Select($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'select';
        return $s;
    }

    public static function Checkbox($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'checkbox';
        return $s;
    }

    public static function String($val)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $val;
        $s->typeData = 'string';
        return $s;
    }

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

    public function ShowProgressBar()
    {
        $this->isShowProgressBar = true;
        return $this;
    }

    public function SetTypeString()
    {
        $this->typeData = 'string';
        return $this;
    }

    public function SetTypeFloat()
    {
        $this->typeData = 'float';
        return $this;
    }

    public function SetTypeInt()
    {
        $this->typeData = 'int';
        return $this;
    }

    public function SetIcon($val)
    {
        $this->icon = $val;
        return $this;
    }


    public function SetOptions(array $array)
    {
        $this->options = $array;
        return $this;
    }

    public function PreapendLabel($val)
    {

        $this->preapendLabel = $val;
        return $this;
    }

    public function SetDefault($val)
    {
        $this->default = $val;
        $this->value = $val;
        return $this;
    }

    public function SetValue($val)
    {
        $this->value = $val;
        return $this;
    }

    public function Hidden()
    {
        $this->is_hidden_property = true;
        return $this;
    }

    public function ShopCategory()
    {
        $this->shopCategory = true;
        return $this;
    }

    public function SetShowInShort($val)
    {

        $this->isShowShort = $val;
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

    public function SetProgressBarIcons($on, $off)
    {
        $this->progressBarIcon_On = $on;
        $this->progressBarIcon_Off = $off;
        return $this;
    }

    public function RenderLine($isShort = true, $isShowDescr = false, $asValue = null)
    {
        $val = $this->value;
        if ($asValue <> null) {
            $val = $asValue;
        }

        $text = "";

        if (!$isShort && $isShowDescr && !empty($this->descr)) {
            $text .= "\n";
        }
        $text .= $this->icon;

        if (!$isShort) {
            $text .= " " . $this->name . ': ';
        }

        if ($this->format == "money") {
            $text .= " " . number_format($val);
        } elseif ($this->typeData == "float") {
            $text .= " " . number_format($val, 2);
        } else {
            $text .= " " . $val;
        }

        if (!$this->max) {
            $text .= $this->postfix ?? '';
        }

        if ($this->max) {
            $text .= "/" . $this->max . ($this->postfix ?? '');
        }

        if (!$isShort && $this->max && $this->isShowProgressBar) {
            $text .= " ";
            $percent = $val / $this->max * 10;
            $percent = floor($percent);
            $text .= str_repeat($this->progressBarIcon_On, $percent);
            $text .= str_repeat($this->progressBarIcon_Off, 10 - $percent);
        }


        if (!$isShort && $isShowDescr && !empty($this->descr)) {
            $text .= "\n";
            $text .= $this->descr;
        }


        return $text;
    }
}


