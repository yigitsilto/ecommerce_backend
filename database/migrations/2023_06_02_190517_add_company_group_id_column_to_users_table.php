<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyGroupIdColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('company_group_id')
                  ->default(1);
        });

        // update all users company_group_id to 1

        \Illuminate\Support\Facades\DB::table('users')
                                      ->update(
                                          array(
                                              'company_group_id' => '1',
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('company_group_id');
        });
    }
}
