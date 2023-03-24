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
        Schema::create('spares', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->comment('予備材名');
            $table->integer('priority_flg')->nullable(false)->comment('優先材フラグ 1:優先');
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
        Schema::dropIfExists('spares');
    }
};
