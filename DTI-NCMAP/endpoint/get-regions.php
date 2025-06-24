<?php
header('Content-Type: application/json');
include("../conn/conn.php");

try {
    $stmt = $conn->prepare("SELECT region_id, region_name FROM regions ORDER BY region_name");
    $stmt->execute();
    $regions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'regions' => $regions
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
