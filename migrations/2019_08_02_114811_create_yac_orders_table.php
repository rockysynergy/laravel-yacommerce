<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_orders', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('订单id');
            $table->bigInteger('pay_amount')->default(0)->comment('支付金额，以分为单位');
            $table->tinyInteger('pay_status')->default(1)->comment('支付状态。1：等待支付；2：完成支付');
            $table->string('order_number',21)->comment('订单号')->unique();
            $table->string('exorder_number',30)->nullable()->comment('外部订单号');
            $table->tinyInteger('pay_method')->comment('支付方式。1：微信支付；2：现金支付；3：支付宝支付.');

            $table->unsignedBigInteger('user_id')->comment('订单所属用户id');

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
        Schema::dropIfExists('yac_orders');
    }
}
