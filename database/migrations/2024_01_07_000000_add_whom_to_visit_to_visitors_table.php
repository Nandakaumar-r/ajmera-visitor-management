<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('visitors', 'whom_to_visit')) {
            Schema::table('visitors', function (Blueprint $table) {
                // Add column without foreign key first
                $table->unsignedBigInteger('whom_to_visit')->after('purpose_of_visit')->nullable();
            });

            // Update existing records to set a default value if needed
            DB::table('visitors')->whereNull('whom_to_visit')->update([
                'whom_to_visit' => DB::table('users')->where('status', 'active')->value('id')
            ]);

            Schema::table('visitors', function (Blueprint $table) {
                // Make the column required and add foreign key
                $table->unsignedBigInteger('whom_to_visit')->nullable(false)->change();
                $table->foreign('whom_to_visit')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('visitors', 'whom_to_visit')) {
            Schema::table('visitors', function (Blueprint $table) {
                $table->dropForeign(['whom_to_visit']);
                $table->dropColumn('whom_to_visit');
            });
        }
    }
};
