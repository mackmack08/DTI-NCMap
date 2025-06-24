<?php
header('Content-Type: application/json');
include("../conn/conn.php");

try {
    $stmt = $conn->prepare("
        SELECT o.*, r.region_name 
        FROM offices o 
        LEFT JOIN regions r ON o.region_id = r.region_id 
        ORDER BY o.office_name
    ");
    $stmt->execute();
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'offices' => $offices
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
