<?php

namespace Modules\Biblio\Http\Controllers;

use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Liste des catégories (Livewire gère le CRUD).
     */
    public function index()
    {
        return view('biblio::categories.index');
    }
}
