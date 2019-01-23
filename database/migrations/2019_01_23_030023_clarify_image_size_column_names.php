<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClarifyImageSizeColumnNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logos', function (Blueprint $table) {
            $table->renameColumn('small', 'icon');
            $table->renameColumn('large', 'thumbnail');
            $table->string('standard')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logos', function (Blueprint $table) {
            $table->renameColumn('icon', 'small');
            $table->renameColumn('thumbnail', 'large');
            $table->dropColumn('standard');
        });
    }
}
