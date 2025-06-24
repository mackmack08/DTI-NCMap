<?php
header('Content-Type: application/json');
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['office_id'])) {
    echo json_encode(['success' => false, 'message' => 'Office ID required']);
    exit;
}

try {
    $office_id = $input['office_id'];
    
    // Get office data for file cleanup
    $stmt = $conn->prepare("SELECT image_path FROM offices WHERE office_id = ?");
    $stmt->execute([$office_id]);
    $office = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$office) {
        throw new Exception('Office not found');
    }
    
    // Start transaction
    $conn->beginTransaction();
    
    // Delete staff photos
    $stmt = $conn->prepare("SELECT photo_path FROM staff WHERE office_id = ?");
    $stmt->execute([$office_id]);
    $staff_photos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($staff_photos as $photo_path) {
        if ($photo_path && file_exists('../' . $photo_path)) {
            unlink('../' . $photo_path);
        }
    }
    
    // Delete staff records
    $stmt = $conn->prepare("DELETE FROM staff WHERE office_id = ?");
    $stmt->execute([$office_id]);
    
    // Delete office image
    if ($office['image_path'] && file_exists('../' . $office['image_path'])) {
        unlink('../' . $office['image_path']);
    }
    
    // Delete office record
    $stmt = $conn->prepare("DELETE FROM offices WHERE office_id = ?");
    $stmt->execute([$office_id]);
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Office deleted successfully'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
