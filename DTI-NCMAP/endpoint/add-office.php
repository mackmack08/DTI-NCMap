<?php
header('Content-Type: application/json');
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Validate required fields
    $required_fields = ['office_name', 'office_type', 'region_id', 'address', 'latitude', 'longitude'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }

    // Handle file upload
    $image_path = null;
    if (isset($_FILES['office_image']) && $_FILES['office_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/offices/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['office_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception('Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.');
        }
        
        $filename = uniqid() . '.' . $file_extension;
        $image_path = $upload_dir . $filename;
        
        if (!move_uploaded_file($_FILES['office_image']['tmp_name'], $image_path)) {
            throw new Exception('Failed to upload image');
        }
        
        $image_path = 'uploads/offices/' . $filename; // Store relative path
    }

    // Insert office
    $stmt = $conn->prepare("
        INSERT INTO offices (
            office_name, office_type, region_id, address, latitude, longitude,
            contact_number, email, office_head, description, services_offered,
            office_hours, image_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_POST['office_name'],
        $_POST['office_type'],
        $_POST['region_id'],
        $_POST['address'],
        $_POST['latitude'],
        $_POST['longitude'],
        $_POST['contact_number'] ?? null,
        $_POST['email'] ?? null,
        $_POST['office_head'] ?? null,
        $_POST['description'] ?? null,
        $_POST['services_offered'] ?? null,
        $_POST['office_hours'] ?? null,
        $image_path
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Office added successfully',
        'office_id' => $conn->lastInsertId()
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
