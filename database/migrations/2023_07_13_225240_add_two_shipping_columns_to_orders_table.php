<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoShippingColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string("cargo_no")->nullable();
            $table->string("cargo_key")->nullable();
            $table->string("cargo_status")->nullable();
            $table->string("cargo_url")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn("cargo_no");
            $table->dropColumn("cargo_key");
            $table->dropColumn("cargo_status");
            $table->dropColumn("cargo_url");
        });
    }
}
