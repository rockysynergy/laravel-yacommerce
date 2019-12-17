<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacCampaignProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_campaign_product', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->comment('产品id');
            $table->unsignedBigInteger('campaign_id')->comment('活动id');
            $table->unsignedBigInteger('campaign_price')->nullable()->comment('活动价格');

            $table->string('campaign_type')->nullable()->comment('活动类型');

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
        Schema::dropIfExists('yac_campaign_product');
    }
}
