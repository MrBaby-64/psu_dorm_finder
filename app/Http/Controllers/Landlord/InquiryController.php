<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index()
    {
        $inquiries = Inquiry::whereHas('property', function($query) {
            $query->where('user_id', auth()->id());
        })
        ->with(['user', 'property'])
        ->latest()
        ->paginate(20);

        return view('landlord.inquiries.index', compact('inquiries'));
    }
}