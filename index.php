<?php
include_once 'header.php';
require_once 'config/database.php';
require_once 'classes/Document.php';

$database = new Database();
$db = $database->getConnection();
$document = new Document($db);

$user_documents = $document->getUserDocuments($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';

// Get AI conversations
$conv_query = "SELECT * FROM ai_conversations WHERE user_id = ? ORDER BY updated_at DESC LIMIT 5";
$conv_stmt = $db->prepare($conv_query);
$conv_stmt->execute([$_SESSION['user_id']]);
$recent_conversations = $conv_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
                    <p class="text-gray-600 mt-1">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openUploadModal()" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Upload Document
                    </button>
                    <button onclick="openAIModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-robot mr-2"></i>AI Contract Review
                    </button>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo count($user_documents); ?></p>
                            <p class="text-gray-600 text-sm">Total Documents</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-robot text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo count($recent_conversations); ?></p>
                            <p class="text-gray-600 text-sm">AI Reviews</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800">8</p>
                            <p class="text-gray-600 text-sm">Active Tasks</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800">3</p>
                            <p class="text-gray-600 text-sm">Pending Reviews</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- AI Contract Review -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">AI Contract Review</h2>
                        <button onclick="openAIModal()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Start New Review
                        </button>
                    </div>
                    
                    <?php if (empty($recent_conversations)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-robot text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No AI reviews yet</h3>
                            <p class="text-gray-500 mb-4">Upload a contract and get AI-powered insights</p>
                            <button onclick="openAIModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-robot mr-2"></i>Start AI Review
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recent_conversations as $conv): ?>
                                <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" onclick="viewConversation(<?php echo $conv['id']; ?>)">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($conv['title']); ?></h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <?php echo date('M j, Y g:i A', strtotime($conv['updated_at'])); ?>
                                            </p>
                                        </div>
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Documents -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Documents</h2>
                        <a href="documents.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
                    </div>
                    
                    <?php if (empty($user_documents)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No documents</h3>
                            <p class="text-gray-500 mb-4">Upload your first document</p>
                            <button onclick="openUploadModal()" class="text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-upload mr-2"></i>Upload Document
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach (array_slice($user_documents, 0, 5) as $doc): ?>
                                <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded-lg">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            <?php echo htmlspecialchars($doc['original_name']); ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo date('M j, Y', strtotime($doc['created_at'])); ?>
                                        </p>
                                    </div>
                                    <button onclick="downloadDocument(<?php echo $doc['id']; ?>)" class="text-gray-400 hover:text-blue-600">
                                        <i class="fas fa-download text-sm"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Contract Review Modal -->
<div id="aiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">AI Contract Review</h2>
                    <button onclick="closeAIModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="aiReviewForm" enctype="multipart/form-data">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Contract Document</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600 mb-3">Drag and drop your contract here, or click to select</p>
                            <input type="file" id="contractFile" name="document" accept=".pdf,.doc,.docx,.txt" class="hidden" onchange="handleFileSelect(event)">
                            <button type="button" onclick="document.getElementById('contractFile').click()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Select File
                            </button>
                            <div id="selectedFile" class="mt-3 text-sm text-gray-600 hidden"></div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Review Instructions</label>
                        <textarea id="aiInstructions" name="instructions" rows="4" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="What would you like me to review? For example: 'Check for potential risks and liability issues' or 'Review payment terms and conditions'"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAIModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" id="aiSubmitBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50" disabled>
                            <i class="fas fa-robot mr-2"></i>Start AI Review
                        </button>
                    </div>
                </form>
                
                <!-- AI Response Area -->
                <div id="aiResponse" class="hidden mt-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-robot text-blue-600 mr-2"></i>
                        <h3 class="font-semibold text-gray-800">AI Analysis</h3>
                    </div>
                    <div id="aiResponseContent" class="prose max-w-none text-sm text-gray-700"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-lg w-full">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Upload Documents</h2>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 mb-4">Drag and drop files here, or click to select</p>
                    <input type="file" id="fileInput" multiple class="hidden" onchange="handleUploadFileSelect(event)">
                    <button onclick="document.getElementById('fileInput').click()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Select Files
                    </button>
                </div>
                <div id="uploadFileList" class="mt-4 space-y-2"></div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeUploadModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button onclick="uploadFiles()" id="uploadBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50" disabled>
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedFiles = [];
    let selectedContractFile = null;

    function toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('hidden');
    }

    // AI Modal Functions
    function openAIModal() {
        document.getElementById('aiModal').classList.remove('hidden');
    }

    function closeAIModal() {
        document.getElementById('aiModal').classList.add('hidden');
        document.getElementById('aiReviewForm').reset();
        document.getElementById('aiResponse').classList.add('hidden');
        document.getElementById('selectedFile').classList.add('hidden');
        selectedContractFile = null;
        updateAISubmitButton();
    }

    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            selectedContractFile = file;
            const fileInfo = document.getElementById('selectedFile');
            fileInfo.innerHTML = `
                <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-file-alt text-blue-600"></i>
                    <span>${file.name}</span>
                    <span class="text-gray-500">(${(file.size / 1024).toFixed(1)} KB)</span>
                </div>
            `;
            fileInfo.classList.remove('hidden');
            updateAISubmitButton();
        }
    }

    function updateAISubmitButton() {
        const submitBtn = document.getElementById('aiSubmitBtn');
        const instructions = document.getElementById('aiInstructions').value.trim();
        submitBtn.disabled = !selectedContractFile || !instructions;
    }

    // Update submit button when instructions change
    document.getElementById('aiInstructions').addEventListener('input', updateAISubmitButton);

    // AI Form Submission
    document.getElementById('aiReviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('document', selectedContractFile);
        formData.append('instructions', document.getElementById('aiInstructions').value);
        
        const submitBtn = document.getElementById('aiSubmitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Analyzing...';
        submitBtn.disabled = true;
        
        // Show loading in response area
        const responseArea = document.getElementById('aiResponse');
        const responseContent = document.getElementById('aiResponseContent');
        responseArea.classList.remove('hidden');
        responseContent.innerHTML = '<div class="flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i>AI is analyzing your contract...</div>';
        
        fetch('api/ai_contract_review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                responseContent.innerHTML = formatAIResponse(data.response);
            } else {
                responseContent.innerHTML = `<div class="text-red-600"><i class="fas fa-exclamation-triangle mr-2"></i>Error: ${data.error || 'Failed to analyze contract'}</div>`;
            }
        })
        .catch(error => {
            console.error('AI Review Error:', error);
            responseContent.innerHTML = `<div class="text-red-600"><i class="fas fa-exclamation-triangle mr-2"></i>Error: Failed to connect to AI service</div>`;
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    function formatAIResponse(response) {
        // Format the AI response with better styling
        return response.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    }

    // Upload Modal Functions
    function openUploadModal() {
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        selectedFiles = [];
        document.getElementById('uploadFileList').innerHTML = '';
        document.getElementById('uploadBtn').disabled = true;
    }

    function handleUploadFileSelect(event) {
        const files = Array.from(event.target.files);
        selectedFiles = files;
        displayUploadFileList();
        document.getElementById('uploadBtn').disabled = files.length === 0;
    }

    function displayUploadFileList() {
        const fileList = document.getElementById('uploadFileList');
        fileList.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
            fileItem.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-file mr-2 text-gray-500"></i>
                    <span class="text-sm">${file.name}</span>
                    <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024).toFixed(1)} KB)</span>
                </div>
                <button onclick="removeUploadFile(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
    }

    function removeUploadFile(index) {
        selectedFiles.splice(index, 1);
        displayUploadFileList();
        document.getElementById('uploadBtn').disabled = selectedFiles.length === 0;
    }

    function uploadFiles() {
        if (selectedFiles.length === 0) return;

        const formData = new FormData();
        selectedFiles.forEach((file, index) => {
            formData.append('documents[]', file);
        });

        const uploadBtn = document.getElementById('uploadBtn');
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
        uploadBtn.disabled = true;

        fetch('api/upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Documents uploaded successfully!');
                closeUploadModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                alert('Error uploading documents: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            alert('Error uploading documents: ' + error.message);
        })
        .finally(() => {
            uploadBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload';
            uploadBtn.disabled = false;
        });
    }

    function downloadDocument(id) {
        window.open(`api/download.php?id=${id}`, '_blank');
    }

    function viewConversation(id) {
        window.location.href = `ai-conversations.php?id=${id}`;
    }
</script>