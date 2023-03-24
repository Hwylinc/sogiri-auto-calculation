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
        Schema::create('port_factories', function (Blueprint $table) {
            $table->id();
            $table->integer('port_id')->nullable(false)->comment('吐き出し口ID');
            $table->integer('factory_id')->nullable(false)->comment('工場ID');
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
        Schema::dropIfExists('port_factories');
    }
};
