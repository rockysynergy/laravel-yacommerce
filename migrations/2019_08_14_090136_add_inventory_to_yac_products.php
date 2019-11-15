<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInventoryToYacProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yac_products', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory')->default(0)->comment('库存');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yac_products', function (Blueprint $table) {
            $table->dropColumn('inventory');
        });
    }
}
