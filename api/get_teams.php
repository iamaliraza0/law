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
    
    // Get teams where user is a member
    $query = "SELECT t.*, u.name as created_by_name, tm.role,
                     (SELECT COUNT(*) FROM team_members WHERE team_id = t.id) as member_count
              FROM teams t 
              LEFT JOIN users u ON t.created_by = u.id 
              LEFT JOIN team_members tm ON t.id = tm.team_id AND tm.user_id = ?
              WHERE tm.user_id = ?
              ORDER BY t.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'teams' => $teams
    ]);

} catch (Exception $e) {
    error_log("Get Teams Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>