<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

require_once '../config/database.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $name = $input['name'] ?? '';
    $description = $input['description'] ?? '';
    
    if (empty(trim($name))) {
        http_response_code(400);
        echo json_encode(['error' => 'Team name is required']);
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();
    
    // Check if team name already exists for this user
    $check_query = "SELECT id FROM teams WHERE name = ? AND created_by = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([trim($name), $_SESSION['user_id']]);
    
    if ($check_stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Team with this name already exists']);
        exit();
    }
    
    // Create team
    $team_query = "INSERT INTO teams (name, description, created_by, created_at) VALUES (?, ?, ?, NOW())";
    $team_stmt = $db->prepare($team_query);
    $team_stmt->execute([trim($name), trim($description), $_SESSION['user_id']]);
    
    $team_id = $db->lastInsertId();
    
    // Add creator as team admin
    $member_query = "INSERT INTO team_members (team_id, user_id, role, added_by, created_at) VALUES (?, ?, 'admin', ?, NOW())";
    $member_stmt = $db->prepare($member_query);
    $member_stmt->execute([$team_id, $_SESSION['user_id'], $_SESSION['user_id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Team created successfully',
        'team_id' => $team_id,
        'team_name' => trim($name)
    ]);

} catch (Exception $e) {
    error_log("Create Team Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>