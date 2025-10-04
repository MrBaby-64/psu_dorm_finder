<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Cloudinary settings used for image uploads
    | and transformations. You can get these credentials from your Cloudinary
    | dashboard at https://cloudinary.com/console
    |
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'url' => env('CLOUDINARY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Upload Settings
    |--------------------------------------------------------------------------
    */

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    /*
    |--------------------------------------------------------------------------
    | Default Upload Folder
    |--------------------------------------------------------------------------
    */

    'folders' => [
        'properties' => 'psu-dorm-finder/properties',
        'rooms' => 'psu-dorm-finder/rooms',
        'profile_pictures' => 'psu-dorm-finder/profile-pictures',
    ],

];
