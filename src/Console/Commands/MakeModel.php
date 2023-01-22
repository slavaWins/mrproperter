<?php

namespace MrProperter\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp:model {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать модель';


    public static function GetModelPath($name)
    {
        $pTo = app_path("Models");

        $pTo = $pTo . '/' . $name . '.php';
        return $pTo;
    }

    public function handle()
    {
        $name = $this->argument("name");

        $pTo = self::GetModelPath($name);
        if (file_exists($pTo)) {
            return $this->error("Model exist!");
        }

        $templateFile = file_get_contents(__DIR__ . '/../../template/TemplateModel.php');
        $templateFile = str_replace("TemplateModel", $name, $templateFile);

        file_put_contents($pTo, $templateFile);

        $this->info("Модель создана");
    }
}
