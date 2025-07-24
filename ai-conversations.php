<?php
include_once 'header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$conversation_id = $_GET['id'] ?? null;
$user_name = $_SESSION['user_name'] ?? 'User';

if ($conversation_id) {
    // Get specific conversation
    $conv_query = "SELECT * FROM ai_conversations WHERE id = ? AND user_id = ?";
    $conv_stmt = $db->prepare($conv_query);
    $conv_stmt->execute([$conversation_id, $_SESSION['user_id']]);
    $conversation = $conv_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$conversation) {
        header('Location: index.php');
        exit();
    }
    
    // Get messages
    $msg_query = "SELECT * FROM ai_messages WHERE conversation_id = ? ORDER BY created_at ASC";
    $msg_stmt = $db->prepare($msg_query);
    $msg_stmt->execute([$conversation_id]);
    $messages = $msg_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all conversations for sidebar
$all_conv_query = "SELECT * FROM ai_conversations WHERE user_id = ? ORDER BY updated_at DESC";
$all_conv_stmt = $db->prepare($all_conv_query);
$all_conv_stmt->execute([$_SESSION['user_id']]);
$all_conversations = $all_conv_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto flex">
        <!-- Conversations Sidebar -->
        <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">AI Conversations</h2>
                    <button onclick="startNewConversation()" class="text-blue-600 hover:text-blue-700">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                <?php if (empty($all_conversations)): ?>
                    <div class="p-4 text-center">
                        <i class="fas fa-robot text-3xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-sm">No conversations yet</p>
                        <button onclick="startNewConversation()" class="text-blue-600 hover:text-blue-700 text-sm mt-2">
                            Start your first AI review
                        </button>
                    </div>
                <?php else: ?>
                    <div class="space-y-1 p-2">
                        <?php foreach ($all_conversations as $conv): ?>
                            <a href="?id=<?php echo $conv['id']; ?>" 
                               class="block p-3 rounded-lg hover:bg-gray-50 <?php echo ($conversation_id == $conv['id']) ? 'bg-blue-50 border-l-4 border-blue-500' : ''; ?>">
                                <h4 class="font-medium text-gray-800 text-sm truncate"><?php echo htmlspecialchars($conv['title']); ?></h4>
                                <p class="text-xs text-gray-500 mt-1"><?php echo date('M j, g:i A', strtotime($conv['updated_at'])); ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col">
            <?php if ($conversation_id && $conversation): ?>
                <!-- Chat Header -->
                <div class="bg-white border-b px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($conversation['title']); ?></h1>
                            <p class="text-sm text-gray-500">Started <?php echo date('M j, Y g:i A', strtotime($conversation['created_at'])); ?></p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="exportConversation(<?php echo $conversation_id; ?>)" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-download"></i>
                            </button>
                            <button onclick="deleteConversation(<?php echo $conversation_id; ?>)" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <?php foreach ($messages as $message): ?>
                        <div class="flex <?php echo $message['message_type'] === 'user' ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-3xl <?php echo $message['message_type'] === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800'; ?> rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas <?php echo $message['message_type'] === 'user' ? 'fa-user' : 'fa-robot'; ?> mr-2"></i>
                                    <span class="font-medium text-sm">
                                        <?php echo $message['message_type'] === 'user' ? 'You' : 'AI Assistant'; ?>
                                    </span>
                                    <span class="ml-auto text-xs opacity-75">
                                        <?php echo date('g:i A', strtotime($message['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="prose prose-sm max-w-none <?php echo $message['message_type'] === 'user' ? 'prose-invert' : ''; ?>">
                                    <?php echo nl2br(htmlspecialchars($message['content'])); ?>
                                </div>
                                <?php if ($message['document_name']): ?>
                                    <div class="mt-2 text-xs opacity-75">
                                        <i class="fas fa-paperclip mr-1"></i>
                                        <?php echo htmlspecialchars($message['document_name']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- New Message Form -->
                <div class="bg-white border-t p-4">
                    <form id="newMessageForm" class="flex space-x-3">
                        <input type="hidden" name="conversation_id" value="<?php echo $conversation_id; ?>">
                        <div class="flex-1">
                            <textarea name="message" id="messageInput" rows="2" 
                                    class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" 
                                    placeholder="Ask a follow-up question about this contract..."></textarea>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 self-end">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- No Conversation Selected -->
                <div class="flex-1 flex items-center justify-center bg-gray-50">
                    <div class="text-center">
                        <i class="fas fa-robot text-6xl text-gray-300 mb-4"></i>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">AI Contract Review</h2>
                        <p class="text-gray-600 mb-6">Select a conversation or start a new AI review</p>
                        <button onclick="startNewConversation()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>Start New Review
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('hidden');
    }

    function startNewConversation() {
        window.location.href = 'index.php';
        setTimeout(() => {
            if (typeof openAIModal === 'function') {
                openAIModal();
            }
        }, 100);
    }

    function deleteConversation(id) {
        if (confirm('Are you sure you want to delete this conversation?')) {
            fetch('api/delete_conversation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ conversation_id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'ai-conversations.php';
                } else {
                    alert('Error deleting conversation: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting conversation');
            });
        }
    }

    function exportConversation(id) {
        // This would export the conversation as PDF or text
        alert('Export functionality will be implemented in the next phase.');
    }

    // Handle new message form
    <?php if ($conversation_id): ?>
    document.getElementById('newMessageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();
        
        if (!message) return;
        
        // Add user message to chat immediately
        addMessageToChat('user', message);
        messageInput.value = '';
        
        // Send to AI
        fetch('api/ai_query.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                conversation_id: <?php echo $conversation_id; ?>,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addMessageToChat('assistant', data.response);
            } else {
                addMessageToChat('assistant', 'Sorry, I encountered an error processing your request.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            addMessageToChat('assistant', 'Sorry, I encountered an error processing your request.');
        });
    });

    function addMessageToChat(type, content) {
        const messagesContainer = document.querySelector('.space-y-6');
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${type === 'user' ? 'justify-end' : 'justify-start'}`;
        
        const now = new Date();
        const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        messageDiv.innerHTML = `
            <div class="max-w-3xl ${type === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800'} rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas ${type === 'user' ? 'fa-user' : 'fa-robot'} mr-2"></i>
                    <span class="font-medium text-sm">${type === 'user' ? 'You' : 'AI Assistant'}</span>
                    <span class="ml-auto text-xs opacity-75">${timeString}</span>
                </div>
                <div class="prose prose-sm max-w-none ${type === 'user' ? 'prose-invert' : ''}">
                    ${content.replace(/\n/g, '<br>')}
                </div>
            </div>
        `;
        
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    <?php endif; ?>
</script>