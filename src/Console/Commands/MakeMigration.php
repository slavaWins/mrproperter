<?php

namespace MrProperter\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use MrProperter\Helpers\FinderParts;
use MrProperter\Library\MigrationRender;
use MrProperter\Models\MPModel;

class MakeMigration extends Command {
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
    protected $description = 'Создать миграцию для модели';

    public static function CodeFormater( $f ) {
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

            if (substr($V, -1) == "}" || substr($V, -3) == '});') $opens -= 1;

            $text .= "\n";
            if ($opens > 0) $text .= str_repeat("    ", $opens);
            $text .= $V;

            if (substr($V, -1) == "{") $opens += 1;
        }
        $text = trim($text);
        return $text;
    }


    public static function FixView( $content ) {
        $content = html_entity_decode($content);
        $content = self::CodeFormater($content);
        $content = str_replace("   ->", '->', $content);
        return $content;
    }

    public function handle() {
        $name = $this->argument("Model");

        $pTo = FinderParts::GetModelPath($name);
        if (!file_exists($pTo)) return $this->error("Model not exist!");


        $cln = FinderParts::GetClassFullModel($name);

        /** @var MPModel $class */
        $class = new $cln();
        $tableName = $class->getTable();


        $keys = Schema::getConnection()->getSchemaBuilder()->getColumnListing($tableName);
        $keyList = [];
        foreach ($keys as $V) $keyList[$V] = true;

        $info = MigrationRender::RenderMigration($class, $keyList, !empty($keys));


        if (!$info['content']) return $this->error("Not new collums in model");


        $path = database_path() . '/migrations/' . $info['file'];

        $content = '<?php' . "\n" . $info['content'];
        $content = self::FixView($content);


        file_put_contents($path, $content);


        $this->info("Миграция создана " . $info['file']);
    }
}
