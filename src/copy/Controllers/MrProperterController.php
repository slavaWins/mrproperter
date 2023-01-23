<?php

namespace App\Http\Controllers\MrProperter;

use App\Http\Controllers\Controller;
use App\Models\Bot\History;
use App\Models\Bot\VirtualStep;
use App\Models\ExampleMPModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MrProperterController extends Controller
{
    public function index()
    {


        $model = ExampleMPModel::all()->first();
        //$model = Suka::find(2);
        if(!$model)$model = new ExampleMPModel();

        return view('mrproperter.index', compact('model'));

    }

    public function store(Request $request)
    {

        $rules = ExampleMPModel::GetValidateRules();
        $validator = Validator::make($request->toArray(), $rules);
        $validator->validate();

        if ($validator->fails()) {
            dd($validator->errors()->first());
        }
        $shops = [];

        return redirect()->back();

    }

}
