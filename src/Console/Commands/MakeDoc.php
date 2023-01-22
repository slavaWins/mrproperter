<?php

namespace MrProperter\Console\Commands;

use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use MrProperter\Library\MigrationRender;
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

        $info = MigrationRender::RenderDoc($model);

        $f = file_get_contents($pTo);

        if (substr_count($f, "@property")) {
            return $this->error("Сначала удали все @property перед class " . $name);
        }

        $fout = "";
        $need = strtolower("class" . $name . 'extends');

        foreach (explode("\n", $f) as $V) {
            if ($need) {
                $s = str_replace(" ", "", $V);
                $s = strtolower($s);
                $s = trim($s);
                if (strpos($s, $need) === 0) {
                    $fout .= "\n" . $info;
                   // $fout .= "\n" . $V;
                    $need = null;
                }
            }
            $fout .= "\n" . $V;
        }
        $fout =trim($fout);


        file_put_contents($pTo, $fout);


        $this->info("Пхп док создан ");
    }
}
