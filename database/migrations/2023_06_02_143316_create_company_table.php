<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('company_price_id')
                  ->unsigned();
            $table->timestamps();
        });

        // add one seeder create to company table
        \Illuminate\Support\Facades\DB::table('company')
                                      ->insert(
                                          array(
                                              'title' => 'Kullanıcı',
                                              'company_price_id' => '1',
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
        Schema::dropIfExists('company');
    }
}
