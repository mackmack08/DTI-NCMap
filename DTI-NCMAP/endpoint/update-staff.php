<?php
header('Content-Type: application/json');
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Validate required fields
    if (empty($_POST['staff_id']) || empty($_POST['staff_name']) || empty($_POST['position'])) {
        throw new Exception('Staff ID, name, and position are required');
    }

    $staff_id = $_POST['staff_id'];
    
    // Get current staff data
    $stmt = $conn->prepare("SELECT photo_path FROM staff WHERE staff_id = ?");
    $stmt->execute([$staff_id]);
    $current_staff = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$current_staff) {
        throw new Exception('Staff not found');
    }
    
    $photo_path = $current_staff['photo_path'];

    // Handle photo upload
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
        
        // Delete old photo if exists
        if ($photo_path && file_exists('../' . $photo_path)) {
            unlink('../' . $photo_path);
        }
        
        $filename = uniqid() . '.' . $file_extension;
        $new_photo_path = $upload_dir . $filename;
        
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $new_photo_path)) {
            throw new Exception('Failed to upload photo');
        }
        
        $photo_path = 'uploads/staff/' . $filename;
    }

    // Update staff member
    $stmt = $conn->prepare("
        UPDATE staff SET 
            staff_name = ?, position = ?, staff_type = ?, contact_number = ?,
            email = ?, services_offered = ?, bio = ?, photo_path = ?
        WHERE staff_id = ?
    ");
    
    $stmt->execute([
        $_POST['staff_name'],
        $_POST['position'],
        $_POST['staff_type'] ?? 'Regular',
        $_POST['contact_number'] ?? null,
        $_POST['email'] ?? null,
        $_POST['services_offered'] ?? null,
        $_POST['bio'] ?? null,
        $photo_path,
        $staff_id
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Staff member updated successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
