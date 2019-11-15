<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacSeckillproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_seckillproducts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 100)->comment('商品名称');
            $table->string('cover_pic', 300)->nullable()->comment('头图地址');
            $table->string('description', 2000)->nullable()->comment('商品详情');
            $table->unsignedBigInteger('price')->default(0)->comment('商品价格');
            $table->string('pictures', 500)->nullable()->comment('商品图片');

            $table->unsignedBigInteger('inventory')->default(0)->comment('实时库存');
            $table->tinyInteger('status')->default(0)->comment('状态0:下架；1：上架');
            $table->unsignedBigInteger('category_id')->comment('类别id');

            $table->unsignedBigInteger('sk_price')->default(0)->comment('秒杀价格');
            $table->timestamp('end_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('结束时间');
            $table->timestamp('start_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('开始时间');
            $table->unsignedBigInteger('total')->comment('起始总库存');


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
        Schema::dropIfExists('yac_seckillproducts');
    }
}
