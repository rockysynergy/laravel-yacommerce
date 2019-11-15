<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacCartitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_cartitems', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->comment('产品id');
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->unsignedBigInteger('amount')->default(1)->comment('数量');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('添加时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yac_cartitems');
    }
}
