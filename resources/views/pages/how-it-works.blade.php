@extends('layouts.guest')

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-500 to-green-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">How It Works</h1>
            <p class="text-xl text-green-100">Finding your perfect dorm in 3 simple steps</p>
        </div>
    </div>

    <!-- For Students Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">For Students</h2>
            <p class="text-xl text-gray-600">Your journey to finding the perfect home</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- Step 1 -->
            <div class="text-center">
                <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl font-bold text-green-600">1</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">Browse</h3>
                <p class="text-gray-600 mb-4">
                    Search through hundreds of verified properties near PSU campuses. Filter by price, 
                    amenities, and location to find your perfect match.
                </p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <ul class="text-left text-sm text-gray-600 space-y-2">
                        <li>✓ Advanced search filters</li>
                        <li>✓ Interactive map view</li>
                        <li>✓ Real student reviews</li>
                        <li>✓ Photo galleries</li>
                    </ul>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="text-center">
                <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl font-bold text-green-600">2</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">Schedule</h3>
                <p class="text-gray-600 mb-4">
                    Book a viewing directly through the platform. Choose your preferred date and time, 
                    and get instant confirmation from the landlord.
                </p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <ul class="text-left text-sm text-gray-600 space-y-2">
                        <li>✓ Online scheduling</li>
                        <li>✓ Instant messaging</li>
                        <li>✓ Virtual tours available</li>
                        <li>✓ Flexible viewing times</li>
                    </ul>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="text-center">
                <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl font-bold text-green-600">3</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">Move In</h3>
                <p class="text-gray-600 mb-4">
                    Visit the property, complete the rental agreement, and move into your new home. 
                    We're here to support you every step of the way.
                </p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <ul class="text-left text-sm text-gray-600 space-y-2">
                        <li>✓ Secure contracts</li>
                        <li>✓ Move-in checklist</li>
                        <li>✓ 24/7 support</li>
                        <li>✓ Community guidelines</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- CTA for Students -->
        <div class="text-center">
            <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition inline-block">
                Search for Homes
            </a>
        </div>
    </section>

    <!-- For Landlords Section -->
    <section class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">For Landlords</h2>
                <p class="text-xl text-gray-600">Reach PSU students easily</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <!-- Step 1 -->
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="text-4xl mb-4">📝</div>
                    <h3 class="text-xl font-bold mb-3">1. Create Listing</h3>
                    <p class="text-gray-600 mb-4">
                        Register your property with photos, amenities, and pricing. Our verification team 
                        will review within 24-48 hours.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Free to list</li>
                        <li>• Photo upload tools</li>
                        <li>• Flexible pricing</li>
                    </ul>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="text-4xl mb-4">✓</div>
                    <h3 class="text-xl font-bold mb-3">2. Get Verified</h3>
                    <p class="text-gray-600 mb-4">
                        PSU administration verifies your property for safety and quality standards. 
                        This builds trust with students.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Safety inspection</li>
                        <li>• Document verification</li>
                        <li>• Quality assurance</li>
                    </ul>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="text-4xl mb-4">🤝</div>
                    <h3 class="text-xl font-bold mb-3">3. Connect</h3>
                    <p class="text-gray-600 mb-4">
                        Receive inquiries from interested students, schedule viewings, and manage bookings 
                        all in one place.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Direct messaging</li>
                        <li>• Booking management</li>
                        <li>• Analytics dashboard</li>
                    </ul>
                </div>
            </div>

            <!-- CTA for Landlords -->
            <div class="text-center">
                @auth
                    <a href="{{ route('landlord.properties.create') }}" class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition inline-block">
                        Start Listing for Free
                    </a>
                @else
                    <button onclick="openAuthModal('register')" class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition inline-block">
                        Get Started
                    </button>
                @endauth
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold text-center mb-12">Why Use PSU Dorm Finder?</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center">
                        <span class="text-xl">🛡️</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-2">Safe & Verified</h3>
                    <p class="text-gray-600">All properties verified by PSU administration for student safety</p>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center">
                        <span class="text-xl">💰</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-2">Transparent Pricing</h3>
                    <p class="text-gray-600">No hidden fees, clear pricing, and honest reviews from students</p>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center">
                        <span class="text-xl">📍</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-2">Near Campus</h3>
                    <p class="text-gray-600">Find properties within walking distance of your classes</p>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center">
                        <span class="text-xl">💬</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-2">Easy Communication</h3>
                    <p class="text-gray-600">Direct messaging between students and landlords</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="bg-gray-50 py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Frequently Asked Questions</h2>
            
            <div class="space-y-6">
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <h3 class="font-bold mb-2">Is it free to use?</h3>
                    <p class="text-gray-600">Yes! PSU Dorm Finder is completely free for students. Landlords can list properties for free as well.</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-md">
                    <h3 class="font-bold mb-2">How are properties verified?</h3>
                    <p class="text-gray-600">Every property undergoes verification by PSU administration to ensure safety, security, and quality standards are met.</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-md">
                    <h3 class="font-bold mb-2">Can I schedule virtual tours?</h3>
                    <p class="text-gray-600">Yes! Many landlords offer virtual tours. You can request this when booking a viewing.</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-md">
                    <h3 class="font-bold mb-2">What if I have issues with my rental?</h3>
                    <p class="text-gray-600">Our support team is available 24/7 to help resolve any issues. You can also contact PSU student services for assistance.</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection