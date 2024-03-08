<?php

    namespace MrProperter\Helpers;


    class FinderParts
    {


        public static function GetClassFullModel($name) {

            $fixStr = self::GetModelPath($name);


            if (strpos(strtolower($fixStr), "/app/models/") > -1) {
                return '\App\Models\\'.$name;
            }

            if(strpos($fixStr,"/app/")>-1) {
                $fixStr = explode("/app/", $fixStr);
                $fixStr = "App/".$fixStr[1];
                $clasPath = $fixStr;

                $clasPath = str_replace("/","\\", $clasPath);
                $clasPath = "\\".$clasPath;
                $clasPath = str_replace(".php","", $clasPath);

                return  $clasPath;
            }


            return '\App\\'.$name;
        }


        private static function GetModelPathInServices($name) {
            $servicesVariantas = ["Services", "Domain", "Domains"];

            foreach ($servicesVariantas as $serFoolder) {
                $services = app_path($serFoolder);
                if (!file_exists($services)) continue;

                foreach (scandir($services) as $K => $service) if ($K > 1) {
                    $serviceModelsFoolder = $services.'/'.$service.'/Models/'.$name.'.php';
                    if (!file_exists($serviceModelsFoolder)) continue;

                    return $serviceModelsFoolder;
                }
            }

            return null;
        }

        public static function FixPath($name) {
            $name = str_replace("\\", "/", $name);

            return $name;
        }

        public static function GetModelPath($name) {
            $modelsFoolder = app_path("Models");
            $modelsFoolder = $modelsFoolder.'/'.$name.'.php';


            if (!file_exists($modelsFoolder)) {
                $variant = self::GetModelPathInServices($name);

                if ($variant) return self::FixPath($variant);
            }

            if (!file_exists($modelsFoolder)) {
                $modelsFoolder = app_path("");
                $modelsFoolder = $modelsFoolder.'/'.$name.'.php';
            }

            return self::FixPath($modelsFoolder);
        }
    }


