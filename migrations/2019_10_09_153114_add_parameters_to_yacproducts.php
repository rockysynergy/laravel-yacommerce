<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParametersToYacproducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yac_products', function (Blueprint $table) {
            $table->unsignedBigInteger('show_price')->default(0)->comment('显示价格');
            $table->string('parameters', 3000)->comment('商品参数');
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
            $table->dropColumn('show_price');
            $table->dropColumn('parameters');
        });
    }
}
