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
    // Validate office ID
    if (empty($_POST['office_id'])) {
        echo json_encode(['success' => false, 'message' => 'Office ID is required']);
        exit();
    }

    $office_id = intval($_POST['office_id']);

    // Check if office exists
    $check_sql = "SELECT id, image_path FROM offices WHERE id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$office_id]);
    $existing_office = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing_office) {
        echo json_encode(['success' => false, 'message' => 'Office not found']);
        exit();
    }

    // Validate required fields
    $required_fields = ['office_name', 'office_type', 'region', 'province', 'city', 'latitude', 'longitude'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            exit();
        }
    }

    // Handle file upload
    $image_path = $existing_office['image_path']; // Keep existing image by default
    
    if (isset($_FILES['office_image']) && $_FILES['office_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/offices/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['office_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
            exit();
        }

        // Check file size (5MB max)
        if ($_FILES['office_image']['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 5MB allowed.']);
            exit();
        }

        $filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['office_image']['tmp_name'], $target_path)) {
            // Delete old image if it exists
            if ($existing_office['image_path'] && file_exists('../' . $existing_office['image_path'])) {
                unlink('../' . $existing_office['image_path']);
            }
            $image_path = 'uploads/offices/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit();
        }
    }

    // Update database
    $sql = "UPDATE offices SET 
        office_name = ?, office_type = ?, region = ?, province = ?, city = ?, 
        barangay = ?, street_address = ?, postal_code = ?, latitude = ?, longitude = ?, 
        phone_number = ?, email_address = ?, website_url = ?, office_hours = ?, 
        services_offered = ?, head_official = ?, image_path = ?, updated_at = NOW()
        WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $_POST['office_name'],
        $_POST['office_type'],
        $_POST['region'],
        $_POST['province'],
        $_POST['city'],
        $_POST['barangay'] ?? '',
        $_POST['street_address'] ?? '',
        $_POST['postal_code'] ?? '',
        floatval($_POST['latitude']),
        floatval($_POST['longitude']),
        $_POST['phone_number'] ?? '',
        $_POST['email_address'] ?? '',
        $_POST['website_url'] ?? '',
        $_POST['office_hours'] ?? '',
        $_POST['services_offered'] ?? '',
        $_POST['head_official'] ?? '',
        $image_path,
        $office_id
    ]);

    if ($result) {
        // Log the action
        $log_sql = "INSERT INTO activity_logs (user_id, action, table_name, record_id, details, created_at) 
                   VALUES (?, 'UPDATE', 'offices', ?, ?, NOW())";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([
            $_SESSION['user_id'],
            $office_id,
            "Updated office: " . $_POST['office_name']
        ]);

        echo json_encode([
            'success' => true, 
            'message' => 'Office updated successfully!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update office']);
    }

} catch (PDOException $e) {
    error_log("Database error in edit-office.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("General error in edit-office.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating the office']);
}
?>
