@extends('layouts.account')

@section('content')
<div class="py-8">
    <h1 class="text-3xl font-bold mb-6">My Favorites</h1>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if($favorites->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($favorites as $favorite)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
            <a href="{{ route('properties.show', $favorite->property) }}">
                <div class="h-48 bg-gray-300 rounded-t-lg flex items-center justify-center">
                    <span class="text-gray-500">Property Image</span>
                </div>
                
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-2">{{ $favorite->property->title }}</h3>
                    <p class="text-gray-600 text-sm mb-3">{{ $favorite->property->location_text }}</p>
                    
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl font-bold text-green-600">₱{{ number_format($favorite->property->price, 0) }}</span>
                        <span class="text-sm text-gray-500">/month</span>
                    </div>
                </div>
            </a>
            
            <div class="px-4 pb-4">
                <form action="{{ route('favorites.destroy', $favorite->property) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full border border-red-300 text-red-600 py-2 rounded-lg hover:bg-red-50">
                        Remove from Favorites
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $favorites->links() }}
    </div>

    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <h3 class="text-lg font-medium text-gray-900">No favorites yet</h3>
        <p class="mt-1 text-sm text-gray-500">Start browsing properties and add your favorites!</p>
        <div class="mt-6">
            <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block">
                Browse Properties
            </a>
        </div>
    </div>
    @endif
</div>
@endsection