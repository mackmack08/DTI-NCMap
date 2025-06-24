<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    // Validate required fields
    $required_fields = ['staff_name', 'position', 'office_id'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            exit();
        }
    }

    // Validate office exists
    $office_check_sql = "SELECT id FROM offices WHERE id = ?";
    $office_check_stmt = $pdo->prepare($office_check_sql);
    $office_check_stmt->execute([$_POST['office_id']]);
    
    if (!$office_check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Selected office does not exist']);
        exit();
    }

    // Validate email format if provided
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }

    // Handle file upload
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/staff/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
            exit();
        }

        // Check file size (2MB max for staff photos)
        if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 2MB allowed for staff photos.']);
            exit();
        }

        $filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
            $photo_path = 'uploads/staff/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload photo']);
            exit();
        }
    }

    // Process services array
    $services = '';
    if (!empty($_POST['services'])) {
        if (is_array($_POST['services'])) {
            $services = implode(', ', $_POST['services']);
        } else {
            $services = $_POST['services'];
        }
    }

    // Insert into database
    $sql = "INSERT INTO staff (
        office_id, staff_name, position, department, phone_number, 
        email, services_offered, bio, photo_path, employee_id, 
        hire_date, status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        intval($_POST['office_id']),
        $_POST['staff_name'],
        $_POST['position'],
        $_POST['department'] ?? '',
        $_POST['phone_number'] ?? '',
        $_POST['email'] ?? '',
        $services,
        $_POST['bio'] ?? '',
        $photo_path,
        $_POST['employee_id'] ?? '',
        $_POST['hire_date'] ?? null
    ]);

    if ($result) {
        $staff_id = $pdo->lastInsertId();
        
        // Log the action
        $log_sql = "INSERT INTO activity_logs (user_id, action, table_name, record_id, details, created_at) 
                   VALUES (?, 'CREATE', 'staff', ?, ?, NOW())";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([
            $_SESSION['user_id'],
            $staff_id,
            "Added new staff member: " . $_POST['staff_name']
        ]);

        echo json_encode([
            'success' => true, 
            'message' => 'Staff member added successfully!',
            'staff_id' => $staff_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add staff member']);
    }

} catch (PDOException $e) {
    error_log("Database error in add-staff.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("General error in add-staff.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while adding the staff member']);
}
?>
