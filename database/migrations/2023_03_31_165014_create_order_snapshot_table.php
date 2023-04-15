<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSnapshotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_snapshot', function (Blueprint $table) {
            $table->id();
            $table->json("order");
            $table->integer('user_id');
            $table->decimal('totalPrice', 18, 4)->unsigned()->nullable();
            $table->integer('installment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_snapshot');
    }
}
