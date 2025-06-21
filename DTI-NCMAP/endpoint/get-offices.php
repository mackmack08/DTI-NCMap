<?php
header('Content-Type: application/json');
include("../conn/conn.php");

try {
    $stmt = $conn->prepare("
        SELECT 
            o.*,
            r.region_name,
            r.region_code,
            r.is_ncr,
            r.region_description
        FROM tbl_dti_offices o
        LEFT JOIN tbl_regions r ON o.region_id = r.region_id
        WHERE o.is_active = 1
        ORDER BY o.office_name ASC
    ");
    
    $stmt->execute();
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert numeric strings to proper types
    foreach ($offices as &$office) {
        $office['latitude'] = (float) $office['latitude'];
        $office['longitude'] = (float) $office['longitude'];
        $office['office_id'] = (int) $office['office_id'];
        $office['region_id'] = (int) $office['region_id'];
        $office['is_ncr'] = (bool) $office['is_ncr'];
        $office['is_active'] = (bool) $office['is_active'];
    }
    
    echo json_encode([
        'success' => true,
        'offices' => $offices,
        'count' => count($offices)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
