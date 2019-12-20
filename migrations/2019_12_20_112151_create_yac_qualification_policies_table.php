<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYacQualificationPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yac_qualification_policies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('strategy', 150)->comment('资格政策');
            $table->string('parameters', 500)->comment('资格政策参数');

            $table->unsignedBigInteger('campaign_id')->nullable()->comment('活动id');
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
        Schema::dropIfExists('yac_qualification_policies');
    }
}
