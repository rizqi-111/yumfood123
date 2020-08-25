<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DetailOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('detail_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('detail_id');
            $table->unsignedBigInteger('order_id');
            $table->timestamps();

            $table->foreign('detail_id')->references('id')->on('details');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('detail_order');
    }
}
