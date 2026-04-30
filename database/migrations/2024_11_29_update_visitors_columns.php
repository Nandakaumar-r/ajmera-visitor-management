<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn('full_name');
            $table->dropColumn('contact_information');

            // Add new columns
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('email')->nullable()->after('last_name');
            $table->string('phone_number')->after('email');
        });
    }

    public function down()
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['first_name', 'last_name', 'email', 'phone_number']);

            // Restore old columns
            $table->string('full_name')->after('id');
            $table->string('contact_information')->after('full_name');
        });
    }
};
