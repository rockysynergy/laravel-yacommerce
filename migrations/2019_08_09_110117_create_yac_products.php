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
            $table->string('title', 100)->nullable()->comment('商品名称');
            $table->string('cover_pic', 300)->nullable()->comment('头图地址');
            $table->string('description', 20000)->nullable()->comment('商品详情');
            $table->unsignedBigInteger('price')->comment('商品价格');
            $table->unsignedBigInteger('show_price')->nullable()->comment('商品价格');
            $table->string('pictures', 500)->nullable()->comment('商品图片');

            $table->unsignedBigInteger('category_id')->comment('类别id');
            $table->unsignedBigInteger('inventory')->comment('库存');
            $table->tinyInteger('status')->comment('状态0:下架；1：上架');
            $table->string('parameters', 3000)->nullable()->comment('商品参数');
            $table->string('model', 300)->nullable()->comment('型号');
            $table->string('variants_id', 70)->nullable()->comment('子规格产品的id, 逗号隔开');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('规格产品的父id');
            $table->foreign('category_id')->references('id')->on('yac_categories');

            $table->softDeletes();
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
        Schema::dropIfExists('yac_products');
    }
}
