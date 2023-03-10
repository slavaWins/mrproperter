@php
    /** @var MigrationRender $fb */
    /** @var \App\Library\MrProperter\PropertyBuilderStructure[] $props */

use App\Library\MrProperter\MigrationRender;
@endphp
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class {{$className}} extends Migration
{


    public function up()
    {

@if(!$isModify)
Schema::create('{{$tableName}}', function(Blueprint $table) {
$table->id();
$table->timestamps();
@else
    Schema::table('{{$tableName}}', function(Blueprint $table) {
@endif

@foreach($list as $ind=> $data)

    $table @foreach( $data as $fun=> $arg)  ->{{$fun}}({{$arg}}) @endforeach;@endforeach

});

}
    public function down()
    {
@if(!$isModify)
    Schema::dropIfExists('{{$tableName}}');
@else
    Schema::table('{{$tableName}}', function (Blueprint $table) {
         $table->dropColumn([
    @foreach($list as $ind=> $data)
    '{{$ind}}',
    @endforeach
    ]);
    });
@endif
    }
}
