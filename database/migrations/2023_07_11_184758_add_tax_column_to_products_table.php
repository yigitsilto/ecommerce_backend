<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxColumnToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->addColumn("integer", "tax")->nullable();
        });

        Schema::table('tax_classes', function (Blueprint $table) {
            $table->string('based_on')->change()->nullable();
        });

        Schema::table('tax_rates', function (Blueprint $table) {
            $table->string('country')->change()->nullable();
            $table->string('state')->change()->nullable();
            $table->string('city')->change()->nullable();
            $table->string('zip')->change()->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('tax');
        });
    }
}
