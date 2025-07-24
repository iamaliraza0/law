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
    $email = $input['email'] ?? '';
    $role = $input['role'] ?? 'member';
    $message = $input['message'] ?? '';
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Valid email address is required']);
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();
    
    // Check if user already exists
    $check_query = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$email]);
    
    if ($check_stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'User with this email already exists']);
        exit();
    }
    
    // Check if invitation already exists
    $invite_check = "SELECT id FROM user_invitations WHERE email = ? AND status = 'pending'";
    $invite_stmt = $db->prepare($invite_check);
    $invite_stmt->execute([$email]);
    
    if ($invite_stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Invitation already sent to this email']);
        exit();
    }
    
    // Create invitation record
    $invitation_token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    $invite_query = "INSERT INTO user_invitations (email, role, message, invitation_token, invited_by, expires_at, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $invite_insert = $db->prepare($invite_query);
    $invite_insert->execute([$email, $role, $message, $invitation_token, $_SESSION['user_id'], $expires_at]);
    
    // Send invitation email (mock implementation)
    $invitation_link = "https://" . $_SERVER['HTTP_HOST'] . "/accept-invitation.php?token=" . $invitation_token;
    
    // In a real implementation, you would send an actual email here
    // For now, we'll just log it
    error_log("Invitation sent to $email with link: $invitation_link");
    
    echo json_encode([
        'success' => true,
        'message' => 'Invitation sent successfully',
        'invitation_link' => $invitation_link // For demo purposes
    ]);

} catch (Exception $e) {
    error_log("Invite User Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>