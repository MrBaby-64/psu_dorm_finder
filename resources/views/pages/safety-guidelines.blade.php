@extends('layouts.guest')

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-500 to-green-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-6">
                <span class="text-5xl">🛡️</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Safety Guidelines</h1>
            <p class="text-xl text-green-100">Your safety is our priority. Follow these guidelines for a secure rental experience.</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        <!-- For Tenants Section -->
        <section class="mb-16">
            <div class="bg-blue-50 rounded-2xl p-8 border-2 border-blue-200 shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-4xl">👤</span>
                    <h2 class="text-3xl font-bold text-blue-800">For Tenants</h2>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Always visit the property in person</h3>
                            <p class="text-gray-700">Before making any payment or commitment, visit the property yourself. Never rent a place you haven't seen with your own eyes.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Verify the landlord's identity</h3>
                            <p class="text-gray-700">Check their property ownership documents and valid ID before signing any contract. Make sure you're dealing with the real owner.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Read contracts carefully</h3>
                            <p class="text-gray-700">Understand all terms, fees, and cancellation policies before signing. Don't hesitate to ask questions about anything unclear.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Request official receipts</h3>
                            <p class="text-gray-700">For all payments made (deposit, rent, utilities), always request and keep official receipts for your records.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Check property condition</h3>
                            <p class="text-gray-700">Document any existing damage with photos before moving in. This protects you from being charged for pre-existing issues.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Keep emergency contacts handy</h3>
                            <p class="text-gray-700">Maintain a list of emergency contacts including local authorities, property management, and trusted friends or family.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-red-50 p-5 rounded-lg border-2 border-red-200">
                        <span class="text-red-600 font-bold text-2xl flex-shrink-0 mt-1">✗</span>
                        <div>
                            <h3 class="font-bold text-red-900 mb-1">Never send money before viewing</h3>
                            <p class="text-red-800">Do not send money to someone you haven't met or before seeing the property. This is a common scam tactic.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-red-50 p-5 rounded-lg border-2 border-red-200">
                        <span class="text-red-600 font-bold text-2xl flex-shrink-0 mt-1">✗</span>
                        <div>
                            <h3 class="font-bold text-red-900 mb-1">Avoid deals that seem too good to be true</h3>
                            <p class="text-red-800">If rent is significantly lower than market rate, be cautious. Scammers often use unrealistic prices to lure victims.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Landlords Section -->
        <section class="mb-16">
            <div class="bg-green-50 rounded-2xl p-8 border-2 border-green-200 shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-4xl">🏢</span>
                    <h2 class="text-3xl font-bold text-green-800">For Landlords</h2>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Verify tenant identity</h3>
                            <p class="text-gray-700">Request valid ID and conduct background checks when possible. This protects both you and your property.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Provide accurate property information</h3>
                            <p class="text-gray-700">Use recent photos and honest descriptions of amenities. Transparency builds trust and prevents disputes.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Use written contracts</h3>
                            <p class="text-gray-700">Clearly state all terms including rent, deposit, utilities, and house rules. Written agreements protect both parties.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Issue official receipts</h3>
                            <p class="text-gray-700">Provide receipts for all payments received to maintain transparency and proper documentation.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Maintain property safety</h3>
                            <p class="text-gray-700">Ensure working locks, fire extinguishers, and proper electrical installations. Safety features protect your tenants and your property.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-green-600 font-bold text-2xl flex-shrink-0 mt-1">✓</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Respect tenant privacy</h3>
                            <p class="text-gray-700">Provide advance notice before property visits. Your tenants have a right to peaceful enjoyment of their space.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-red-50 p-5 rounded-lg border-2 border-red-200">
                        <span class="text-red-600 font-bold text-2xl flex-shrink-0 mt-1">✗</span>
                        <div>
                            <h3 class="font-bold text-red-900 mb-1">Don't discriminate</h3>
                            <p class="text-red-800">Treat all tenants fairly regardless of gender, religion, or background. Discrimination is unethical and illegal.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- General Safety Tips Section -->
        <section class="mb-16">
            <div class="bg-yellow-50 rounded-2xl p-8 border-2 border-yellow-200 shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-4xl">⚠️</span>
                    <h2 class="text-3xl font-bold text-yellow-800">General Safety Tips</h2>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-yellow-600 font-bold text-2xl flex-shrink-0 mt-1">!</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Meet in public places</h3>
                            <p class="text-gray-700">For initial discussions, if you're uncomfortable meeting at the property, choose a public location like a café or the university campus.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-yellow-600 font-bold text-2xl flex-shrink-0 mt-1">!</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Trust your instincts</h3>
                            <p class="text-gray-700">If something feels wrong or unsafe, walk away from the deal. Your safety and comfort should always come first.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-yellow-600 font-bold text-2xl flex-shrink-0 mt-1">!</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Report suspicious activity</h3>
                            <p class="text-gray-700">If you encounter suspicious behavior or scam attempts, report it to platform administrators immediately.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-yellow-600 font-bold text-2xl flex-shrink-0 mt-1">!</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Keep communication on platform</h3>
                            <p class="text-gray-700">Initially, keep your conversations on the platform. This creates a record for your protection and helps us maintain safety.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white p-5 rounded-lg shadow-sm">
                        <span class="text-yellow-600 font-bold text-2xl flex-shrink-0 mt-1">!</span>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Bring someone with you</h3>
                            <p class="text-gray-700">When viewing properties or meeting new people, bring a friend or family member for added safety and a second opinion.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Support Section -->
        <section>
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl p-8 border-2 border-blue-200 shadow-sm text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">Need Help or Want to Report Something?</h2>
                <p class="text-gray-700 mb-6 max-w-2xl mx-auto">
                    If you encounter any suspicious behavior or have safety concerns, please contact our support team immediately.
                    We're here to help keep our community safe.
                </p>
                <a href="mailto:psuteam001@gmail.com" class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 transition font-semibold shadow-lg hover:shadow-xl text-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                    </svg>
                    Email Support
                </a>
            </div>
        </section>

        <!-- Back to Home -->
        <div class="text-center mt-12">
            <a href="{{ route('home') }}" class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold text-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
