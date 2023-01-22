<?php

namespace MrProperter\Library;

use App\Models\PropertyBuilder\MPModel;
use App\Models\User;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\Str;

class MigrationRender
{

    public static function GetType($type)
    {
        if ($type == "int") return "integer";
        if ($type == "string") return 'string';
        if ($type == "select") return 'string';
        if ($type == "checkbox") return 'boolean';
        return 'string';
    }

    public static function RenderDoc(\MrProperter\Models\MPModel $model)
    {
        $list = [];
        $inputs = $model->GetPropertys();

        $text = "/**";
        foreach ($inputs as $ind => $prop) {
            $data = [];
//     * @property $data[]|mixed $className
            $text .= "\n * @property " . self::GetType($prop->typeData);

            if (!$prop->default) $text .= "|null";
            $text .= '$' . $ind;

            if ($prop->comment || $prop->descr) $text .= $prop->comment ?? $prop->descr;

        }
        $text .= "\n" . '*/';

        return $text . ' ';
    }

    public static function RenderMigration(\MrProperter\Models\MPModel $model, $ignoreKey = [], $isModify = false)
    {
        $list = [];
        $inputs = $model->GetPropertys();

        foreach ($inputs as $ind => $prop) {
            if (isset($ignoreKey[$ind] ) or in_array( $ind, $ignoreKey)) continue;
            $data = [];

            $data[self::GetType($prop->typeData)] = $ind;

            if ($prop->default) $data['default'] = $prop->default;

            if (!$prop->default) $data['nullable'] = null;
            if ($prop->comment || $prop->descr) $data['comment'] = $prop->comment ?? $prop->descr;


            foreach ($data as $K => $V) {

                if (is_string($V)) $V = '"' . $V . '"';
                if (is_null($V)) $V = '';

                if ($prop->typeData == "checkbox" and $K == "default") {
                    if ($V) {
                        $V = "true";
                    } else {
                        $V = "false";
                    }
                }
                $data[$K] = $V;
            }
            $list[$ind] = $data;
        }


        $tableName = $model->getTable();
        $modelName = basename(get_class($model));

        $className = "able_" . $tableName;
        if($isModify)$className.="_modify";
        if(!$isModify)$className.="_create";
        $className = "T" . Str::camel($className);

        $fileName = Str::snake($className);
        $fileName = str_replace($modelName, $tableName, $fileName);
        $fileName = date('Y_m_d_His') . '_' . $fileName . '.php';


        $view = view("mrproperter::migration", ['list' => $list, 'tableName' => $tableName, 'className' => $className,'isModify'=>$isModify]);
        return [
            'content' => $view,
            'file' => $fileName,
            'class' => $className,
            'table' => $tableName,
        ];
        return $view . ' ';
    }

}
