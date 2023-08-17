<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diameters', function (Blueprint $table) {
            $table->id();
            $table->integer('size')->comment('鉄筋径の値')->nullable(false);
            $table->integer('length')->comment('生材のデフォルトの長さ')->nullable(false);
            $table->integer('max_limit')->comment('同時切断可能数')->nullable(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diameters');
    }
};
