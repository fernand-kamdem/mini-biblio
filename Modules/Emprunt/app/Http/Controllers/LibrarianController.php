<?php

namespace Modules\Emprunt\Http\Controllers;

use App\Http\Controllers\Controller;

class LibrarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('emprunt::emprunts.librarians');
    }
}
