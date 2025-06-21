<?php
// Application Configuration
define('APP_NAME', 'DTI Office Locator System');
define('APP_VERSION', '1.0.0');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'dti_map_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('UPLOAD_PATH', 'uploads/offices/');

// Map Configuration
define('DEFAULT_MAP_CENTER_LAT', 12.8797);
define('DEFAULT_MAP_CENTER_LNG', 121.7740);
define('DEFAULT_MAP_ZOOM', 6);

// API Keys (if needed)
define('GOOGLE_MAPS_API_KEY', ''); // Add your Google Maps API key here
define('MAPBOX_ACCESS_TOKEN', ''); // Add your Mapbox token here

// Application Settings
define('ITEMS_PER_PAGE', 20);
define('ENABLE_DEBUG', false);
?>
