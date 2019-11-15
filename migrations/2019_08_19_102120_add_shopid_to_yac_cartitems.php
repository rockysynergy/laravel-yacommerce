<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShopidToYacCartitems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yac_cartitems', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->comment('记录所属的商店id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yac_cartitems', function (Blueprint $table) {
            $table->dropColumn('shop_id');
        });
    }
}
