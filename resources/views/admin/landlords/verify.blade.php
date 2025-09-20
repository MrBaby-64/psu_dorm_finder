{{-- View for admins to verify landlord IDs --}}
<!DOCTYPE html>
<html>
<head>
    <title>Verify Landlords - Admin</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50">
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Landlord ID Verification</h1>

        @foreach($landlords->where('id_verification_status', 'pending') as $landlord)
        <div class="bg-white rounded-lg shadow p-6 mb-4">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-lg">{{ $landlord->name }}</h3>
                    <p class="text-gray-600">{{ $landlord->email }}</p>
                    <p class="text-sm text-gray-500">Phone: {{ $landlord->phone }}</p>
                </div>
                <div>
                    @if($landlord->valid_id_path)
                    <img src="{{ asset('storage/' . $landlord->valid_id_path) }}" 
                         class="max-h-64 rounded">
                    @endif
                </div>
            </div>
            <div class="mt-4 flex gap-3">
                <form action="{{ route('admin.landlords.approve-id', $landlord) }}" method="POST">
                    @csrf
                    <button class="bg-green-600 text-white px-6 py-2 rounded">Approve</button>
                </form>
                <form action="{{ route('admin.landlords.reject-id', $landlord) }}" method="POST">
                    @csrf
                    <button class="bg-red-600 text-white px-6 py-2 rounded">Reject</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>