<?php

    namespace MrProperter\Library;

    use App\Models\PropertyBuilder\MPModel;
    use App\Models\User;
    use Illuminate\Database\Migrations\MigrationCreator;
    use Illuminate\Support\Str;

    class MigrationRender
    {

        public static function GetType($type) {
            if ($type == "text") return "text";
            if ($type == "int") return "integer";
            if ($type == "string") return 'string';
            if ($type == "select") return 'string';
            if ($type == "checkbox") return 'boolean';
            if ($type == "float") return 'float';
            if ($type == "multioption") return 'string';
            if ($type == "json") return 'json';
            if ($type == "date") return 'timestamp';

            return 'string';
        }

        public static function RenderDoc(\MrProperter\Models\MPModel $model) {
            $list = [];
            $inputs = $model->GetProperties();

            $text = "/**";
            foreach ($inputs as $ind => $prop) {
                $data = [];
                //     * @property $data[]|mixed $className
                $text .= "\n * @property ".self::GetType($prop->typeData);

                if (!$prop->default) $text .= "|null";
                $text .= ' $'.$ind;

                if ($prop->comment || $prop->descr) $text .= " ".($prop->comment ?? $prop->descr);

            }
            $text .= "\n".'*/';

            return $text.' ';
        }

        public static function RenderMigration(\MrProperter\Models\MPModel $model, $ignoreKey, $isModify = false) {


            $list = [];
            $inputs = $model->GetProperties();

            foreach ($inputs as $ind => $prop) {
                if (isset($ignoreKey[$ind])) continue;

                $data = [];

                $columType = self::GetType($prop->typeData);
                //   if ($columType == "text") $columType = "string";
                $data[$columType] = $ind;

                $data['default'] = $prop->default;



                if ($prop->default === null) {
                    unset($data['default']);
                    $data['nullable'] = null;
                }

                if ($prop->comment || $prop->descr || $prop->label) $data['comment'] = $prop->label;

                if ($prop->typeData == "text") {
                    unset($data['default']);
                    $data['nullable'] = null;
                }

                if ($prop->typeData == "json") {
                    unset($data['default']);
                    $data['nullable'] = null;
                }

                if ($prop->typeData == "multioption") {
                    $data = [
                        'json'    => '"'.$ind.'"',
                        'comment' => '"'.($prop->comment ?? $prop->descr ?? "").'"',
                    ];
                    $list[$ind] = $data;
                    continue;
                }

                foreach ($data as $K => $V) {

                    if (is_string($V)) $V = '"'.$V.'"';
                    if (is_null($V)) $V = '';

                    if ($prop->typeData == "checkbox" and $K == "default") {
                        if ($V) {
                            $V = "true";
                        }else {
                            $V = "false";
                        }
                    }
                    $data[$K] = $V;
                }


                if($prop->typeData=="select" or $prop->belongsMethod) {
                    $data['index'] = null;
                }

                $list[$ind] = $data;
            }

            $tableName = $model->getTable();
            $modelName = basename(get_class($model));

            $className = "able_".$tableName;
            if ($isModify) $className .= "add";
            if (!$isModify) $className .= "create";

            $appendClassName = 'add_';
            foreach (collect($list)->take(3) as $K => $V) {
                $appendClassName .= '_'.strtolower($K);
            }


            $className .= '_'.$appendClassName;
            $className .= '_'.substr(md5(time()), 0, 5);

            $className = "T".Str::camel($className);


            $fileName = Str::snake($className);
            $fileName = str_replace($modelName, $tableName, $fileName);
            $fileName = date('Y_m_d_His').'_'.$fileName.'.php';


            $view = view("mrproperter::migration", ['list' => $list, 'tableName' => $tableName, 'className' => $className, 'isModify' => $isModify]);
            $response = ['content' => $view.'', 'file' => $fileName, 'class' => $className, 'table' => $tableName,];

            if (empty($list)) $response['content'] = null;

            return $response;
        }

    }
