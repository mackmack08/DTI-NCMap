<?php
header('Content-Type: application/json');
include("../conn/conn.php");

if (!isset($_GET['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Staff ID required']);
    exit;
}

try {
    $staff_id = $_GET['staff_id'];
    
    $stmt = $conn->prepare("
        SELECT s.*, o.office_name 
        FROM staff s 
        LEFT JOIN offices o ON s.office_id = o.office_id 
        WHERE s.staff_id = ?
    ");
    $stmt->execute([$staff_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$staff) {
        echo json_encode(['success' => false, 'message' => 'Staff not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'staff' => $staff
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
