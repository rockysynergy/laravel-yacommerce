<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShipTrackingIdToYacOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yac_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('shiptracking_id')->nullable()->comment('运单信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yac_orders', function (Blueprint $table) {
            $table->dropColumn('shiptracking_id');
        });
    }
}
