<?php
header('Content-Type: application/json');
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['office_image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                throw new Exception("Invalid file type. Only JPG, PNG, GIF, and WebP files are allowed.");
            }
            
            // Generate unique filename
            $filename = 'office_' . uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['office_image']['tmp_name'], $target_path)) {
                $image_path = 'uploads/offices/' . $filename;
            } else {
                throw new Exception("Failed to upload image");
            }
        }

        // Insert office data
        $stmt = $conn->prepare("
            INSERT INTO tbl_dti_offices (
                office_name, office_type, region_id, address, contact_number, 
                email, office_head, latitude, longitude, description, 
                services_offered, office_hours, image_path
            ) VALUES (
                :office_name, :office_type, :region_id, :address, :contact_number,
                :email, :office_head, :latitude, :longitude, :description,
                :services_offered, :office_hours, :image_path
            )
        ");

        $stmt->execute([
            ':office_name' => $_POST['office_name'],
            ':office_type' => $_POST['office_type'],
            ':region_id' => $_POST['region_id'],
            ':address' => $_POST['address'],
            ':contact_number' => $_POST['contact_number'] ?? null,
            ':email' => $_POST['email'] ?? null,
            ':office_head' => $_POST['office_head'] ?? null,
            ':latitude' => $_POST['latitude'],
            ':longitude' => $_POST['longitude'],
            ':description' => $_POST['description'] ?? null,
            ':services_offered' => $_POST['services_offered'] ?? null,
            ':office_hours' => $_POST['office_hours'] ?? '8:00 AM - 5:00 PM',
            ':image_path' => $image_path
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'DTI office added successfully',
            'office_id' => $conn->lastInsertId()
        ]);

    } catch (Exception $e) {
        // Clean up uploaded file if database insert fails
        if (isset($target_path) && file_exists($target_path)) {
            unlink($target_path);
        }
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
