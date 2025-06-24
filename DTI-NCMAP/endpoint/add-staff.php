<?php
header('Content-Type: application/json');
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Validate required fields
    if (empty($_POST['staff_name']) || empty($_POST['position']) || empty($_POST['office_id'])) {
        throw new Exception('Staff name, position, and office are required');
    }

    // Handle photo upload
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/staff/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        
                if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception('Invalid file type. Only JPG, JPEG, and PNG are allowed.');
        }
        
        $filename = uniqid() . '.' . $file_extension;
        $photo_path = $upload_dir . $filename;
        
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            throw new Exception('Failed to upload photo');
        }
        
        $photo_path = 'uploads/staff/' . $filename; // Store relative path
    }

    // Insert staff member
    $stmt = $conn->prepare("
        INSERT INTO staff (
            office_id, staff_name, position, staff_type, contact_number,
            email, services_offered, bio, photo_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_POST['office_id'],
        $_POST['staff_name'],
        $_POST['position'],
        $_POST['staff_type'] ?? 'Regular',
        $_POST['contact_number'] ?? null,
        $_POST['email'] ?? null,
        $_POST['services_offered'] ?? null,
        $_POST['bio'] ?? null,
        $photo_path
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Staff member added successfully',
        'staff_id' => $conn->lastInsertId()
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
