<?php

namespace Modules\Biblio\Http\Controllers;

use App\Http\Controllers\Controller;

class BookController extends Controller
{
    /**
     * Liste des livres (Livewire gère le CRUD).
     */
    public function index()
    {
        return view('biblio::books.index');
    }
}
