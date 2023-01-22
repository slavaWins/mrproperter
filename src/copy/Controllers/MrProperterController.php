<?php

    namespace App\Http\Controllers\MrProperter;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;

    class MrProperterController extends Controller
    {


        public function index() {


            return view('mrproperter.example');

        }
    }
