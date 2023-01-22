@php
    /** @var MigrationRender $fb */
    /** @var \App\Library\MrProperter\PropertyBuilderStructure[] $props */

use App\Library\MrProperter\MigrationRender;
@endphp



@foreach($list as $ind=> $data)

    $table @foreach( $data as $fun=> $arg)
        ->{{$fun}}({{$arg}})
    @endforeach;
@endforeach

