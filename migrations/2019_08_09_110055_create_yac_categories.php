<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 100)->comment('类别名称');
            $table->string('pic', 120)->nullable()->comment('类别图片');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('父类id');
            $table->foreign('parent_id')->references('id')->on('yac_categories');

            $table->unsignedBigInteger('shop_id')->comment('店铺id');
            $table->foreign('shop_id')->references('id')->on('yac_shops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yac_categories');
    }
}
