<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacOrderitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_orderitems', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->comment('所属订单id');
            $table->foreign('order_id')->references('id')->on('yac_orders');

            $table->string('thumbnail', 200)->comment('缩略图地址');
            $table->string('title', 200)->comment('标题');
            $table->string('info', 200)->nullable()->comment('额外信息');
            $table->unsignedInteger('amount')->comment('购买数量');
            $table->bigInteger('unit_price')->comment('单价，以分为单位');
            $table->bigInteger('pay_amount')->comment('支付金额，以分为单位');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yac_orderitems');
    }
}
