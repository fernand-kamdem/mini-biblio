<?php

namespace Modules\Emprunt\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
