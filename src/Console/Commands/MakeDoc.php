<?php

namespace MrProperter\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use MrProperter\Helpers\FinderParts;
use MrProperter\Library\MigrationRender;
use MrProperter\Models\MPModel;

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


    public static function IssetShema($f)
    {
        return strpos($f, "* @OA\Schema(") > 0;
    }

    public static function GetExistPrpoperys($f, $proplist, $nameClass)
    {

        $existProps = [];

        $getAllProperties = explode('@property', $f);
        foreach ($getAllProperties as $K => $V) if ($K > 0) {
            $line = explode("\n", $V)[0];
            $line = '@property' . $line;
            foreach ($proplist as $K => $prop) {
                if (strpos($line, $K) > 0) {
                    $existProps[$K] = true;
                }
            }
            if (strpos($V, "class " . $nameClass) > -1) break;
        }
        return $existProps;
    }


    public static function GetExistSchemePrpoperties($f, $proplist, $nameClass)
    {

        $existProps = [];

        $getAllProperties = explode('@OA\Property', $f);
        foreach ($getAllProperties as $K => $V) if ($K > 0) {
            $line = explode("),", $V)[0];
            $line = str_replace("\n", "", $line);
            $line = str_replace(" ", "", $line);
            foreach ($proplist as $K => $prop) {
                if (strpos($line, 'property="' . $K . '"') > 0) {
                    $existProps[$K] = true;
                }
            }
            if (strpos($V, "class " . $nameClass) > -1) break;
        }
        return $existProps;
    }


    public function handle()
    {
        $name = $this->argument("Model");

        $pTo = FinderParts::GetModelPath($name);
        if (!file_exists($pTo)) return $this->error("Model not exist!");


        $cln = FinderParts::GetClassFullModel($name);

        /** @var MPModel $model */
        $model = new $cln();


        $f = file_get_contents($pTo);


        $addingBottomClass = '';

        $fout = "";
        $needClassStart = strtolower("class" . $name . 'extends');


        $proplist = $model->GetProperties();

        $existProps = self::GetExistPrpoperys($f, $proplist, $name);
        $existPropsSheme = self::GetExistSchemePrpoperties($f, $proplist, $name);

        $addingPropertyCount = 0;

        $docPropertyBlock = "/**\n";


        foreach ($proplist as $K => $prop) {
            if ($prop->belongsMethod) {
                $title = 'public function ' . $prop->belongsMethod . '()';
                if (substr_count($f, $title) == 0) {
                    $addingBottomClass .= $title . '
    {
        return $this->belongsTo(' . basenames($prop->belongsToClass) . '::class, "' . $K . '");
    }';
                }
            }

            if (isset($existProps[$K])) continue;
            $addingPropertyCount++;
            $_type = MigrationRender::GetType($prop->typeData);
            if($_type=="text")$_type="string";
            if($_type=="timestamp")$_type="Carbon";
            $docPropertyBlock .= "\n * @property " . $_type;
            if (!$prop->default) $docPropertyBlock .= "|null ";
            $docPropertyBlock .= ' $' . $K . ' ' . ($prop->comment ?? $prop->label ?? $prop->descr ?? " ");

            if ($prop->belongsMethod) {
                $docPropertyBlock .= "\n * @property ".basenames($prop->belongsToClass).' '. $prop->belongsMethod ;
            }

        }
        $docPropertyBlock .= "\n*/";



        $docScheme = "";
        foreach ($proplist as $K => $prop) {
            if (isset($existPropsSheme[$K])) continue;
            $docScheme .= "\n " . '@OA\Property( property="' . $K . '", ';
            $docScheme .= "\n " . ' description="' . ($prop->comment ?? $prop->label ?? $prop->descr ?? "Без описания") . '"),';

        }
        $docScheme = trim($docScheme, '');
        // $docScheme = trim($docScheme,',');
        $docScheme = str_replace("\n", "\n *     ", $docScheme);

        foreach (explode("\n", $f) as $line) {


            if ($needClassStart) {

                $s = str_replace(" ", "", $line);
                $s = strtolower($s);
                $s = trim($s);


                if (strpos($line, "@OA\Schema(") > 0) {
                    $fout .= $line;
                    $line = "";
                    $fout .= $docScheme;
                }


                if($addingPropertyCount>0) {
                    if (strpos($s, $needClassStart) === 0) {
                        $fout .= "\n" . $docPropertyBlock . "\n";
                        $needClassStart = null;
                    }
                }


            }

            $fout .= $line . "\n";
        }

        $fout = trim($fout);

        if($addingBottomClass!="") {
            $fout = substr($fout, 0, strlen($fout)   - 1);
            $fout .= $addingBottomClass . "\n }";
        }

        $fout = str_replace("*/\n/**\n", "", $fout);
        $fout = str_replace("*/\n\n/**\n", "", $fout);
        $fout = str_replace("*/\n\n/**\n", "", $fout);
        $fout = preg_replace('/\*\/\s*\/\*\*/', '', $fout);

        file_put_contents($pTo, $fout);


        $this->info("Пхп док создан ");
    }
}
