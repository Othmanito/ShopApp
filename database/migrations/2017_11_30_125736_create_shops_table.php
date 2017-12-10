<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateShopsTable extends Migration
{
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('ville');
            $table->string('adresse');
            $table->double('distance');
            $table->boolean('liked');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}
