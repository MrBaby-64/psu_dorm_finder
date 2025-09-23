@extends('layouts.account')

@section('content')
<div class="py-8">
    <h1 class="text-3xl font-bold mb-6">My Bookings</h1>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if($bookings->count() > 0)
    <div class="space-y-4">
        @foreach($bookings as $booking)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold">{{ $booking->property->title }}</h3>
                    <p class="text-gray-600 mt-1">{{ $booking->property->location_text }}</p>
                    
                    @if($booking->room)
                    <p class="text-sm text-gray-500 mt-1">Room: {{ $booking->room->name }}</p>
                    @endif

                    <div class="mt-3">
                        <span class="text-gray-700">Check-in: </span>
                        <span class="font-medium">{{ $booking->check_in->format('M d, Y') }}</span>
                        <span class="mx-2">→</span>
                        <span class="text-gray-700">Check-out: </span>
                        <span class="font-medium">{{ $booking->check_out->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="ml-6 text-right">
                    @if($booking->status === 'pending')
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">Pending</span>
                    @elseif($booking->status === 'approved')
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Approved</span>
                    @elseif($booking->status === 'rejected')
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">Rejected</span>
                    @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">Cancelled</span>
                    @endif

                    @if($booking->status === 'pending')
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Cancel this booking?')"
                                class="text-red-600 hover:text-red-800 text-sm">
                            Cancel Booking
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>

    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <h3 class="text-lg font-medium text-gray-900">No bookings yet</h3>
        <p class="mt-1 text-sm text-gray-500">Browse properties and make your first booking!</p>
        <div class="mt-6">
            <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block">
                Browse Properties
            </a>
        </div>
    </div>
    @endif
</div>
@endsection