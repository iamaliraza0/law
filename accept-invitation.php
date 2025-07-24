<?php
session_start();
require_once 'config/database.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    $error = 'Invalid invitation link';
} else {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if invitation exists and is valid
    $query = "SELECT * FROM user_invitations WHERE invitation_token = ? AND status = 'pending' AND expires_at > NOW()";
    $stmt = $db->prepare($query);
    $stmt->execute([$token]);
    $invitation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$invitation) {
        $error = 'Invalid or expired invitation';
    }
}

if ($_POST && !$error) {
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($password)) {
        $error = 'Name and password are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        try {
            // Create user account
            $user_query = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
            $user_stmt = $db->prepare($user_query);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $user_stmt->execute([$name, $invitation['email'], $hashed_password]);
            
            $user_id = $db->lastInsertId();
            
            // Update invitation status
            $update_query = "UPDATE user_invitations SET status = 'accepted', accepted_at = NOW() WHERE id = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->execute([$invitation['id']]);
            
            // Log in the new user
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $invitation['email'];
            
            header('Location: index.php');
            exit();
            
        } catch (Exception $e) {
            $error = 'Error creating account: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation - PocketLegal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">PocketLegal</h1>
            <h2 class="text-xl text-gray-600">Accept Invitation</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (!$error && $invitation): ?>
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
                <p class="text-sm">
                    <strong>You've been invited to join PocketLegal!</strong><br>
                    Email: <?php echo htmlspecialchars($invitation['email']); ?><br>
                    Role: <?php echo ucfirst($invitation['role']); ?>
                </p>
                <?php if ($invitation['message']): ?>
                    <p class="text-sm mt-2 italic">"<?php echo htmlspecialchars($invitation['message']); ?>"</p>
                <?php endif; ?>
            </div>

            <form method="POST" class="mt-8 space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input id="name" name="name" type="text" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your full name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Create a password (min 6 characters)">
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input id="confirm_password" name="confirm_password" type="password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Confirm your password">
                </div>

                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </button>
            </form>
        <?php endif; ?>

        <div class="text-center">
            <a href="login.php" class="text-sm text-blue-600 hover:text-blue-500">
                Already have an account? Sign in
            </a>
        </div>
    </div>
</body>
</html>