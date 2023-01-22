<?php

namespace MrProperter\Console\Commands;

use MrProperter\Library\MrProperterHelper;
use MrProperter\Models\MrProperter;
use MrProperter\Models\MrProperterSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrproperter:example';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заготовка команды mrproperter';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {

        $this->info("mrproperter - Команда выполнена");
    }
}
