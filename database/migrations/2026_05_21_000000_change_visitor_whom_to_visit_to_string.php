<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $visitorNames = DB::table('visitors')
            ->join('users', 'visitors.whom_to_visit', '=', 'users.id')
            ->select('visitors.id', 'users.name')
            ->get();

        Schema::table('visitors', function (Blueprint $table) {
            $table->dropForeign(['whom_to_visit']);
            $table->string('whom_to_visit')->change();
        });

        foreach ($visitorNames as $visitorName) {
            DB::table('visitors')
                ->where('id', $visitorName->id)
                ->update(['whom_to_visit' => $visitorName->name]);
        }
    }

    public function down()
    {
        $fallbackUserId = DB::table('users')->value('id');
        $visitorUserIds = DB::table('visitors')
            ->leftJoin('users', 'visitors.whom_to_visit', '=', 'users.name')
            ->select('visitors.id', 'users.id as user_id')
            ->get();

        foreach ($visitorUserIds as $visitorUserId) {
            DB::table('visitors')
                ->where('id', $visitorUserId->id)
                ->update(['whom_to_visit' => $visitorUserId->user_id ?? $fallbackUserId]);
        }

        Schema::table('visitors', function (Blueprint $table) {
            $table->unsignedBigInteger('whom_to_visit')->change();
            $table->foreign('whom_to_visit')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
