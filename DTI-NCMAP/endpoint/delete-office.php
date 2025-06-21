<?php
header('Content-Type: application/json');
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['office_id'])) {
            throw new Exception("Office ID is required");
        }
        
        $office_id = $input['office_id'];
        
        // Get office details first to delete associated files
        $stmt = $conn->prepare("SELECT image_path FROM tbl_dti_offices WHERE office_id = :office_id");
        $stmt->execute([':office_id' => $office_id]);
        $office = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$office) {
            throw new Exception("Office not found");
        }
        
        // Delete the office record (soft delete)
        $stmt = $conn->prepare("UPDATE tbl_dti_offices SET is_active = 0 WHERE office_id = :office_id");
        $stmt->execute([':office_id' => $office_id]);
        
        // Delete associated image file
        if ($office['image_path'] && file_exists('../' . $office['image_path'])) {
            unlink('../' . $office['image_path']);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'DTI office deleted successfully'
        ]);

    } catch (Exception $e) {
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
