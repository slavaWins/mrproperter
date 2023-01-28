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
        if ($type == "text") return "text";
        if ($type == "int") return "integer";
        if ($type == "string") return 'string';
        if ($type == "select") return 'string';
        if ($type == "checkbox") return 'boolean';
        if ($type == "float") return 'float';
        return 'string';
    }

    public static function RenderDoc(\MrProperter\Models\MPModel $model)
    {
        $list = [];
        $inputs = $model->GetProperties();

        $text = "/**";
        foreach ($inputs as $ind => $prop) {
            $data = [];
            //     * @property $data[]|mixed $className
            $text .= "\n * @property " . self::GetType($prop->typeData);

            if (!$prop->default) $text .= "|null";
            $text .= ' $' . $ind;

            if ($prop->comment || $prop->descr) $text .= " " . ($prop->comment ?? $prop->descr);

        }
        $text .= "\n" . '*/';

        return $text . ' ';
    }

    public static function RenderMigration(\MrProperter\Models\MPModel $model, $ignoreKey, $isModify = false)
    {


        $list = [];
        $inputs = $model->GetProperties();

        foreach ($inputs as $ind => $prop) {
            if (isset($ignoreKey[$ind])) continue;

            $data = [];

            $columType = self::GetType($prop->typeData);
         //   if ($columType == "text") $columType = "string";
            $data[$columType] = $ind;

            $data['default'] = $prop->default;

            if ($prop->default === null) $data['nullable'] = null;
            if ($prop->comment || $prop->descr) $data['comment'] = $prop->comment ?? $prop->descr;

            if ($prop->typeData == "text") unset($data['default']);


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
        if ($isModify) $className .= "_modify";
        if (!$isModify) $className .= "_create";
        $className = "T" . Str::camel($className);

        $fileName = Str::snake($className);
        $fileName = str_replace($modelName, $tableName, $fileName);
        $fileName = date('Y_m_d_His') . '_' . $fileName . '.php';


        $view = view("mrproperter::migration", ['list' => $list, 'tableName' => $tableName, 'className' => $className, 'isModify' => $isModify]);
        $response = ['content' => $view . '', 'file' => $fileName, 'class' => $className, 'table' => $tableName,];

        if (empty($list)) $response['content'] = null;
        return $response;
    }

}
