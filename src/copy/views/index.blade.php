@php
    /** @var \App\Models\Bot\VirtualStep $model */

use MrProperter\Library\FormBuilderStructure;
@endphp

@extends('layouts.containerscreen')

@section('content')

    <style>
        body {
            font-size: 12px;
            color: #000;
        }
    </style>
    <div class="  row">
        <div class="  col-8 ">
            <small>FormBuilderStructure</small>
            <h3>Сборка формы по своей структуре</h3>


            <div class="card  mb-4">
                <div class="card-body">
                    @php

                        FormBuilderStructure::New($model)
                        ->Input("selector_character_enabled")
                        ->Row()
                        ->Input("selector_character")
                        ->Input("selector_character_filter")
                        ->Input("selector_character_to_varible")
                        ->Row()
                        ->Input("render_character_enabled")
                        ->Row()
                        ->Input("render_character")
                        ->Row()
                        ->Submit("Отправить")
                        ->Route(route('mrproperter.store'))
                          ->RenderHtml();


                    @endphp
                </div>
            </div>
        </div>

        <div class="  col-4 ">
            <small>BuildInputAll</small>
            <h3>Вывод инпутов</h3>
            <div class="card  mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('mrproperter.store') }}">
                        @csrf
                        {{  $model->BuildInputAll()}}
                        <button type="submit" class="mt-4 btn btn-primary col-12 p-3 shadow-0 btn-submit-auth">
                            Вход
                        </button>
                    </form>
                </div>
            </div>
        </div>


        <div class="  col-6 ">
            <small>MigrationRender</small>
            <h3>Генерация файла миграции</h3>
            <div class="card  mb-6">
                <div class="card-body" style="font-size: 15px; font-family: Arial;">

                    {!!  \MrProperter\Library\MigrationRender::RenderMigration($model) !!}

                </div>
            </div>
        </div>


        <div class="  col-6 ">
            <small>DocRender</small>
            <h3>Генерация php doc</h3>
            <div class="card  mb-4">
                <div class="card-body" style="font-size: 15px; font-family: Arial;">

                    {!!  nl2br( \MrProperter\Library\MigrationRender::RenderDoc($model) )!!}

                </div>
            </div>
        </div>

        <div class="  col-6 ">
            <small>Validate Rules</small>
            <h3>Валидация модели</h3>
            <div class="card  mb-4">
                <div class="card-body" style="font-size: 15px; font-family: Arial;">

                    @php
                        $cl = get_class($model);
                        $ar = $cl::GetValidateRules();
                          echo  nl2br( print_r($ar, true));
                    @endphp

                </div>
            </div>
        </div>

    </div>
@endsection
