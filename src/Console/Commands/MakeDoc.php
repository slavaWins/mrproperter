<?php

namespace MrProperter\Console\Commands;

use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use MrProperter\Library\MigrationRender;
use MrProperter\Library\PropertyBuilderStructure;
use MrProperter\Models\MPModel;
use Illuminate\Support\Facades\DB;

class MakeDoc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp:doc {Model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать php doc описание к модели';


    public function handle()
    {
        $name = $this->argument("Model");

        $pTo = MakeModel::GetModelPath($name);
        if (!file_exists($pTo)) return $this->error("Model not exist!");

        $cln = '\App\Models\\' . $name;

        /** @var MPModel $class */
        $model = new $cln();


        $f = file_get_contents($pTo);


        $fout = "";
        $needClassStart = strtolower("class" . $name . 'extends');

        $proplist = $model->GetProperties();
        foreach (explode("\n", $f) as $line) {
            if ($needClassStart) {

                //ищем все парметры здесь
                if (substr_count($line, '@property')) {
                    foreach ($proplist as $K => $V) {
                        if (substr_count($line, "$" . $K." ")) {
                            unset($proplist[$K]);
                            break;
                        }
                    }
                }

                $s = str_replace(" ", "", $line);

                $s = strtolower($s);
                $s = trim($s);
                if (strpos($s, $needClassStart) === 0) {

                    /** * @var  PropertyBuilderStructure $prop */
                    foreach ($proplist as $K => $prop) {
                        $fout .= "\n /** @property " . MigrationRender::GetType($prop->typeData);
                        if (!$prop->default) $fout .= "|null ";
                        $fout .= '$' . $K . ' ' . ($prop->comment ?? $prop->descr ?? " ");
                        $fout .= ' */';
                        $this->info("add @property ".$K);
                    }

                    $needClassStart = null;
                }
            }

            $fout .= "\n" . $line;
        }

        $fout = trim($fout);


        file_put_contents($pTo, $fout);


        $this->info("Пхп док создан ");
    }
}
