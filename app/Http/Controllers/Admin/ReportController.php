<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Message;

class ReportController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $stats = [
            'total_properties' => Property::count(),
            'total_bookings' => Booking::count(),
            'total_messages' => Message::count(),
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function export()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        // Simple CSV export
        $filename = 'psu-dorm-report-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Property', 'Landlord', 'City', 'Price', 'Status', 'Bookings']);

            Property::with(['landlord', 'bookings'])->get()->each(function($property) use ($file) {
                fputcsv($file, [
                    $property->title,
                    $property->landlord->name,
                    $property->city,
                    $property->price,
                    $property->approval_status,
                    $property->bookings->count()
                ]);
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}