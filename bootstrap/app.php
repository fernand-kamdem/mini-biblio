<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Middleware\IsActive;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Enregistre des alias pour les middlewares personnalisés.
    // Cela permet ensuite de les utiliser facilement dans les routes,
    // par exemple : ->middleware('permission') ou ->middleware('role').
    ->withMiddleware(function (Middleware $middleware): void {

    // Définition des alias des middlewares d'autorisation.
    // 'permission' permet de vérifier les permissions d'un utilisateur.
    // 'role' permet de vérifier le rôle d'un utilisateur.
    $middleware->alias([
        'permission' => PermissionMiddleware::class,
        'role' => RoleMiddleware::class,
    ]);

    // Ajoute le middleware IsActive à toutes les routes
    // appartenant au groupe de middleware 'web'.
    // Il permettra notamment de vérifier que l'utilisateur
    // est actif avant de poursuivre la requête.
    $middleware->appendToGroup('web', [
        IsActive::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
