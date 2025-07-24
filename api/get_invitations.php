<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get pending invitations
    $query = "SELECT i.*, u.name as invited_by_name 
              FROM user_invitations i 
              LEFT JOIN users u ON i.invited_by = u.id 
              WHERE i.invited_by = ? 
              ORDER BY i.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $invitations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'invitations' => $invitations
    ]);

} catch (Exception $e) {
    error_log("Get Invitations Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>