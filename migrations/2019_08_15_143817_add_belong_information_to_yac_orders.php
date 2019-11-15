<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBelongInformationToYacOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yac_orders', function (Blueprint $table) {
           $table->string('ptype', 15)->comment('订单所属实体类型(应用、商城等）');
           $table->unsignedBigInteger('pid')->comment(('订单所属实体id'));
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
           $table->dropColumn('ptype');
           $table->dropColumn('pid');
        });
    }
}
