<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJuklaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('juklaks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('contoh')->nullable();
            $table->longText('jawaban')->nullable();
            $table->longText('follow_up')->nullable();
            $table->longText('indikator')->nullable();
            $table->foreignId('juklak_category_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('juklaks');
    }
}
