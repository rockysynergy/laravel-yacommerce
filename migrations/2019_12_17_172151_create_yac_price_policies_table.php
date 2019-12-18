<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacPricePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_price_policies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 100)->comment('价格策略类型');
            $table->string('parameters', 500)->comment('价格策略参数');

            $table->unsignedBigInteger('campaign_id')->nullable()->comment('活动id');
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
        Schema::dropIfExists('yac_price_policies');
    }
}
