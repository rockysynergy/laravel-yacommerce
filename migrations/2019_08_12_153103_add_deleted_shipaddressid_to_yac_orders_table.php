<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedShipaddressidToYacOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yac_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('shipaddress_id')->nullable()->comment('收货地址id');
            $table->foreign('shipaddress_id')->references('id')->on('yac_shipaddresses');

            $table->tinyInteger('deleted')->default(0)->comment('删除标志1：删除；2：不删除');
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
            $table->dropColumn('deleted');
            $table->dropForeign('yac_orders_shipaddress_id');
            $table->dropColumn('shipaddress_id');
        });
    }
}
