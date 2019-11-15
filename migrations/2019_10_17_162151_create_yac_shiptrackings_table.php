<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacShiptrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_shiptrackings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->comment('订单号');
            $table->string('shipnumber', 100)->comment('运单号');
            $table->string('carrier', 50)->nullable()->comment('快递公司');
            $table->tinyInteger('tracking_status')->default(0)->comment('监控状态');

            $table->string('tracking', 5000)->nullable()->comment('跟踪信息');
            $table->unsignedBigInteger('shipaddress_id')->comment('收货地址id');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建日期');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新日期');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yac_shiptrackings');
    }
}
