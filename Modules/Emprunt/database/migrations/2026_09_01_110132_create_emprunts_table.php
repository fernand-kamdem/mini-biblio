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
        Schema::create('emprunts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->restrictOnDelete();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->enum('statut', ['brouillon', 'en_attente', 'approuve', 'refuse', 'rendu'])->default('brouillon');
            $table->string('workflow_state')->default('brouillon');
            $table->date('date_emprunt')->nullable();
            $table->date('date_retour_prevue')->nullable();
            $table->date('date_retour_reelle')->nullable();
            $table->foreignId('approuve_par')->nullable()->constrained('users')->nullOnDelete()->comment("id de l'utilisateur ayant approuvé l'emprunt")->nullable();
            $table->text('note_membre')->nullable();
            $table->text('note_bibliotécaire')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emprunts');
    }
};
