<?php

namespace App\Http\Controllers\MrProperter;

use App\Http\Controllers\Controller;
use App\Models\Bot\History;
use App\Models\Bot\VirtualStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MrProperterController extends Controller
{
    public function index()
    {


        $model = VirtualStep::all()->first();

        return view('mrproperter::index', compact('model'));

    }

    public function store(Request $request)
    {

        $rules = History::GetValidateRules();
        $validator = Validator::make($request->toArray(), $rules);
        $validator->validate();

        if ($validator->fails()) {
            dd("X");
            //   return ResponseApi::Error($validator->errors()->first());
        }
        $shops = [];

        $model = History::all()->first();

        return redirect()->back();

    }

}
