<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacSeckillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_seckills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('start_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('开始日期');
            $table->timestamp('end_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('结束日期');

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
        Schema::dropIfExists('yac_seckills');
    }
}
