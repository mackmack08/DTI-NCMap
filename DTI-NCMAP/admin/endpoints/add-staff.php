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
    // Validate staff ID
    if (empty($_POST['staff_id'])) {
        echo json_encode(['success' => false, 'message' => 'Staff ID is required']);
        exit();
    }

    $staff_id = intval($_POST['staff_id']);

    // Check if staff exists
    $check_sql = "SELECT id, photo_path FROM staff WHERE id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$staff_id]);
    $existing_staff = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing_staff) {
        echo json_encode(['success' => false, 'message' => 'Staff member not found']);
        exit();
    }

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
    $photo_path = $existing_staff['photo_path']; // Keep existing photo by default
    
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
            // Delete old photo if it exists
            if ($existing_staff['photo_path'] && file_exists('../' . $existing_staff['photo_path'])) {
                unlink('../' . $existing_staff['photo_path']);
            }
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

    // Update database
    $sql = "UPDATE staff SET 
        office_id = ?, staff_name = ?, position = ?, department = ?, 
        phone_number = ?, email = ?, services_offered = ?, bio = ?, 
        photo_path = ?, employee_id = ?, hire_date = ?, updated_at = NOW()
        WHERE id = ?";

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
        $_POST['hire_date'] ?? null,
        $staff_id
    ]);

    if
