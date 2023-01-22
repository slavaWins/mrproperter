<?php

namespace MrProperter\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use MrProperter\Library\MigrationRender;
use MrProperter\Models\MPModel;

class MakeMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp:migration {Model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать модель';

    public static function CodeFormater($f)
    {
        $text = "";
        $opens = 0;
        $emptyLines = 0;

        foreach (explode("\n", $f) as $V) {

            $V = trim($V);
            $opens = max(0, $opens);
            if (empty($V)) {
                $emptyLines++;
            } else {
                $emptyLines = 0;
            }
            if ($emptyLines > 1) continue;

            if (substr($V, -1) == "}") $opens -= 1;

            $text .= "\n";
            if ($opens > 0) $text .= str_repeat("    ", $opens);
            $text .= $V;

            if (substr($V, -1) == "{") $opens += 1;
        }
        $text = trim($text);
        return $text;
    }

    public function handle()
    {
        $name = $this->argument("Model");

        $pTo = MakeModel::GetModelPath($name);
        if (!file_exists($pTo)) return $this->error("Model not exist!");

        $cln = '\App\Models\\' . $name;

        /** @var MPModel $class */
        $class = new $cln();

        $info = MigrationRender::RenderMigration($class);


        $path = database_path() . '/migrations/' . $info['file'];

        $content = '<?php'."\n".$info['content'];
        $content = html_entity_decode($content);
        $content = self::CodeFormater($content);

        file_put_contents($path, $content);


        $this->info("Миграция создана " . $info['file']);
    }
}
