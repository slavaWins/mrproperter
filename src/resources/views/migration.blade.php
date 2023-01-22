@php
    /** @var MigrationRender $fb */
    /** @var \App\Library\MrProperter\PropertyBuilderStructure[] $props */

use App\Library\MrProperter\MigrationRender;
@endphp

Schema::create('{{$tableName}}', function(Blueprint $table) {
$table->id();@foreach($list as $ind=> $data)

    $table @foreach( $data as $fun=> $arg)  ->{{$fun}}({{$arg}}) @endforeach;@endforeach

$table->timestamps();
});
