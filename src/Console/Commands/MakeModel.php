<?php

namespace MrProperter\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use MrProperter\Helpers\FinderParts;

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


    public function handle()
    {
        $name = $this->argument("name");

        $pTo = FinderParts::GetModelPath($name);
        if (file_exists($pTo)) {
            return $this->error("Model exist!");
        }

        $templateFile = file_get_contents(__DIR__ . '/../../template/TemplateModel.php');
        $templateFile = str_replace("TemplateModel", $name, $templateFile);

        file_put_contents($pTo, $templateFile);

        $this->info("Модель создана");
    }
}
