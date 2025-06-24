<?php
header('Content-Type: application/json');
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Validate required fields
    $required_fields = ['office_id', 'office_name', 'office_type', 'region_id', 'address', 'latitude', 'longitude'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }

    $office_id = $_POST['office_id'];
    
    // Get current office data
    $stmt = $conn->prepare("SELECT image_path FROM offices WHERE office_id = ?");
    $stmt->execute([$office_id]);
    $current_office = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$current_office) {
        throw new Exception('Office not found');
    }
    
    $image_path = $current_office['image_path'];

    // Handle file upload
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
        
        // Delete old image if exists
        if ($image_path && file_exists('../' . $image_path)) {
            unlink('../' . $image_path);
        }
        
        $filename = uniqid() . '.' . $file_extension;
        $new_image_path = $upload_dir . $filename;
        
        if (!move_uploaded_file($_FILES['office_image']['tmp_name'], $new_image_path)) {
            throw new Exception('Failed to upload image');
        }
        
        $image_path = 'uploads/offices/' . $filename;
    }

    // Update office
    $stmt = $conn->prepare("
        UPDATE offices SET 
            office_name = ?, office_type = ?, region_id = ?, address = ?, 
            latitude = ?, longitude = ?, contact_number = ?, email = ?, 
            office_head = ?, description = ?, services_offered = ?, 
            office_hours = ?, image_path = ?
        WHERE office_id = ?
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
        $image_path,
        $office_id
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Office updated successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
