<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacShipaddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_shipaddresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('所说用户id');
            $table->string('name', 20)->comment('收件人称呼');
            $table->string('mobile', 11)->comment('手机号码');
            $table->string('address', 255)->comment('收货地址');
            $table->tinyInteger('is_default')->default(0)->comment('是否设为默认1：是0:否');
            $table->string('tab', 20)->nullable()->comment('标签');
            $table->tinyInteger('deleted')->default(0)->comment('是否删除1：是0:否');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yac_shipaddresses');
    }
}
