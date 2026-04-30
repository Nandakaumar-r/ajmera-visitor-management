<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('purpose_of_visit', 255)->change();
        });
    }

    public function down()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('purpose_of_visit')->change();
        });
    }
};
