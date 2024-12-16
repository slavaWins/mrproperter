<?php


namespace MrProperter\Library;

use App\Models\User;
use http\Params;
use MrProperter\Models\MPModel;

class PropertyConfigStructure
{
    private MPModel $model;

    public $isPropertyConfigStructure = true;
    public $list = [];

    public function __construct(MPModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return PropertyBuilderStructure
     */
    public function Select($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'select';
        $this->list[$name] = $s;
        return $s;
    }

    /**
     * @return PropertyBuilderStructure
     */
    public function Json($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'json';
        $this->list[$name] = $s;
        return $s;
    }

    /**
     * @return PropertyBuilderStructure
     */
    public function Multioption($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'multioption';
        $this->list[$name] = $s;
        return $s;
    }

    public function Checkbox($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'checkbox';
        $this->list[$name] = $s;
        return $s;
    }

    public function Date($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'date';
        $this->list[$name] = $s;
        return $s;
    }

    /**
     * @return PropertyBuilderStructure[]
     */
    public function GetConfig()
    {
        $mta = $this->model->toArray();
        foreach ($this->list as $K => $V) {
            if (isset($mta->$K)) {
                $V->value = $mta->$K;
            }
        }

        return $this->list;
    }

    public function String($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'string';
        $this->list[$name] = $s;
        return $s;
    }

    public function Text($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'text';
        $this->list[$name] = $s;
        return $s;
    }

    public function Int($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'int';
        $this->list[$name] = $s;
        return $s;
    }

    public function Float($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'float';
        $this->list[$name] = $s;
        return $s;
    }
}
