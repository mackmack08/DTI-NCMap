<?php
header('Content-Type: application/json');
include("../conn/conn.php");

try {
    $search_term = $_GET['q'] ?? '';
    $region_id = $_GET['region_id'] ?? '';
    $office_type = $_GET['office_type'] ?? '';
    
    $sql = "
        SELECT 
            o.*,
            r.region_name,
            r.region_code,
            r.is_ncr,
            r.region_description
        FROM tbl_dti_offices o
        LEFT JOIN tbl_regions r ON o.region_id = r.region_id
        WHERE o.is_active = 1
    ";
    
    $params = [];
    
    if (!empty($search_term)) {
        $sql .= " AND (
            o.office_name LIKE :search_term OR 
            o.address LIKE :search_term OR 
            r.region_name LIKE :search_term OR
            o.services_offered LIKE :search_term
        )";
        $params[':search_term'] = '%' . $search_term . '%';
    }
    
    if (!empty($region_id)) {
        $sql .= " AND o.region_id = :region_id";
        $params[':region_id'] = $region_id;
    }
    
    if (!empty($office_type)) {
        $sql .= " AND o.office_type = :office_type";
        $params[':office_type'] = $office_type;
    }
    
    $sql .= " ORDER BY o.office_name ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
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
        'count' => count($offices),
        'search_params' => [
            'search_term' => $search_term,
            'region_id' => $region_id,
            'office_type' => $office_type
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
