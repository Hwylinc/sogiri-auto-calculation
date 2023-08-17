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
        Schema::create('calculation_group_calculation_code', function (Blueprint $table) {
            $table->id();
            $table->string('group_code')->nullable(false)->comment('計算グループコード');
            $table->string('code')->nullable(false)->comment('計算依頼コード');
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
        Schema::dropIfExists('calculation_group_calculation_code');
    }
};
