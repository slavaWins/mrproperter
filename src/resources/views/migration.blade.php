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

Schema::create('{{$tableName}}', function(Blueprint $table) {
$table->id();@foreach($list as $ind=> $data)

    $table @foreach( $data as $fun=> $arg)  ->{{$fun}}({{$arg}}) @endforeach;@endforeach

$table->timestamps();
});


    public function down()
    {
        Schema::dropIfExists('{{$tableName}}');
    }
}
