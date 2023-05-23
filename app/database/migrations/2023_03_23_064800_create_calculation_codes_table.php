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
        Schema::create('calculation_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(false)->unique()->comment('計算依頼コード');
            $table->integer('client_id')->nullable(false)->comment('メーカーID');
            $table->string('house_name')->nullable(false)->comment('邸名');
            $table->integer('factory_id')->nullable(false)->comment('工場ID');
            $table->integer('calculation_status')->nullable(false)->comment('計算済みフラグ 1:計算済み 2:未計算');
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
        Schema::dropIfExists('calculation_codes');
    }
};
