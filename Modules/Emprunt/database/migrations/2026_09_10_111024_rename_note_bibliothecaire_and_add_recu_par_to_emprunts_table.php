<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emprunts', function (Blueprint $table) {
            $table->foreignId('recu_par')
                ->after('note_bibliothecaire')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            
            $table->renameColumn('note_bibliotécaire', 'note_bibliothecaire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emprunts', function (Blueprint $table) {
            $table->dropForeign(['recu_par']);
            $table->dropColumn('recu_par');
            $table->renameColumn('note_bibliothecaire', 'note_bibliotécaire');
        });
    }
};