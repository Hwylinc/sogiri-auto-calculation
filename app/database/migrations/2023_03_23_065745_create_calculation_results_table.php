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
        Schema::create('calculation_results', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(false)->comment('計算番号');
            $table->integer('diameter_id')->nullable(false)->comment('鉄筋径ID');
            $table->integer('times')->nullable(false)->comment('切断順番');
            $table->integer('cutting_order')->nullable(false)->comment('切断順番');
            $table->integer('component_id')->nullable(false)->comment('部材ID');
            $table->integer('length')->nullable(false)->comment('長さ');
            $table->integer('set_number')->nullable(false)->comment('切断セット本数');
            $table->integer('port_id')->nullable(false)->comment('吐き出し口ID');
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
        Schema::dropIfExists('calculation_results');
    }
};
