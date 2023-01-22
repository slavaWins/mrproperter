@php
    /** @var FormBuilderStructure $fb */
    /** @var \App\Library\MrProperter\PropertyBuilderStructure $prop */

use App\Library\MrProperter\FormBuilderStructure;
@endphp

@if($fb->url)
    <form method="post" action="{{$fb->url}}">
        @endif

        @foreach($fb->row_list as $rowId=> $row)

            <div class="row">

                @foreach($row as $K=>$prop)
                    <div class="col">
                        @if($K=="submit")
                            <button type="submit" class="btn btn-outline-dark mt-4 float-end">{{$prop}}</button>
                        @else
                            {{$fb->model->BuildInput($K)}}
                        @endif

                    </div>
                @endforeach
            </div>
        @endforeach

        @if($fb->url)
    </form>
@endif
