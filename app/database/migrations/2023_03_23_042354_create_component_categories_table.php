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
// おそらく不要
        Schema::create('component_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->comment('部材名');
            $table->integer('component_id')->nullable(false)->comment('紐付ける部材のID');
            $table->string('external_component_category_id')->nullable(false)->comment('外部部材カテゴリID');
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
        Schema::dropIfExists('component_categories');
    }
};
