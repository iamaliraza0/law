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
    $template_id = $input['template_id'] ?? '';
    $document_name = $input['document_name'] ?? '';
    $content = $input['content'] ?? '';
    
    if (empty($template_id) || empty($document_name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Template ID and document name are required']);
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();
    
    // Create user template directory if it doesn't exist
    $user_template_dir = '../templates/user_' . $_SESSION['user_id'];
    if (!file_exists($user_template_dir)) {
        mkdir($user_template_dir, 0777, true);
    }
    
    // Generate filename
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document_name) . '_' . time() . '.html';
    $file_path = $user_template_dir . '/' . $filename;
    
    // Save template content to file
    if (!empty($content)) {
        file_put_contents($file_path, $content);
    } else {
        // Get default template content based on template_id
        $default_content = getDefaultTemplateContent($template_id);
        file_put_contents($file_path, $default_content);
    }
    
    // Save template record to database
    $template_query = "INSERT INTO user_templates (user_id, template_id, name, file_path, created_at) VALUES (?, ?, ?, ?, NOW())";
    $template_stmt = $db->prepare($template_query);
    $template_stmt->execute([$_SESSION['user_id'], $template_id, $document_name, $file_path]);
    
    $user_template_id = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Template saved successfully',
        'user_template_id' => $user_template_id,
        'file_path' => $file_path
    ]);

} catch (Exception $e) {
    error_log("Use Template Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

function getDefaultTemplateContent($template_id) {
    $templates = [
        '1' => '
            <h1>EMPLOYMENT AGREEMENT</h1>
            <p>This Employment Agreement ("Agreement") is entered into on [DATE] between [COMPANY_NAME] ("Company") and [EMPLOYEE_NAME] ("Employee").</p>
            
            <h2>1. POSITION AND DUTIES</h2>
            <p>Employee shall serve as [POSITION_TITLE] and shall perform duties as assigned by the Company.</p>
            
            <h2>2. COMPENSATION</h2>
            <p>Employee shall receive a salary of $[SALARY_AMOUNT] per year, payable in accordance with Company payroll practices.</p>
            
            <h2>3. BENEFITS</h2>
            <p>Employee shall be entitled to participate in Company benefit programs as may be available to employees.</p>
            
            <h2>4. TERMINATION</h2>
            <p>This Agreement may be terminated by either party with [NOTICE_PERIOD] days written notice.</p>
        ',
        '2' => '
            <h1>NON-DISCLOSURE AGREEMENT</h1>
            <p>This Non-Disclosure Agreement ("Agreement") is entered into on [DATE] between [PARTY_1] and [PARTY_2].</p>
            
            <h2>1. CONFIDENTIAL INFORMATION</h2>
            <p>For purposes of this Agreement, "Confidential Information" means any and all information disclosed by either party.</p>
            
            <h2>2. OBLIGATIONS</h2>
            <p>Each party agrees to maintain the confidentiality of all Confidential Information received.</p>
            
            <h2>3. TERM</h2>
            <p>This Agreement shall remain in effect for [TERM_YEARS] years from the date of execution.</p>
        ',
        '3' => '
            <h1>SERVICE AGREEMENT</h1>
            <p>This Service Agreement ("Agreement") is entered into on [DATE] between [SERVICE_PROVIDER] and [CLIENT].</p>
            
            <h2>1. SERVICES</h2>
            <p>Service Provider agrees to provide the following services: [SERVICE_DESCRIPTION]</p>
            
            <h2>2. COMPENSATION</h2>
            <p>Client agrees to pay Service Provider $[AMOUNT] for the services described herein.</p>
            
            <h2>3. DELIVERABLES</h2>
            <p>Service Provider shall deliver [DELIVERABLES] by [DELIVERY_DATE].</p>
        '
    ];
    
    return $templates[$template_id] ?? '<h1>LEGAL DOCUMENT TEMPLATE</h1><p>Template content goes here...</p>';
}
?>