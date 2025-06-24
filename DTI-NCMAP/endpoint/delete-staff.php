<?php
header('Content-Type: application/json');
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Staff ID required']);
    exit;
}

try {
    $staff_id = $input['staff_id'];
    
    // Get staff data for file cleanup
    $stmt = $conn->prepare("SELECT photo_path FROM staff WHERE staff_id = ?");
    $stmt->execute([$staff_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$staff) {
        throw new Exception('Staff not found');
    }
    
    // Delete photo file if exists
    if ($staff['photo_path'] && file_exists('../' . $staff['photo_path'])) {
        unlink('../' . $staff['photo_path']);
    }
    
    // Delete staff record
    $stmt = $conn->prepare("DELETE FROM staff WHERE staff_id = ?");
    $stmt->execute([$staff_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Staff member deleted successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
