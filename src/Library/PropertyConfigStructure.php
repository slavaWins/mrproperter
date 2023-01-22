<?php


namespace MrProperter\Library;

use App\Models\User;
use http\Params;
use MrProperter\Models\MPModel;

class PropertyConfigStructure
{
    private MPModel $model;


    private $list = [];

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

    public function Checkbox($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'checkbox';
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

    public function Int($name)
    {
        $s = new PropertyBuilderStructure();
        $s->name = $name;
        $s->typeData = 'int';
        $this->list[$name] = $s;
        return $s;
    }
}
