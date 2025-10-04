# PSU DORM FINDER - Presentation Script

## Introduction
Good [morning/afternoon], honorable panelists. We are pleased to present **PSU Dorm Finder**, a comprehensive web-based dormitory and rental property finder application specifically designed for Pampanga State University students, landlords, and administrators.

---

## 1. HOME PAGE

**[Show Homepage]**

Welcome to the PSU Dorm Finder homepage. This is the first touchpoint for all our users.

### Key Features:
- **Hero Section**: Eye-catching gradient banner with clear value proposition - "Find Rooms for Rent with PSU Dorm Finder"
- **Search Functionality**: Prominent search bar allowing users to search by location or property name
- **Quick Amenity Filters**: One-click access to popular amenities including:
  - WiFi
  - Kitchen
  - Parking
  - Laundry
  - Air Conditioning
- **Featured Properties**: Display of verified dormitories and apartments near PSU Bacolor & San Fernando
- **Navigation Menu**: Easy access to Browse Rentals, How It Works, and About pages

The homepage is designed to be user-friendly and provides immediate value by showcasing available properties and search capabilities.

---

## 2. AUTHENTICATION SYSTEM

### Login Page
**[Show Login Page]**

Our secure login system offers multiple authentication methods:

- **Email & Password Authentication**: Traditional secure login
- **Google OAuth Integration**: One-click login using Google accounts for convenience
- **Password Recovery**: Forgot password functionality with email reset links
- **Remember Me**: Option to stay logged in for returning users
- **Responsive Design**: Works seamlessly on desktop and mobile devices

#### Security Features:
- **Login Rate Limiter**:
  - Maximum 5 failed login attempts per email/IP combination
  - Account temporarily locked after exceeding limit
  - Automatic unlock after cooldown period
  - Prevents brute force attacks

### Registration Page
**[Show Registration Page]**

New users can easily create accounts with:

- **User Information**: Name, email, and password
- **Role Selection**: Users choose their role during registration:
  - **Tenant/Student**: For those seeking accommodation
  - **Landlord**: For property owners
  - **Admin**: (By invitation only)
- **Email Verification**: Automated verification process
- **Google Sign-Up**: Alternative quick registration via Google
- **reCAPTCHA Protection**: Security against bot registrations

#### Security Features:
- **Registration Rate Limiter**:
  - Maximum 5 registration attempts per hour per IP address
  - Prevents multiple account spam from same IP
  - Automatic reset after 1 hour
  - Clears immediately upon successful registration
- **Duplicate Prevention**: Email and phone number uniqueness validation
- **Password Strength**: Enforced strong password requirements

---

## 3. TENANT/STUDENT FEATURES

### Browse Properties
**[Show Browse Page]**

The heart of our tenant experience - a powerful property browsing system:

#### Search & Filter Options:
- **Text Search**: Search by property name, location, or description
- **Price Range Filter**: Minimum and maximum price filters
- **Location Filter**: Filter by specific areas near PSU
- **Amenity Filters**: Multiple amenity selection (WiFi, Kitchen, Parking, etc.)
- **Property Type**: Filter by dorms, apartments, or rooms
- **Availability Status**: Show only available properties

#### Property Display:
- **Grid/List View**: Toggle between viewing modes
- **Property Cards**: Each showing:
  - Cover image
  - Property name and location
  - Price per month
  - Key amenities icons
  - Availability status
  - Quick favorite button
  - View details button

### Property Details & Booking
**[Show Property Details Page]**

When a tenant clicks on a property, they see comprehensive information:

#### Property Information:
- **Image Gallery**: Multiple high-quality property images with slideshow
- **Detailed Description**: Full property details and rules
- **Amenities List**: Complete list of available amenities
- **Room Types**: Different room options with individual pricing
- **Location Map**: Interactive map showing exact location
- **Landlord Information**: Contact details and verification status
- **Reviews & Ratings**: Previous tenant reviews and ratings

#### Interaction Options:
- **Schedule Visit**: Book a property visit appointment
- **Send Inquiry**: Direct message to landlord
- **Add to Favorites**: Save property for later
- **Book Now**: Create booking request

### Booking & Inquiries Process
**[Demonstrate Booking Flow]**

1. **Inquiry Form**:
   - Tenant fills out inquiry with message
   - Select preferred move-in date
   - Specify room preferences
   - Submit to landlord

2. **Scheduled Visits**:
   - Choose visit date and time
   - Add special requests or notes
   - Receive confirmation from landlord
   - Get email notifications

3. **Booking Request**:
   - Select room type
   - Choose start date
   - Review pricing
   - Submit booking request
   - Wait for landlord approval

### Tenant Dashboard
**[Show Tenant Dashboard]**

The tenant dashboard provides centralized access to:

#### Navigation Sections:
1. **My Account**: Profile management and settings
2. **Scheduled Visits**:
   - Upcoming visits
   - Visit history
   - Cancel or reschedule options
3. **My Bookings**:
   - Active bookings
   - Pending requests
   - Booking history
4. **Messages**:
   - Conversations with landlords
   - Inquiry responses
   - Real-time notifications
5. **Favorites**:
   - Saved properties
   - Quick access to favorites
   - Remove from favorites option
6. **My Reviews**:
   - Reviews written by tenant
   - Edit or delete reviews
7. **Notifications**:
   - Booking updates
   - Message notifications
   - Visit confirmations

---

## 4. LANDLORD FEATURES

### Create Property
**[Show Property Creation Form]**

Landlords can list their properties with a comprehensive creation process:

#### Step 1: Basic Information
- Property name
- Property type (Dorm, Apartment, Room)
- Short description
- Detailed description
- House rules

#### Step 2: Location
- Street address
- Barangay
- City/Municipality
- Interactive map marker placement
- Proximity to PSU campus

#### Step 3: Images
- **Cloudinary Integration**: Professional image upload
- Upload multiple property images
- Set cover/main image
- Image preview and management
- Automatic image optimization

#### Step 4: Room Details
- Add multiple room types
- Set individual room pricing
- Specify capacity per room
- Room-specific amenities

#### Step 5: Amenities
- WiFi
- Kitchen
- Parking
- Laundry
- Air Conditioning
- Security features
- Study areas
- And more...

#### Step 6: Pricing & Availability
- Monthly rent pricing
- Deposit requirements
- Utility inclusions
- Availability calendar

### Property Management
**[Show Landlord Properties Dashboard]**

Landlords have full control over their listings:

#### Property Actions:
- **View**: See property as tenants see it
- **Edit**: Update any property information
- **Manage Images**: Add, remove, or change cover image
- **Manage Rooms**: Update room availability and pricing
- **Archive/Delete**: Request property removal
- **Performance Stats**: View property views and inquiries

#### Property Status Indicators:
- **Pending**: Awaiting admin approval
- **Approved**: Live and visible to tenants
- **Featured**: Highlighted in search results
- **Rejected**: Needs revision

### Landlord Dashboard
**[Show Landlord Dashboard]**

Comprehensive management interface with:

#### Dashboard Sections:
1. **My Properties**:
   - All listed properties
   - Quick stats per property
   - Add new property button

2. **Inquiries**:
   - New inquiries counter
   - Inquiry list with tenant details
   - Approve/Reject actions
   - Reply to inquiries

3. **Scheduled Visits**:
   - Pending visit requests
   - Confirm visits
   - Mark as completed
   - Cancel with reason

4. **Bookings**:
   - Pending booking requests
   - Approve/Reject bookings
   - Booking history
   - Tenant information

5. **Messages**:
   - Direct communication with tenants
   - Organized by property
   - Unread message counter

6. **Admin Messages**:
   - Communications from administrators
   - Deletion request responses
   - Verification updates

7. **Notifications**:
   - New inquiry alerts
   - Booking requests
   - Visit requests
   - Admin responses

---

## 5. ADMIN FEATURES & FUNCTIONS

### Admin Dashboard
**[Show Admin Dashboard]**

The admin dashboard provides complete system oversight:

#### Dashboard Overview:
- **Total Users**: Count of registered users
- **Total Properties**: All property listings
- **Pending Approvals**: Properties awaiting approval
- **Active Bookings**: Current booking count
- **System Analytics**: Charts and graphs showing:
  - User registration trends
  - Property listing trends
  - Booking statistics
  - Popular locations

### Property Management
**[Show Admin Property Management]**

#### Pending Properties:
- Review new property submissions
- View all property details
- Check uploaded images
- Verify landlord information

#### Approval Process:
1. **Review**: Examine property details
2. **Approve**:
   - Make property live
   - Send approval notification to landlord
3. **Reject**:
   - Provide reason for rejection
   - Notify landlord with improvements needed

#### Additional Actions:
- **Verify Property**: Mark as verified/trusted
- **Feature Property**: Highlight in search results
- **Delete Property**: Remove inappropriate listings

### Property Deletion Requests
**[Show Deletion Requests]**

When landlords request property deletion:
- View deletion request details
- See landlord's reason
- Review property status
- Approve or reject deletion
- Send response to landlord

### User Management
**[Show User Management]**

Complete user administration:

#### User Overview:
- List all registered users
- Filter by role (Tenant, Landlord, Admin)
- Search by name or email
- User status indicators

#### User Actions:
- **View Profile**: See complete user details
- **Update Role**: Change user permissions
- **Verify User**: Mark as verified
- **View Activity**: See user's bookings/properties
- **Manage Account**: Enable/disable accounts

### Message Center
**[Show Admin Messages]**

Centralized communication hub:

#### Features:
- Messages from landlords (contact admin form)
- Property deletion requests
- Respond to inquiries
- Mark messages as read/resolved
- Email notifications to landlords

### Reports & Analytics
**[Show Reports Page]**

Comprehensive reporting system:

#### Available Reports:
1. **User Reports**:
   - New registrations by date
   - User role distribution
   - Active vs inactive users

2. **Property Reports**:
   - Properties by location
   - Average pricing trends
   - Popular amenities

3. **Booking Reports**:
   - Booking success rate
   - Peak booking periods
   - Revenue projections

4. **Export Options**:
   - PDF export
   - Excel/CSV download
   - Date range filtering

---

## 6. ADDITIONAL FEATURES

### Messaging System
**[Show Messages]**

Real-time communication between users:
- Direct messaging between tenants and landlords
- Conversation threading
- Message read status
- Email notifications
- Attachment support

### Reviews & Ratings
**[Show Reviews Section]**

Quality assurance through reviews:
- Tenants can review properties after booking
- Star rating system (1-5 stars)
- Written review comments
- Landlord responses
- Review moderation by admin

### Notifications
**[Show Notifications]**

Real-time notification system:
- In-app notifications
- Email notifications
- Notification categories:
  - Booking updates
  - Message alerts
  - Visit confirmations
  - Admin announcements
- Mark as read functionality
- Notification preferences

### Favorites System
**[Show Favorites]**

Save properties for later:
- Add to favorites from browse page
- Dedicated favorites page
- Remove from favorites
- Quick access to saved properties

---

## 7. TECHNOLOGIES & TOOLS USED

### Backend Technologies:
1. **PHP 8.2+**: Core programming language
2. **Laravel 12.0**: Modern PHP framework
   - MVC Architecture
   - Eloquent ORM
   - Blade Templating Engine
   - Artisan CLI
   - Job Queues
   - Email System

### Frontend Technologies:
1. **HTML5 & CSS3**: Modern web standards
2. **Tailwind CSS v3**: Utility-first CSS framework
3. **Alpine.js v3**: Lightweight JavaScript framework
4. **Vite**: Modern build tool and dev server
5. **Axios**: HTTP client for API requests

### Authentication & Security:
1. **Laravel Breeze**: Authentication scaffolding
2. **Laravel Socialite**: Google OAuth integration
3. **NoCaptcha (anhskohbo/no-captcha)**: reCAPTCHA protection
4. **CSRF Protection**: Built-in Laravel security
5. **Password Hashing**: Bcrypt encryption

### Database:
1. **MySQL 5.7+**: Relational database
2. **Laravel Migrations**: Database version control
3. **Eloquent ORM**: Object-relational mapping
4. **Database Seeders**: Sample data generation

### Cloud Services & APIs:
1. **Cloudinary**: Image hosting and optimization
   - Image uploads
   - Automatic optimization
   - CDN delivery
   - Image transformations

2. **SendGrid**: Email delivery service
   - Transactional emails
   - Email notifications
   - Delivery tracking

3. **Resend**: Alternative email service
   - Laravel integration
   - Email templates

4. **Google OAuth 2.0**: Social authentication
   - One-click login
   - Secure authentication

### Development Tools:
1. **Composer**: PHP dependency manager
2. **NPM**: Node package manager
3. **Git**: Version control
4. **Laravel Pint**: Code style fixer
5. **Laravel Pail**: Log viewer
6. **PHPUnit**: Testing framework

### Additional Libraries:
1. **Laravel Tinker**: REPL for Laravel
2. **Faker PHP**: Fake data generation
3. **Mockery**: Mocking framework for tests
4. **Collision**: Error handler for console

### Deployment & Infrastructure:
1. **Apache/Nginx**: Web server
2. **Docker**: Containerization (optional)
3. **Laravel Sail**: Docker development environment
4. **Render**: Cloud deployment platform
5. **GitHub**: Code repository

### Key Features Powered by Technology:
- **Image Upload**: Cloudinary PHP SDK
- **Email Notifications**: SendGrid/Resend Laravel integration
- **Real-time Features**: Alpine.js reactivity
- **Responsive Design**: Tailwind CSS utilities
- **Authentication**: Laravel Breeze + Socialite
- **API Integration**: Axios for AJAX requests
- **Map Integration**: Interactive property location mapping

---

## CONCLUSION

**PSU Dorm Finder** is a comprehensive, modern web application that successfully bridges the gap between PSU students seeking accommodation and landlords offering rental properties.

### Key Achievements:
✅ **User-Friendly Interface**: Intuitive design for all user types
✅ **Secure Authentication**: Multiple login options with Google OAuth
✅ **Comprehensive Property Management**: Full CRUD operations for landlords
✅ **Efficient Booking System**: Streamlined inquiry and booking process
✅ **Admin Control**: Complete system oversight and moderation
✅ **Modern Technology Stack**: Built with latest web technologies
✅ **Cloud Integration**: Cloudinary for images, SendGrid for emails
✅ **Responsive Design**: Works on all devices
✅ **Real-time Communication**: Messaging and notification systems

### Impact:
- **For Students**: Easy property discovery and booking
- **For Landlords**: Effective property marketing and tenant management
- **For PSU Community**: Centralized, verified housing solution

Thank you for your attention. We are now ready for your questions and feedback.

---

## DEMONSTRATION CHECKLIST

- [ ] Homepage navigation and search
- [ ] User registration (Tenant & Landlord)
- [ ] Login with email and Google OAuth
- [ ] Browse properties with filters
- [ ] Property details and features
- [ ] Tenant: Send inquiry and schedule visit
- [ ] Tenant: Create booking request
- [ ] Tenant dashboard navigation
- [ ] Landlord: Create new property
- [ ] Landlord: Upload images via Cloudinary
- [ ] Landlord: Manage inquiries
- [ ] Landlord: Approve/reject bookings
- [ ] Landlord dashboard navigation
- [ ] Admin: Approve pending properties
- [ ] Admin: User management
- [ ] Admin: Handle deletion requests
- [ ] Admin: View reports and analytics
- [ ] Messaging system demonstration
- [ ] Notification system
- [ ] Review and rating system
- [ ] Mobile responsiveness

---

*PSU Dorm Finder - Connecting Students with Safe, Verified Housing*
