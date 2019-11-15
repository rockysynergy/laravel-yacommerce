<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacProductVariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model', 100)->nullable()->comment('型号');
            $table->string('parameters', 3000)->nullable()->comment('参数');
            $table->unsignedBigInteger('price')->default(0)->comment('商品价格');

            $table->unsignedBigInteger('show_price')->default(0)->comment('显示价格');
            $table->unsignedBigInteger('inventory')->default(0)->comment('库存');
            $table->tinyInteger('status')->default(0)->comment('状态。0：下架1：上架');
            $table->string('pictures', 500)->nullable()->comment('图片地址');
            $table->unsignedBigInteger('product_id')->comment('所属产品id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yac_product_variants');
    }
}
