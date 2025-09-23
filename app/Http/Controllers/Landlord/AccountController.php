<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;  // ADD THIS

class AccountController extends Controller
{
    public function index(): View
    {
        return view('landlord.account.index');
    }
}