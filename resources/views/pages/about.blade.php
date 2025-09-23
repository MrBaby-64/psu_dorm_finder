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
                <p class="text-xl text-gray-600">Founded in 2024, by students looking for better housing solutions</p>
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold mb-2">500+</div>
                    <p class="text-green-100">Properties Listed</p>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">2,000+</div>
                    <p class="text-green-100">Students Helped</p>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">100%</div>
                    <p class="text-green-100">Verified Properties</p>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">24/7</div>
                    <p class="text-green-100">Support Available</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Find Your Home?</h2>
        <p class="text-xl text-gray-600 mb-8">Join the PSU Dorm Finder community today</p>
        <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition inline-block">
            Browse Properties
        </a>
    </section>
</div>
@endsection