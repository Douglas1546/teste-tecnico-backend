<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveBlacklistDataInclusao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::connection('email')->hasColumn('em_black_list', 'black_list_data_inclusao')) {
            Schema::connection('email')->table('em_black_list', function (Blueprint $table) {
                $table->dropColumn('black_list_data_inclusao');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::connection('email')->hasColumn('em_black_list', 'black_list_data_inclusao')) {
            Schema::connection('email')->table('em_black_list', function (Blueprint $table) {
                $table->timestamp('black_list_data_inclusao')->nullable();
            });
        }
    }
}
