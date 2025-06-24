<?php
header('Content-Type: application/json');
include("../conn/conn.php");

if (!isset($_GET['office_id'])) {
    echo json_encode(['success' => false, 'message' => 'Office ID required']);
    exit;
}

try {
    $office_id = $_GET['office_id'];
    
    // Get office details
    $stmt = $conn->prepare("
        SELECT o.*, r.region_name 
        FROM offices o 
        LEFT JOIN regions r ON o.region_id = r.region_id 
        WHERE o.office_id = ?
    ");
    $stmt->execute([$office_id]);
    $office = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$office) {
        echo json_encode(['success' => false, 'message' => 'Office not found']);
        exit;
    }
    
    // Get services
    if ($office['services_offered']) {
        $office['services'] = array_map('trim', explode(',', $office['services_offered']));
    } else {
        $office['services'] = [];
    }
    
    // Get staff
    $stmt = $conn->prepare("
        SELECT * FROM staff 
        WHERE office_id = ? 
        ORDER BY staff_name
    ");
    $stmt->execute([$office_id]);
    $office['staff'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'office' => $office
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
