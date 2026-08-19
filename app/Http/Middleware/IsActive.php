<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Refuse l'accès aux comptes désactivés (is_active = false).
 *
 * Les visiteurs non connectés ne sont pas concernés.
 * Si is_active est null (attribut absent, ex. factory / actingAs),
 * on considère le compte actif pour ne pas couper la session par erreur.
 */
class IsActive
{
    /**
     * Déconnecte l'utilisateur et régénère la session (jeton CSRF inclus).
     */
    protected function logout(): void
    {
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();
    }

    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pas connecté → on laisse passer (login, register, etc.).
        if (! Auth::check()) {
            return $next($request);
        }

        // 2. Compte actif, ou is_active non défini (null) → on continue.
        //    `false` seul déclenche le blocage. `?? true` évite que null = désactivé.
        if (Auth::user()->is_active ?? true) {
            return $next($request);
        }

        // 3. Compte explicitement désactivé.
        $this->logout();

        return redirect()
            ->route('login')
            ->with('error', 'Votre compte est désactivé. Contactez un administrateur.');
    }
}