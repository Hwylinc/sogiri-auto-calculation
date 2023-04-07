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
        Schema::create('user_factories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable(false)->comment('ユーザーID');
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
        Schema::dropIfExists('user_factories');
    }
};
