<?php

namespace MrProperter\Console\Commands;

use Illuminate\Console\Command;

class MakeAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp:all {Model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать php doc и миграцию';


    public function handle()
    {
        $name = $this->argument("Model");

        $this->call('mrp:doc', ['Model' => $name]);
        $this->call('mrp:migration', ['Model' => $name]);

        $this->info("Процесс выполнен ");
    }
}
