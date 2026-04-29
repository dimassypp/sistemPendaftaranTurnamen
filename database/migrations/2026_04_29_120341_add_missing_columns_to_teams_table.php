<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->string('team_name');
            $table->string('logo')->nullable();
        });
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['tournament_id']);
            $table->dropColumn(['tournament_id', 'team_name', 'logo']);
        });
    }
};