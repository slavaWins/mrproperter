<?php

namespace MrProperter\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageMrProperterController extends Controller
{


    public function index()
    {
        return view('mrproperter::page');
    }
}
