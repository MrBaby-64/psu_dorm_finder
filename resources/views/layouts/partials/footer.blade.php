<footer class="bg-gray-800 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <h3 class="text-xl font-bold mb-4">Dorm Finder</h3>
                <p class="text-gray-400">Helping students find their perfect home away from home.</p>
            </div>
            <div>
                <h4 class="font-semibold mb-3">Quick Links</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ route('properties.browse') }}" class="hover:text-white">Browse Rentals</a></li>
                    @guest
                        <li><a href="#" onclick="navigateToSection('about-us'); return false;" class="hover:text-white">About Us</a></li>
                        <li><a href="#" onclick="navigateToSection('how-it-works'); return false;" class="hover:text-white">How It Works</a></li>
                    @else
                        <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                        <li><a href="{{ route('how-it-works') }}" class="hover:text-white">How It Works</a></li>
                    @endguest
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3">For Landlords</h4>
                <ul class="space-y-2 text-gray-400">
                    @auth
                        <li><a href="{{ route('landlord.properties.create') }}" class="hover:text-white">List Your Property</a></li>
                    @else
                        <li><button onclick="openAuthModal('signup')" class="hover:text-white">List Your Property</button></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3">Contact</h4>
                <ul class="space-y-2 text-gray-400">
                    <li>Pampanga State University</li>
                    <li>Bacolor & San Fernando</li>
                    <li>Pampanga, Philippines</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Dorm Finder. All rights reserved.</p>
        </div>
    </div>
</footer>