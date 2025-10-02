@extends('layouts.guest')

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-500 to-green-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">About PSU Dorm Finder</h1>
            <p class="text-xl text-green-100">Making student housing search easier for PSU students</p>
        </div>
    </div>

    <!-- Inspirational Quote Section -->
    <section class="bg-gradient-to-r from-green-50 to-green-100 py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <svg class="w-16 h-16 text-green-600 mx-auto mb-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                </svg>
                <blockquote class="text-3xl md:text-4xl font-serif text-gray-800 italic mb-8 leading-relaxed">
                    "From a simple idea to a bridge connecting students with their home away from home."
                </blockquote>
                <p class="text-xl text-gray-700 leading-relaxed max-w-3xl mx-auto">
                    What began as a Laravel learning project evolved into a comprehensive platform dedicated to solving a real challenge
                    faced by PSU students: finding safe, affordable, and convenient housing near campus.
                </p>
            </div>
        </div>
    </section>

    <!-- What is PSU Dorm Finder -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold mb-6">What is PSU Dorm Finder?</h2>
                <p class="text-gray-600 mb-4 text-lg">
                    PSU Dorm Finder is a dedicated platform connecting Pampanga State University students
                    with verified, safe, and affordable housing options near campus.
                </p>
                <p class="text-gray-600 mb-4 text-lg">
                    We understand the challenges students face when looking for accommodation - from safety
                    concerns to budget constraints. That's why we created a platform specifically for the PSU community.
                </p>
                <p class="text-gray-600 text-lg">
                    Our mission is simple: <strong>Make renting better, for everyone.</strong>
                </p>
            </div>
            <div class="bg-green-50 rounded-lg p-8 text-center">
                <div class="text-6xl mb-4">🏠</div>
                <h3 class="text-2xl font-bold text-green-600 mb-2">Your Home Away From Home</h3>
                <p class="text-gray-600">Finding safe, affordable student housing made easy</p>
            </div>
        </div>
    </section>

    <!-- Our Story -->
    <section class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Our Story</h2>
                <p class="text-xl text-gray-600">Founded in 2025, by students looking for better housing solutions</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="text-4xl mb-4">🎯</div>
                    <h3 class="text-xl font-bold mb-3">The Problem</h3>
                    <p class="text-gray-600">
                        PSU students struggled to find safe, verified housing. Many fell victim to scams or 
                        ended up in unsuitable living conditions.
                    </p>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="text-4xl mb-4">💡</div>
                    <h3 class="text-xl font-bold mb-3">The Solution</h3>
                    <p class="text-gray-600">
                        We created a platform where every property is verified by PSU administration, ensuring 
                        safety and quality for our students.
                    </p>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="text-4xl mb-4">🚀</div>
                    <h3 class="text-xl font-bold mb-3">The Impact</h3>
                    <p class="text-gray-600">
                        Hundreds of PSU students now have access to verified, affordable housing options 
                        with transparent pricing and reviews.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Our Values</h2>
            <p class="text-gray-600 text-lg">What drives us every day</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="border-l-4 border-green-500 pl-6">
                <h3 class="text-xl font-bold mb-2">Be the Change</h3>
                <p class="text-gray-600">We actively work to improve student housing standards in our community</p>
            </div>
            
            <div class="border-l-4 border-green-500 pl-6">
                <h3 class="text-xl font-bold mb-2">Safety First</h3>
                <p class="text-gray-600">Every property is verified to ensure student safety and security</p>
            </div>
            
            <div class="border-l-4 border-green-500 pl-6">
                <h3 class="text-xl font-bold mb-2">Transparency</h3>
                <p class="text-gray-600">No hidden fees, clear pricing, and honest reviews from real students</p>
            </div>
            
            <div class="border-l-4 border-green-500 pl-6">
                <h3 class="text-xl font-bold mb-2">Student-Centric</h3>
                <p class="text-gray-600">Built by students, for students - we understand your needs</p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-green-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold mb-2">100%</div>
                    <p class="text-green-100">PSU Verified Properties</p>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">Safe</div>
                    <p class="text-green-100">Secure Platform</p>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">Free</div>
                    <p class="text-green-100">For Students</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Find Your Home?</h2>
        <p class="text-xl text-gray-600 mb-8">Join the PSU Dorm Finder community today</p>
        @guest
            <button onclick="openAuthModal('register')" class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition inline-block">
                Get Started
            </button>
        @else
            <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition inline-block">
                Browse Properties
            </a>
        @endguest
    </section>
</div>
@endsection