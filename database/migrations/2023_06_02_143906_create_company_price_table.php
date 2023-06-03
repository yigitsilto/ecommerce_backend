<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_price', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->double("ratio")->default(0);
            $table->timestamps();
        });

        // make 4 insert seeder
        \Illuminate\Support\Facades\DB::table('company_price')->insert(
            array(
                'title' => 'Fiyat 1',
                'ratio' => '0',
            )
        );
        \Illuminate\Support\Facades\DB::table('company_price')->insert(
            array(
                'title' => 'Fiyat 2',
                'ratio' => '0',
            )
        );
        \Illuminate\Support\Facades\DB::table('company_price')->insert(
            array(
                'title' => 'Fiyat 3',
                'ratio' => '0',
            )
        );
        \Illuminate\Support\Facades\DB::table('company_price')->insert(
            array(
                'title' => 'Fiyat 4',
                'ratio' => '0',
            )
        );
        \Illuminate\Support\Facades\DB::table('company_price')->insert(
            array(
                'title' => 'Fiyat 5',
                'ratio' => '0',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_price');
    }
}
