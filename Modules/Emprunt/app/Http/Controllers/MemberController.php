<?php

namespace Modules\Emprunt\Http\Controllers;

use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('emprunt::members.index');
    }
}
