<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToYacProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yac_products', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->comment('状态0:下架；1：上架');
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
            $table->dropColumn('status');
        });
    }
}
