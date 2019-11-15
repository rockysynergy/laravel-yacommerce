<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 100)->comment('商品名称');
            $table->string('cover_pic', 300)->nullable()->comment('头图地址');
            $table->string('description', 2000)->nullable()->comment('商品详情');
            $table->unsignedBigInteger('price')->default(0)->comment('商品价格');
            $table->string('pictures', 500)->nullable()->comment('商品图片');

            $table->unsignedBigInteger('category_id')->comment('类别id');
            $table->foreign('category_id')->references('id')->on('yac_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yac_products');
    }
}
