<?php

namespace Modules\Biblio\Http\Controllers;

use App\Http\Controllers\Controller;

class AuthorController extends Controller
{
    /**
     * Liste des auteurs (Livewire gère le CRUD).
     */
    public function index()
    {
        return view('biblio::authors.index');
    }
}
