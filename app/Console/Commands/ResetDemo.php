<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Remet la démonstration publique dans son état de départ.
 * Planifiée chaque nuit (routes/console.php) ; ne fait rien hors du mode démonstration.
 */
#[Signature('demo:reset {--force : Remettre à zéro sans demander de confirmation}')]
#[Description('Remet la démonstration à zéro : base vidée puis catalogue, comptes et emprunts fictifs rechargés')]
class ResetDemo extends Command
{
    public function handle(): int
    {
        if (! config('demo.enabled')) {
            $this->components->error('Mode démonstration désactivé (DEMO_MODE=false) : aucune donnée n’a été touchée.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('Toutes les données seront remplacées par celles de la démonstration. Continuer ?')) {
            return self::FAILURE;
        }

        $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);

        $this->components->info('Démonstration remise à zéro.');

        return self::SUCCESS;
    }
}
