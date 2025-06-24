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
    // Get office ID from POST or JSON
    $input = json_decode(file_get_contents('php://input'), true);
    $office_id = $input['office_id'] ?? $_POST['office_id'] ?? null;

    if (empty($office_id)) {
        echo json_encode(['success' => false, 'message' => 'Office ID is required']);
        exit();
    }

    $office_id = intval($office_id);

    // Get office details before deletion
    $office_sql = "SELECT office_name, image_path FROM offices WHERE id = ?";
    $office_stmt = $pdo->prepare($office_sql);
    $office_stmt->execute([$office_id]);
    $office = $office_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$office) {
        echo json_encode(['success' => false, 'message' => 'Office not found']);
        exit();
    }

    // Check if there are staff members assigned to this office
    $staff_check_sql = "SELECT COUNT(*) as staff_count FROM staff WHERE office_id = ?";
    $staff_check_stmt = $pdo->prepare($staff_check_sql);
    $staff_check_stmt->execute([$office_id]);
    $staff_count = $staff_check_stmt->fetch(PDO::FETCH_ASSOC)['staff_count'];

    if ($staff_count > 0) {
        echo json_encode([
            'success' => false, 
            'message' => "Cannot delete office. There are {$staff_count} staff member(s) assigned to this office. Please reassign or remove them first."
        ]);
        exit();
    }

    // Begin transaction
    $pdo->beginTransaction();

    try {
        // Delete the office
        $delete_sql = "DELETE FROM offices WHERE id = ?";
        $delete_stmt = $pdo->prepare($delete_sql);
        $result = $delete_stmt->execute([$office_id]);

        if ($result) {
            // Log the action
            $log_sql = "INSERT INTO activity_logs (user_id, action, table_name, record_id, details, created_at) 
                       VALUES (?, 'DELETE', 'offices', ?, ?, NOW())";
            $log_stmt = $pdo->prepare($log_sql);
            $log_stmt->execute([
                $_SESSION['user_id'],
                $office_id,
                "Deleted office: " . $office['office_name']
            ]);

            // Delete associated image file
            if ($office['image_path'] && file_exists('../' . $office['image_path'])) {
                unlink('../' . $office['image_path']);
            }

            $pdo->commit();
            echo json_encode([
                'success' => true, 
                'message' => 'Office deleted successfully!'
            ]);
        } else {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Failed to delete office']);
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    error_log("Database error in delete-office.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message
