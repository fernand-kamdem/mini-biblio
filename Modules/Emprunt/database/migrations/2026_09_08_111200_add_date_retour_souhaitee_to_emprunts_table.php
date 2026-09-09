<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emprunts', function (Blueprint $table) {
            $table->date('date_retour_souhaitee')
                ->nullable()
                ->after('date_emprunt')
                ->comment('Date de retour souhaitée par le membre à la demande');
        });
    }

    public function down(): void
    {
        Schema::table('emprunts', function (Blueprint $table) {
            $table->dropColumn('date_retour_souhaitee');
        });
    }
};
