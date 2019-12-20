<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacParticipatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_participates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('products', 100)->comment('商品id（逗号隔开）');
            $table->unsignedBigInteger('order_id')->nullable()->comment('订单id');

            $table->unsignedBigInteger('campaign_id')->comment('活动id');
            $table->unsignedBigInteger('user_id')->comment('用户id');
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
        Schema::dropIfExists('yac_participates');
    }
}
