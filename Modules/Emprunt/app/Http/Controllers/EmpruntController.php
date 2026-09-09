<?php

namespace Modules\Emprunt\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmpruntController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('emprunt::emprunts.index');
    }

    public function myLoans(){
        return view('emprunt::emprunts.mes-emprunts');
    }

   
}
