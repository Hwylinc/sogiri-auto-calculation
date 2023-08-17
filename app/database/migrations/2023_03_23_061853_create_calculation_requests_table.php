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
        Schema::create('calculation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(false)->comment('計算依頼コード');
            $table->integer('length')->nullable(false)->comment('長さ');
            $table->integer('number')->nullable(false)->comment('本数');
            $table->integer('diameter_id')->nullable(false)->comment('鉄筋径ID');
            $table->integer('component_id')->nullable(false)->comment('部材ID');
            $table->integer('port_id')->nullable(false)->comment('吐き出し口ID');
            $table->integer('user_id')->nullable(false)->comment('登録者ID');
            $table->integer('display_order')->nullable(false)->comment('表示順');
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
        Schema::dropIfExists('calculation_requests');
    }
};
