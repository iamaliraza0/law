<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

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
    $message = $input['message'] ?? '';
    $conversation_id = $input['conversation_id'] ?? null;
    
    if (empty(trim($message))) {
        http_response_code(400);
        echo json_encode(['error' => 'Message is required']);
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();
    
    // Verify conversation belongs to user
    if ($conversation_id) {
        $conv_check = "SELECT id FROM ai_conversations WHERE id = ? AND user_id = ?";
        $conv_stmt = $db->prepare($conv_check);
        $conv_stmt->execute([$conversation_id, $_SESSION['user_id']]);
        if (!$conv_stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['error' => 'Conversation not found']);
            exit();
        }
    }
    
    // Send query to n8n webhook
    $query_url = 'https://n8n.srv909751.hstgr.cloud/webhook/query';
    $webhook_data = [
        'message' => trim($message),
        'user_id' => $_SESSION['user_id'],
        'conversation_id' => $conversation_id,
        'timestamp' => date('c'),
        'type' => 'follow_up_query'
    ];
    
    $max_retries = 3;
    $retry_delay = 2;
    $ai_response = '';
    $query_success = false;
    
    for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
        error_log("AI Query attempt $attempt/$max_retries - URL: $query_url");
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $query_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhook_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: PocketLegal/1.0',
            'Accept: application/json',
            'Cache-Control: no-cache'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        error_log("AI Query attempt $attempt - HTTP Code: $http_code, Error: $curl_error");
        
        if (!$curl_error && $http_code >= 200 && $http_code < 300 && !empty($response)) {
            $query_success = true;
            
            $webhook_result = json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE && $webhook_result) {
                if (isset($webhook_result['response'])) {
                    $ai_response = $webhook_result['response'];
                } elseif (isset($webhook_result['message'])) {
                    $ai_response = $webhook_result['message'];
                } elseif (isset($webhook_result['data'])) {
                    $ai_response = is_string($webhook_result['data']) ? $webhook_result['data'] : json_encode($webhook_result['data'], JSON_PRETTY_PRINT);
                } else {
                    $ai_response = json_encode($webhook_result, JSON_PRETTY_PRINT);
                }
            } else {
                $ai_response = trim($response);
            }
            
            error_log("AI Query success! Response length: " . strlen($ai_response));
            break;
        }
        
        if ($attempt < $max_retries) {
            error_log("AI Query retry $attempt/$max_retries failed, retrying after $retry_delay seconds");
            sleep($retry_delay);
        }
    }
    
    if (!$query_success || empty($ai_response)) {
        // Fallback response
        $ai_response = "I apologize, but I'm currently unable to process your request due to a temporary service issue. Please try again in a few moments.";
        error_log("AI Query failed after $max_retries attempts, using fallback response");
    }
    
    // Save messages to database
    if ($conversation_id) {
        $msg_query = "INSERT INTO ai_messages (conversation_id, user_id, message_type, content, created_at) VALUES (?, ?, ?, ?, NOW())";
        $msg_stmt = $db->prepare($msg_query);
        
        // Save user message
        $msg_stmt->execute([$conversation_id, $_SESSION['user_id'], 'user', $message]);
        
        // Save AI response
        $msg_stmt->execute([$conversation_id, $_SESSION['user_id'], 'assistant', $ai_response]);
        
        // Update conversation timestamp
        $update_conv = "UPDATE ai_conversations SET updated_at = NOW() WHERE id = ?";
        $update_stmt = $db->prepare($update_conv);
        $update_stmt->execute([$conversation_id]);
    }

    echo json_encode([
        'success' => true,
        'response' => $ai_response,
        'message' => $message,
        'conversation_id' => $conversation_id,
        'webhook_success' => $query_success
    ]);

} catch (Exception $e) {
    error_log("AI Query Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error occurred while processing your request',
        'details' => $e->getMessage()
    ]);
}
?>