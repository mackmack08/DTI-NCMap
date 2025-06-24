<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WELCOME TO NEGOSYO CENTER - CEBU PROVINCE LOCATOR </title>
    
    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Favicon -->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <style>
        :root {
            --primary-color: #0D47A1; /* Darker blue */
            --primary-dark: #002171;
            --primary-light: #BBDEFB;
            --secondary-color: #2E7D32;
            --accent-color: #EF6C00;
            --purple-color: #6A1B9A;
            --text-dark: #333333;
            --text-medium: #555555;
            --text-light: #777777;
            --background-light: #f8f9fa;
            --white: #ffffff;
            --shadow-light: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-medium: 0 4px 15px rgba(0,0,0,0.15);
            --transition-normal: all 0.3s ease;
            --border-radius-sm: 4px;
            --border-radius-md: 8px;
            --border-radius-lg: 12px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background-color: var(--background-light);
            font-family: 'Roboto', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        .landing-container {
            display: flex;
            height: 100vh;
        }
        
        .landing-left {
            flex: 1;
            background-color: rgba(255, 255, 255, 0.8); /* Transparent white background */
            backdrop-filter: blur(10px); /* Blur effect */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            box-shadow: var(--shadow-medium);
            position: relative;
            z-index: 2;
        }
        
        .landing-right {
            flex: 1.2;
            position: relative;
            overflow: hidden;
        }
        
        #preview-map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .map-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(13, 71, 161, 0.7) 0%, rgba(13, 71, 161, 0.3) 100%);
            pointer-events: none;
            z-index: 1;
        }
        
        .landing-content {
            width: 100%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .landing-logo {
            width: 100%;
            max-width: 450px; /* Wider logo */
            margin-bottom: 2.5rem;
            height: auto;
            object-fit: contain;
        }
        
        .landing-title {
            text-align: center;
            margin-bottom: 2rem;
            width: 100%;
        }
        
        .landing-title h1 {
            color: var(--primary-color);
            font-size: 2.4rem;
            margin-bottom: 0.8rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.5px;
            text-transform: uppercase; /* All capitals */
        }
        
        .landing-title p {
            color: var(--text-medium);
            font-size: 1.2rem;
            margin-top: 0;
            font-weight: 400;
            text-transform: uppercase; /* All capitals */
        }
        
        .landing-description {
            text-align: center;
            margin-bottom: 3rem;
            color: var(--text-medium);
            line-height: 1.7;
            font-size: 1.05rem;
            font-weight: 300;
        }
        
        .landing-actions {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            width: 100%;
            max-width: 340px; /* Slightly smaller buttons */
            margin-bottom: 2rem;
        }
        
        .landing-btn {
            padding: 1rem 1.3rem; /* Slightly smaller padding */
            border-radius: var(--border-radius-md);
            font-size: 1rem; /* Slightly smaller font */
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: var(--transition-normal);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            text-decoration: none;
            box-shadow: var(--shadow-light);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 33, 113, 0.3);
        }
        
        .btn-secondary {
            background-color: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .btn-secondary:hover {
            background-color: rgba(187, 222, 251, 0.2);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(13, 71, 161, 0.2);
        }
        
        .btn-icon {
            font-size: 1.1rem;
        }
        
        .landing-footer {
            position: absolute;
            bottom: 1.5rem;
            text-align: center;
            color: var(--text-light);
            font-size: 0.9rem;
            width: 100%;
        }
        
        .map-features {
            position: absolute;
            bottom: 3rem;
            right: 3rem;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
                        max-width: 420px;
            box-shadow: var(--shadow-medium);
            z-index: 2;
            backdrop-filter: blur(5px);
        }
        
        .map-features h3 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 1.2rem;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-weight: 600;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.2rem;
            gap: 1rem;
        }
        
        .feature-item:last-child {
            margin-bottom: 0;
        }
        
        .feature-icon {
            color: var(--primary-color);
            font-size: 1.1rem;
            margin-top: 0.2rem;
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(187, 222, 251, 0.4);
            border-radius: 50%;
            padding: 1rem;
        }
        
        .feature-text {
            flex: 1;
            font-size: 1rem;
            color: var(--text-medium);
            line-height: 1.5;
        }
        
        .nc-badge {
            display: inline-block;
            background-color: var(--primary-color);
            color: var(--white);
            padding: 0.2rem 0.6rem;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            font-size: 0.9rem;
            margin: 0 0.2rem;
        }
        
        /* Custom NC marker styles */
        .nc-marker-icon {
            background-color: var(--primary-color);
            color: var(--white);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 15px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.3);
            border: 2.5px solid var(--white);
            animation: float 3s ease-in-out infinite;
        }
        
        /* Floating animation for NC markers */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
            100% {
                transform: translateY(0px);
            }
        }
        
        /* Staggered floating animation for different markers */
        .nc-marker-icon:nth-child(odd) {
            animation-delay: 0.5s;
        }
        
        .nc-marker-icon:nth-child(3n) {
            animation-delay: 1s;
        }
        
        .nc-marker-icon:nth-child(3n+1) {
            animation-delay: 1.5s;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(3);
                opacity: 0;
            }
        }
        
        /* Floating NC logos on the map */
        .floating-nc {
            position: absolute;
            background-color: var(--primary-color);
            color: var(--white);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.4);
            border: 3px solid var(--white);
            z-index: 5;
            animation: float-random 4s ease-in-out infinite;
            opacity: 0.85;
        }
        
        .floating-nc.type-a {
            background-color: var(--secondary-color);
        }
        
        .floating-nc.type-b {
            background-color: var(--accent-color);
        }
        
        @keyframes float-random {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(10px, -15px) rotate(5deg);
            }
            66% {
                transform: translate(-5px, -8px) rotate(-3deg);
            }
            100% {
                transform: translate(0, 0) rotate(0deg);
            }
        }
        
        /* Responsive styles */
        @media (max-width: 1200px) {
            .landing-left {
                padding: 2.5rem;
            }
            
            .landing-logo {
                max-width: 600px;
                margin-bottom: 2rem;
            }
            
            .landing-title h1 {
                font-size: 2.2rem;
            }
            
            .map-features {
                right: 2rem;
                bottom: 2rem;
                max-width: 380px;
            }
        }
        
        @media (max-width: 992px) {
            .landing-container {
                flex-direction: column;
            }
            
            .landing-left {
                padding: 2rem;
                min-height: 60vh;
                justify-content: flex-start;
                padding-top: 3rem;
            }
            
            .landing-right {
                min-height: 40vh;
            }
            
            .landing-logo {
                max-width: 320px;
                margin-bottom: 1.5rem;
            }
            
            .landing-title h1 {
                font-size: 2rem;
            }
            
            .landing-title p {
                font-size: 1.1rem;
            }
            
            .landing-description {
                margin-bottom: 2rem;
                font-size: 1rem;
            }
            
            .map-features {
                bottom: 1.5rem;
                right: 1.5rem;
                left: 1.5rem;
                max-width: none;
                padding: 1.5rem;
            }
            
            .feature-item {
                margin-bottom: 1rem;
            }
            
            .floating-nc {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }
        
        @media (max-width: 576px) {
            .landing-left {
                padding: 1.5rem;
                padding-top: 2rem;
            }
            
            .landing-logo {
                max-width: 280px;
                margin-bottom: 1.2rem;
            }
            
            .landing-title h1 {
                font-size: 1.8rem;
            }
            
            .landing-title p {
                font-size: 1rem;
            }
            
            .landing-btn {
                padding: 0.9rem;
                font-size: 0.95rem;
            }
            
            .map-features {
                bottom: 1rem;
                right: 1rem;
                left: 1rem;
                padding: 1.2rem;
            }
            
            .map-features h3 {
                font-size: 1.2rem;
                margin-bottom: 1rem;
            }
            
            .feature-icon {
                padding: 0.8rem;
            }
            
            .feature-text {
                font-size: 0.9rem;
            }
            
            .floating-nc {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <!-- Left Side - Login/Guest Access -->
        <div class="landing-left">
            <div class="landing-content">
                <img src="img/NCBANNER.png" alt="DTI Logo" class="landing-logo">
                
                <div class="landing-title">
                    <h1>NEGOSYO CENTER LOCATOR SYSTEM</h1>
                    <p>CEBU PROVINCE NC MAP</p>
                </div>
                
                <div class="landing-description">
                    <p>Welcome to the Department of Trade and Industry's interactive office locator system for Cebu Province. Explore Negosyo Center and NC staff locations throughout the region.</p>
                </div>
                
                <div class="landing-actions">
                    <a href="login.php" class="landing-btn btn-primary">
                        <i class="fas fa-user-lock btn-icon"></i> Login as Administrator
                    </a>
                    <a href="index.php" class="landing-btn btn-secondary">
                        <i class="fas fa-map-marked-alt btn-icon"></i> Continue as Guest
                    </a>
                </div>
            </div>
            
            <div class="landing-footer">
                <p>&copy; <?php echo date('Y'); ?> Department of Trade and Industry - Cebu Provincial Office</p>
            </div>
        </div>
        
        <!-- Right Side - Map Preview -->
        <div class="landing-right">
            <div id="preview-map"></div>
            <div class="map-overlay"></div>
            
    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize the preview map when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            initPreviewMap();
            addFloatingNCLogos();
        });
        
        function initPreviewMap() {
            // Cebu province center coordinates
            const CEBU_CENTER = [10.3157, 123.8854]; // Approximate center of Cebu City
            
            // Create the map centered on Cebu
            const previewMap = L.map('preview-map', {
                center: CEBU_CENTER,
                zoom: 10,
                zoomControl: false,
                attributionControl: false,
                dragging: false,
                touchZoom: false,
                doubleClickZoom: false,
                scrollWheelZoom: false,
                boxZoom: false,
                tap: false
            });
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                noWrap: true
            }).addTo(previewMap);
            
            // Add sample office markers
            addSampleMarkers(previewMap);
        }
        
        function createNCMarkerIcon(officeType) {
            // Create a custom HTML element for the marker
            const markerHtml = document.createElement('div');
            markerHtml.className = 'nc-marker-icon';
            
            // Set background color based on office type
            let bgColor = '#0D47A1'; // Darker blue
            
            switch(officeType) {
                case 'Regional Office':
                    bgColor = '#002171'; // Even darker blue
                    break;
                case 'Provincial Office':
                    bgColor = '#2E7D32'; // Green
                    break;
                case 'Field Office':
                    bgColor = '#EF6C00'; // Orange
                    break;
                case 'Extension Office':
                    bgColor = '#6A1B9A'; // Purple
                    break;
                case 'Negosyo Center':
                    bgColor = '#0D47A1'; // Darker blue for Negosyo Centers
                    break;
            }
            
            markerHtml.style.backgroundColor = bgColor;
            
            // Add NC text
            markerHtml.innerHTML = 'NC';
            
            // Create a custom divIcon
            return L.divIcon({
                html: markerHtml,
                className: 'custom-nc-marker',
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -40]
            });
        }
        
        function addSampleMarkers(map) {
            // Define Cebu's approximate boundaries
            const CEBU_BOUNDS = {
                minLat: 9.4,
                maxLat: 11.3,
                minLng: 123.5,
                maxLng: 124.5
            };
            
            // Sample office data for Cebu with more Negosyo Centers
            const offices = [
                // Main DTI Offices
                {
                    name: "DTI Region VII - Regional Office",
                    type: "Regional Office",
                    lat: 10.3157,
                    lng: 123.8854
                },
                {
                    name: "DTI Cebu Provincial Office",
                    type: "Provincial Office",
                    lat: 10.3116,
                                        lng: 123.9120
                },
                {
                    name: "DTI Mandaue Field Office",
                    type: "Field Office",
                    lat: 10.3231,
                    lng: 123.9400
                },
                {
                    name: "DTI Lapu-Lapu Extension Office",
                    type: "Extension Office",
                    lat: 10.3100,
                    lng: 123.9500
                },
                {
                    name: "DTI Toledo Field Office",
                    type: "Field Office",
                    lat: 10.3780,
                    lng: 123.6420
                },
                {
                    name: "DTI Bogo Extension Office",
                    type: "Extension Office",
                    lat: 11.0522,
                    lng: 124.0021
                },
                
                // Additional Negosyo Centers in Cebu City and Metro Cebu
                {
                    name: "Cebu City Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.2977,
                    lng: 123.9021
                },
                {
                    name: "Mandaue City Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.3344,
                    lng: 123.9428
                },
                {
                    name: "Lapu-Lapu City Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.3132,
                    lng: 123.9594
                },
                {
                    name: "Talisay City Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.2447,
                    lng: 123.8500
                },
                
                // Northern Cebu Negosyo Centers
                {
                    name: "Bogo City Negosyo Center",
                    type: "Negosyo Center",
                    lat: 11.0430,
                    lng: 123.9850
                },
                {
                    name: "Danao City Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.5580,
                    lng: 124.0240
                },
                {
                    name: "San Remigio Negosyo Center",
                    type: "Negosyo Center",
                    lat: 11.0050,
                    lng: 123.9420
                },
                {
                    name: "Medellin Negosyo Center",
                    type: "Negosyo Center",
                    lat: 11.1290,
                    lng: 123.9650
                },
                
                // Southern Cebu Negosyo Centers
                {
                    name: "Carcar City Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.1060,
                    lng: 123.6420
                },
                {
                    name: "Argao Negosyo Center",
                    type: "Negosyo Center",
                    lat: 9.8830,
                    lng: 123.6080
                },
                {
                    name: "Dalaguete Negosyo Center",
                    type: "Negosyo Center",
                    lat: 9.7620,
                    lng: 123.5350
                },
                {
                    name: "Santander Negosyo Center",
                    type: "Negosyo Center",
                    lat: 9.4280,
                    lng: 123.3400
                },
                
                // Western Cebu Negosyo Centers
                {
                    name: "Toledo City Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.3760,
                    lng: 123.6500
                },
                {
                    name: "Balamban Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.4920,
                    lng: 123.7720
                },
                {
                    name: "Tuburan Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.7230,
                    lng: 123.8250
                },
                
                // Central Cebu Negosyo Centers
                {
                    name: "Liloan Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.4000,
                    lng: 123.9950
                },
                {
                    name: "Consolacion Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.3730,
                    lng: 123.9580
                },
                {
                    name: "Compostela Negosyo Center",
                    type: "Negosyo Center",
                    lat: 10.4580,
                    lng: 124.0120
                }
            ];
            
            // Filter offices to ensure they're only within Cebu's boundaries
            const cebuOffices = offices.filter(office => 
                office.lat >= CEBU_BOUNDS.minLat && 
                office.lat <= CEBU_BOUNDS.maxLat && 
                office.lng >= CEBU_BOUNDS.minLng && 
                office.lng <= CEBU_BOUNDS.maxLng
            );
            
            // Add markers for each office
            cebuOffices.forEach(office => {
                // Create custom NC marker icon
                const markerIcon = createNCMarkerIcon(office.type);
                
                // Add marker to map
                const marker = L.marker([office.lat, office.lng], {icon: markerIcon}).addTo(map);
                
                // Add pulse effect only for main DTI offices (not all Negosyo Centers)
                if (office.type !== "Negosyo Center" || Math.random() < 0.3) { // Only add pulse to 30% of Negosyo Centers
                    addPulseEffect(map, office.lat, office.lng);
                }
            });
        }
        
        function addPulseEffect(map, lat, lng) {
            // Create a pulse effect for important markers
            const pulseIcon = L.divIcon({
                className: 'pulse',
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });
            
            // Add the pulse marker at the same position
            L.marker([lat, lng], {
                icon: pulseIcon,
                zIndexOffset: -1000 // Place behind the actual marker
            }).addTo(map);
        }
        
        function addFloatingNCLogos() {
            const container = document.getElementById('floating-nc-container');
            const mapContainer = document.querySelector('.landing-right');
            const mapWidth = mapContainer.offsetWidth;
            const mapHeight = mapContainer.offsetHeight;
            
            // Create several floating NC logos
            const ncTypes = ['', 'type-a', 'type-b'];
            const ncLabels = ['NC', 'A', 'B'];
            
            // Add 8 floating NC logos with different positions
            for (let i = 0; i < 8; i++) {
                const typeIndex = i % ncTypes.length;
                const ncLogo = document.createElement('div');
                ncLogo.className = `floating-nc ${ncTypes[typeIndex]}`;
                ncLogo.textContent = ncLabels[typeIndex];
                
                // Random position within the map area
                const left = 10 + Math.random() * (mapWidth - 70);
                const top = 10 + Math.random() * (mapHeight - 70);
                
                // Set position
                ncLogo.style.left = `${left}px`;
                ncLogo.style.top = `${top}px`;
                
                // Random animation delay
                ncLogo.style.animationDelay = `${Math.random() * 2}s`;
                
                // Add to container
                mapContainer.appendChild(ncLogo);
            }
        }
    </script>
</body>
</html>


