<?php


namespace MrProperter\Library;


use Illuminate\Support\Facades\Route;

class MrProperterRoute
{

    public static function routes()
    {
        Route::get('/example/mrproperter', [\MrProperter\Http\Controllers\PageMrProperterController::class, 'index']);
    }

}
