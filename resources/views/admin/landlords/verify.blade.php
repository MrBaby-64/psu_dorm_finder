@extends('layouts.account')

@section('content')
<div class="py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Landlord Document Verification</h1>

        <div class="flex gap-2">
            @php
                $pendingCount = $landlords->where('document_verification_status', 'pending')->count();
            @endphp
            <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm">
                {{ $pendingCount }} Pending Verification
            </span>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <div class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                @if(session('error'))
                    <div class="text-sm font-medium text-red-800">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        <div class="text-sm font-medium text-red-800">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    <div class="space-y-6">
        @if($landlords->where('document_verification_status', 'pending')->count() > 0)
            @foreach($landlords->where('document_verification_status', 'pending') as $landlord)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $landlord->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $landlord->email }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pending Verification
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid lg:grid-cols-2 gap-8">
                        {{-- Landlord Information --}}
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Contact Information</label>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $landlord->email }}
                                    </div>
                                    @if($landlord->phone)
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        {{ $landlord->phone }}
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700">Registration Date</label>
                                <div class="mt-1 text-sm text-gray-600">
                                    {{ $landlord->created_at->format('M d, Y g:i A') }}
                                    <span class="text-gray-400">({{ $landlord->created_at->diffForHumans() }})</span>
                                </div>
                            </div>
                        </div>

                        {{-- Property Documents --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-3 block">Property Ownership Documents/Permits</label>
                            @if($landlord->property_documents_path)
                                <div class="border-2 border-gray-200 rounded-lg p-4">
                                    <img src="{{ asset('storage/' . $landlord->property_documents_path) }}"
                                         alt="Property documents for {{ $landlord->name }}"
                                         class="w-full h-auto max-h-80 object-contain rounded-lg shadow-sm cursor-pointer"
                                         onclick="window.open(this.src, '_blank')">
                                    <p class="text-xs text-gray-500 mt-2 text-center">Click image to view full size</p>
                                </div>
                            @else
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500">No property documents uploaded</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <form action="{{ route('admin.landlords.reject-id', $landlord) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to reject {{ $landlord->name }}\'s property documents? This action will notify the landlord.')"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Reject Documents
                                </button>
                            </form>

                            <form action="{{ route('admin.landlords.approve-id', $landlord) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to approve {{ $landlord->name }}\'s property ownership documents? This will verify them as a legitimate property owner.')"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Approve Documents
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">All Caught Up!</h3>
                <p class="text-sm text-gray-500 max-w-sm mx-auto">
                    There are no pending landlord document verifications at this time. New verification requests will appear here.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
// Auto-dismiss success messages after 5 seconds with smooth fade out
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.bg-green-50');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.3s ease-in-out';
            successAlert.style.opacity = '0';
            setTimeout(() => {
                if (successAlert && successAlert.parentNode) {
                    successAlert.parentNode.removeChild(successAlert);
                }
            }, 300);
        }, 5000);
    }

    const errorAlert = document.querySelector('.bg-red-50');
    if (errorAlert) {
        setTimeout(() => {
            errorAlert.style.transition = 'opacity 0.3s ease-in-out';
            errorAlert.style.opacity = '0';
            setTimeout(() => {
                if (errorAlert && errorAlert.parentNode) {
                    errorAlert.parentNode.removeChild(errorAlert);
                }
            }, 300);
        }, 8000);
    }
});
</script>
@endsection