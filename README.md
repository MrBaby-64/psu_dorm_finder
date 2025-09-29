# PSU Dorm Finder

A web-based dormitory and rental property finder application specifically designed for Pampanga State University (PSU) students, landlords, and administrators.

## About

PSU Dorm Finder is a comprehensive property management system that connects students with suitable accommodations near Pampanga State University. The platform facilitates seamless interaction between tenants, landlords, and university administrators.

## Features

### For Students/Tenants
- Browse available properties and dormitories
- Search and filter properties by location, price, and amenities
- Save favorite properties
- Schedule property visits
- Book accommodations online
- Message landlords directly
- Leave reviews and ratings

### For Landlords
- List and manage properties
- Upload property images and details
- Manage room availability and pricing
- Handle booking requests
- Communicate with potential tenants
- Track property performance

### For Administrators
- Oversee all property listings
- Manage user accounts and verifications
- Handle property approval processes
- Generate reports and analytics
- Monitor system activity

## Technology Stack

- **Backend:** PHP 8.1+ with Laravel 10
- **Frontend:** Blade templates with Tailwind CSS
- **Database:** MySQL
- **Authentication:** Laravel Breeze with Google OAuth
- **File Storage:** Local storage with image optimization

## System Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Node.js and NPM
- Web server (Apache/Nginx)

## Installation

1. Clone the repository
2. Install PHP dependencies: `composer install`
3. Install Node dependencies: `npm install`
4. Copy environment file: `cp .env.example .env`
5. Generate application key: `php artisan key:generate`
6. Configure database settings in `.env`
7. Run migrations: `php artisan migrate`
8. Seed database: `php artisan db:seed`
9. Build assets: `npm run build`
10. Start development server: `php artisan serve`

## Configuration

Update the `.env` file with your database credentials and other environment-specific settings:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=psu_dorm_finder
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Usage

1. Access the application through your web browser
2. Register as a student, landlord, or administrator
3. Complete your profile setup
4. Begin browsing or listing properties

## License

This project is proprietary software developed for Pampanga State University.

## Support

For support and inquiries, please contact the development team.