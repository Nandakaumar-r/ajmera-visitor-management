<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('visiting_card_path')->nullable();
            $table->text('visiting_card_ocr_text')->nullable();
            $table->json('visiting_card_data')->nullable();
        });
    }

    public function down()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['visiting_card_path', 'visiting_card_ocr_text', 'visiting_card_data']);
        });
    }
};
