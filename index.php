<?php
include_once 'header.php';
require_once 'config/database.php';
require_once 'classes/Document.php';

$database = new Database();
$db = $database->getConnection();
$document = new Document($db);

$user_documents = $document->getUserDocuments($_SESSION['user_id']);
$recent_documents = array_slice($user_documents, 0, 5); // Get 5 most recent
?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Welcome back <?php echo htmlspecialchars($user_name); ?>,</h1>
                    <p class="text-gray-600 mt-1">Here is what's happening in your account</p>
                </div>
                <button class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition-colors">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Review Contract with AI -->
                <div class="gradient-blue text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openContractReviewModal()">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-robot text-2xl mr-3"></i>
                        <span class="text-sm opacity-90">AI</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Review contract with AI</h3>
                    <p class="text-sm opacity-90">Get immediate responses to your questions and AI assistance with drafting and summarizing</p>
                </div>

                <!-- Create Document -->
                <div class="gradient-dark text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openCreateDocumentModal()">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-file-plus text-2xl mr-3"></i>
                        <span class="text-sm opacity-90">Template library</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Create a document</h3>
                    <p class="text-sm opacity-90">Create a contract based on a template</p>
                </div>

                <!-- Upload Documents -->
                <div class="gradient-gray text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openUploadModal()">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-upload text-2xl mr-3"></i>
                        <span class="text-sm opacity-90">Repository</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Upload documents</h3>
                    <p class="text-sm opacity-90">Upload files to the repository for storage and management</p>
                </div>

                <!-- Send for eSignature -->
                <div class="gradient-purple text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openESignatureModal()">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-signature text-2xl mr-3"></i>
                        <span class="text-sm opacity-90">eSigning</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Send for eSignature</h3>
                    <p class="text-sm opacity-90">Upload a document and send for eSigning instantly</p>
                </div>
            </div>

            <!-- Additional Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Legal Research -->
                <div class="gradient-green text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openLegalResearchModal()">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-search text-2xl mr-3"></i>
                        <span class="text-sm opacity-90">Research</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Legal Research</h3>
                    <p class="text-sm opacity-90">Research legal precedents and case law</p>
                </div>

                <!-- Compliance Check -->
                <div class="gradient-orange text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openComplianceModal()">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-shield-alt text-2xl mr-3"></i>
                        <span class="text-sm opacity-90">Compliance</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Compliance Check</h3>
                    <p class="text-sm opacity-90">Verify regulatory compliance requirements</p>
                </div>

                <!-- Contract Analytics -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="window.location.href='insights.php'">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-chart-pie text-2xl mr-3"></i>
                        <span class="text-sm opacity-90">Analytics</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Contract Analytics</h3>
                    <p class="text-sm opacity-90">Analyze contract performance and risks</p>
                </div>
            </div>

            <!-- Contract Workflow and Tasks -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Contract Workflow -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Contract workflow</h2>
                        <div class="flex items-center space-x-4">
                            <select class="border rounded-lg px-3 py-1 text-sm">
                                <option>My documents</option>
                            </select>
                            <select class="border rounded-lg px-3 py-1 text-sm">
                                <option>Last 30 days</option>
                            </select>
                        </div>
                    </div>

                    <!-- Workflow Tabs -->
                    <div class="flex space-x-6 border-b mb-6">
                        <button class="pb-2 text-sm font-medium text-gray-900 border-b-2 border-blue-500">All</button>
                        <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Draft</button>
                        <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Review</button>
                        <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Agreed form</button>
                        <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">eSigning</button>
                        <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Signed</button>
                        <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Unknown</button>
                    </div>

                    <!-- Recent Documents -->
                    <?php if (empty($recent_documents)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No documents</h3>
                            <p class="text-gray-500 mb-4">Upload your first document to get started.</p>
                            <button onclick="openUploadModal()" class="text-blue-600 hover:text-blue-700 font-medium">Upload Document</button>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($recent_documents as $doc): ?>
                                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($doc['original_name']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($doc['created_at'])); ?></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php echo $doc['status'] === 'uploaded' ? 'bg-green-100 text-green-800' : 
                                                        ($doc['status'] === 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                            <?php echo ucfirst($doc['status']); ?>
                                        </span>
                                        <?php if ($doc['ai_processed']): ?>
                                            <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center pt-4">
                                <a href="documents.php" class="text-blue-600 hover:text-blue-700 font-medium text-sm">View all documents</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tasks -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Tasks</h2>
                        <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">Show all</button>
                    </div>

                    <!-- Task Tabs -->
                    <div class="flex space-x-4 border-b mb-6">
                        <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">To-do</button>
                        <button class="pb-2 text-sm font-medium text-gray-900 border-b-2 border-blue-500">Completed</button>
                    </div>

                    <!-- Completed State -->
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-4xl text-green-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Well done!</h3>
                        <p class="text-gray-500">You have completed all your tasks</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contract Review Modal -->
<div id="contractReviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-robot text-blue-600 mr-2"></i>
                        AI Contract Review
                    </h2>
                    <button onclick="closeContractReviewModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="flex-1 flex overflow-hidden">
                <!-- Upload Section -->
                <div id="uploadSection" class="w-full p-6 overflow-auto">
                    <div class="max-w-2xl mx-auto">
                        <div class="text-center mb-6">
                            <i class="fas fa-file-contract text-4xl text-blue-600 mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Upload Contract for AI Review</h3>
                            <p class="text-gray-600">Upload your contract and provide instructions for AI analysis</p>
                        </div>
                        
                        <form id="contractReviewForm" class="space-y-6">
                            <!-- File Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contract Document *</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                                    <input type="file" id="contractFile" accept=".pdf,.doc,.docx,.txt" class="hidden" onchange="handleContractFileSelect(event)">
                                    <div id="fileDropArea" onclick="document.getElementById('contractFile').click()" class="cursor-pointer">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                                        <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                                        <p class="text-sm text-gray-500">PDF, DOC, DOCX, TXT (Max 10MB)</p>
                                    </div>
                                    <div id="selectedFile" class="hidden mt-4 p-3 bg-blue-50 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <i class="fas fa-file text-blue-600 mr-2"></i>
                                                <span id="fileName" class="text-sm font-medium text-blue-800"></span>
                                            </div>
                                            <button type="button" onclick="removeContractFile()" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Instructions -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Review Instructions *</label>
                                <textarea id="reviewInstructions" rows="4" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Provide specific instructions for the AI review. For example:
                                - Identify potential risks and liabilities
                                - Review payment terms and conditions
                                - Check for missing clauses
                                - Analyze termination provisions" required></textarea>
                                <p class="text-sm text-gray-500 mt-1">Be specific about what you want the AI to focus on</p>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeContractReviewModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                                <button type="submit" id="reviewSubmitBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-robot mr-2"></i>Start AI Review
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Results Section -->
                <div id="resultsSection" class="w-full p-6 hidden overflow-auto">
                    <div class="h-full flex flex-col">
                        <!-- Processing State -->
                        <div id="processingState" class="flex-1 flex items-center justify-center">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Analyzing Contract...</h3>
                                <p class="text-gray-600">AI is reviewing your contract and processing your instructions</p>
                            </div>
                        </div>
                        
                        <!-- Results Display -->
                        <div id="reviewResults" class="hidden flex-1 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">AI Review Results</h3>
                                <button onclick="startNewReview()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    <i class="fas fa-plus mr-1"></i>New Review
                                </button>
                            </div>
                            
                            <div class="flex-1 bg-gray-50 rounded-lg p-4 overflow-y-auto">
                                <div id="aiResponse" class="prose max-w-none">
                                    <!-- AI response will be displayed here -->
                                </div>
                            </div>
                            
                            <!-- Debug Information -->
                            <div id="debugInfo" class="mt-4 p-3 bg-gray-100 rounded text-xs text-gray-600 hidden">
                                <strong>Debug Info:</strong>
                                <div id="debugDetails"></div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-start space-x-2">
                                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                                    <div class="text-sm text-blue-800">
                                        <strong>Document:</strong> <span id="reviewedFileName"></span><br>
                                        <strong>Instructions:</strong> <span id="reviewedInstructions"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Chat Modal -->
<div id="aiChatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-6xl w-full max-h-[90vh] flex">
            <!-- Conversations Sidebar -->
            <div class="w-80 border-r bg-gray-50 flex flex-col">
                <div class="p-4 border-b bg-white">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-800">Conversations</h3>
                        <button onclick="startNewConversation()" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="relative">
                        <input type="text" id="conversationSearch" placeholder="Search conversations..." class="w-full pl-8 pr-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search absolute left-2 top-3 text-gray-400 text-xs"></i>
                    </div>
                </div>
                <div id="conversationsList" class="flex-1 overflow-y-auto p-2">
                    <!-- Conversations will be loaded here -->
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="flex-1 flex flex-col">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-robot text-blue-600 mr-2"></i>
                        AI Contract Assistant
                    </h2>
                        <div class="flex items-center space-x-2">
                            <button id="deleteConversationBtn" onclick="deleteCurrentConversation()" class="text-red-500 hover:text-red-700 hidden">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button onclick="closeAIChat()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                </div>
            </div>
            
            <!-- Chat Messages Area -->
                <div id="chatMessages" class="flex-1 p-6 overflow-y-auto bg-gray-50">
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                            <i class="fas fa-robot"></i>
                        </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm max-w-2xl">
                            <p class="text-gray-800">Hello! I'm your AI contract assistant. Upload a contract and ask me anything about it - I can help with analysis, risk assessment, clause explanations, and more.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- File Upload Area -->
            <div class="p-4 border-t border-b bg-white">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="file" id="chatFileInput" accept=".pdf,.doc,.docx,.txt" class="hidden" onchange="handleChatFileSelect(event)">
                        <button onclick="document.getElementById('chatFileInput').click()" class="flex items-center px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-paperclip mr-2"></i>
                            Attach Contract
                        </button>
                    </div>
                    <div id="chatFilePreview" class="flex items-center space-x-2"></div>
                </div>
            </div>
            
            <!-- Chat Input Area -->
            <div class="p-6 bg-white">
                <div class="flex space-x-3">
                    <div class="flex-1">
                        <textarea id="chatInput" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" placeholder="Ask me about your contract... (e.g., 'What are the key risks in this agreement?', 'Explain the termination clause', 'Is this contract favorable?')"></textarea>
                    </div>
                    <button onclick="sendChatMessage()" id="sendChatBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="flex items-center justify-between mt-3">
                    <div class="text-xs text-gray-500">
                        Press Shift+Enter for new line, Enter to send
                    </div>
                    <div class="text-xs text-gray-500">
                        <span id="charCount">0</span>/2000 characters
                    </div>
                </div>
            </div>
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
                    <input type="file" id="fileInput" multiple class="hidden" onchange="handleFileSelect(event)">
                    <button onclick="document.getElementById('fileInput').click()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Select Files
                    </button>
                </div>
                <div id="fileList" class="mt-4 space-y-2"></div>
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
    let chatFile = null;
    let currentConversationId = null;
    let conversations = [];
    let contractReviewFile = null;

    function toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('hidden');
    }

    function openAIChat() {
        document.getElementById('aiChatModal').classList.remove('hidden');
        loadConversations();
        startNewConversation();
    }

    function openContractReviewModal() {
        document.getElementById('contractReviewModal').classList.remove('hidden');
        resetContractReviewModal();
    }

    function closeContractReviewModal() {
        document.getElementById('contractReviewModal').classList.add('hidden');
        resetContractReviewModal();
    }
    
    function resetContractReviewModal() {
        document.getElementById('contractReviewForm').reset();
        contractReviewFile = null;
        document.getElementById('selectedFile').classList.add('hidden');
        document.getElementById('uploadSection').classList.remove('hidden');
        document.getElementById('resultsSection').classList.add('hidden');
        document.getElementById('reviewResults').classList.add('hidden');
        document.getElementById('processingState').classList.remove('hidden');
        const submitBtn = document.getElementById('reviewSubmitBtn');
        submitBtn.innerHTML = '<i class="fas fa-robot mr-2"></i>Start AI Review';
        submitBtn.disabled = false;
    }
    
    function handleContractFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a PDF, DOC, DOCX, or TXT file.');
                event.target.value = '';
                return;
            }
            
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB.');
                event.target.value = '';
                return;
            }
            
            contractReviewFile = file;
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('selectedFile').classList.remove('hidden');
        }
    }
    
    function removeContractFile() {
        contractReviewFile = null;
        document.getElementById('contractFile').value = '';
        document.getElementById('selectedFile').classList.add('hidden');
    }
    
    function startNewReview() {
        resetContractReviewModal();
    }
    
    // Contract Review Form Submission
    document.getElementById('contractReviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const instructions = document.getElementById('reviewInstructions').value.trim();
        
        if (!contractReviewFile) {
            alert('Please select a contract file to upload.');
            return;
        }
        
        if (!instructions) {
            alert('Please provide review instructions.');
            return;
        }
        
        document.getElementById('uploadSection').classList.add('hidden');
        document.getElementById('resultsSection').classList.remove('hidden');
        document.getElementById('processingState').classList.remove('hidden');
        document.getElementById('reviewResults').classList.add('hidden');
        
        const formData = new FormData();
        formData.append('document', contractReviewFile);
        formData.append('instructions', instructions);
        formData.append('user_id', <?php echo json_encode($_SESSION['user_id']); ?>);
        formData.append('timestamp', new Date().toISOString());
        
        fetch('api/ai_contract_review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('processingState').classList.add('hidden');
            document.getElementById('reviewResults').classList.remove('hidden');
            
            if (data.success) {
                document.getElementById('aiResponse').innerHTML = formatAIResponse(data.response);
                document.getElementById('reviewedFileName').textContent = data.document_name;
                document.getElementById('reviewedInstructions').textContent = data.instructions;
                
                if (data.debug_info) {
                    showDebugInfo(data.debug_info);
                }
                
                if (data.webhook_success) {
                    console.log('✅ n8n webhook response received successfully');
                    console.log('Webhook URL:', data.webhook_url || 'None');
                    console.log('HTTP Code:', data.http_code || 'None');
                } else {
                    console.log('⚠️ AI analysis service unavailable');
                    console.log('Webhook URL:', data.webhook_url || 'None');
                    console.log('HTTP Code:', data.http_code || 'None');
                    console.log('Debug Info:', data.debug_info || 'None');
                }
            } else {
                document.getElementById('aiResponse').innerHTML = `
                    <div class="text-red-600">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Error:</strong> ${data.error || 'Failed to process contract review'}
                    </div>
                `;
                
                if (data.debug_info) {
                    console.log('Debug Info:', data.debug_info);
                    showDebugInfo(data.debug_info);
                }
            }
        })
        .catch(error => {
            console.error('Contract review error:', error);
            document.getElementById('processingState').classList.add('hidden');
            document.getElementById('reviewResults').classList.remove('hidden');
            document.getElementById('aiResponse').innerHTML = `
                <div class="text-red-600">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Error:</strong> Failed to connect to AI service. Please try again.
                </div>
            `;
        });
    });


    function formatAIResponse(response) {
        let formatted = response
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n\n/g, '</p><p>')
            .replace(/\n/g, '<br>');
        
        return '<p>' + formatted + '</p>';
    }
    
    function showDebugInfo(debugInfo) {
        const debugContainer = document.getElementById('debugInfo');
        const debugDetails = document.getElementById('debugDetails');
        
        if (debugInfo && Array.isArray(debugInfo) && debugInfo.length > 0) {
            debugDetails.innerHTML = debugInfo.map(attempt => `
                <div><strong>Type:</strong> ${attempt.type === 'doc_upload' ? 'Document Upload' : 'Query'}</div>
                <div><strong>URL:</strong> ${attempt.url}</div>
                <div><strong>HTTP Code:</strong> ${attempt.http_code}</div>
                <div><strong>cURL Error:</strong> ${attempt.curl_error || 'None'}</div>
                <div><strong>Response Length:</strong> ${attempt.response_length || 0} characters</div>
                <div><strong>Connect Time:</strong> ${attempt.connect_time.toFixed(2)}s</div>
                <div><strong>Total Time:</strong> ${attempt.total_time.toFixed(2)}s</div>
                <div><strong>Retry Attempt:</strong> ${attempt.retry_attempt || 'N/A'}</div>
                <div><strong>Raw Response (truncated):</strong> <pre class="mt-1 text-xs bg-white p-2 rounded">${attempt.raw_response || 'Empty'}</pre></div>
            `).join('<hr class="my-2">');
            debugContainer.classList.remove('hidden');
        } else {
            debugDetails.innerHTML = '<div>No debug information available</div>';
            debugContainer.classList.add('hidden');
        }
    }

    function closeAIChat() {
        document.getElementById('aiChatModal').classList.add('hidden');
        resetChatState();
    }
    
    function resetChatState() {
        currentConversationId = null;
        chatFile = null;
        document.getElementById('chatInput').value = '';
        document.getElementById('chatFilePreview').innerHTML = '';
        document.getElementById('deleteConversationBtn').classList.add('hidden');
        updateCharCount();
    }
    
    function startNewConversation() {
        resetChatState();
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm max-w-2xl">
                        <p class="text-gray-800">Hello! I'm your AI contract assistant. Upload a contract and ask me anything about it - I can help with analysis, risk assessment, clause explanations, and more.</p>
                    </div>
                </div>
            </div>
        `;
        
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('bg-blue-50', 'border-blue-200');
        });
    }
    
    function loadConversations() {
        $.ajax({
            url: 'api/get_conversations.php',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    conversations = response.conversations;
                    displayConversations();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading conversations:', error);
            }
        });
    }
    
    function displayConversations() {
        const conversationsList = document.getElementById('conversationsList');
        
        if (conversations.length === 0) {
            conversationsList.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-comments text-2xl mb-2"></i>
                    <p class="text-sm">No conversations yet</p>
                    <p class="text-xs">Start chatting to create your first conversation</p>
                </div>
            `;
            return;
        }
        
        conversationsList.innerHTML = conversations.map(conv => `
            <div class="conversation-item p-3 rounded-lg cursor-pointer hover:bg-gray-100 mb-2 border border-transparent" 
                 onclick="loadConversation(${conv.id})" data-conversation-id="${conv.id}">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 text-sm truncate">${conv.title}</h4>
                        <p class="text-xs text-gray-500 truncate mt-1">${conv.last_message || 'No messages'}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-400">${conv.message_count} messages</span>
                            <span class="text-xs text-gray-400">${formatDate(conv.updated_at)}</span>
                        </div>
                    </div>
                    <button onclick="event.stopPropagation(); deleteConversation(${conv.id})" 
                            class="text-gray-400 hover:text-red-500 ml-2">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    function loadConversation(conversationId) {
        currentConversationId = conversationId;
        
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('bg-blue-50', 'border-blue-200');
        });
        
        const activeItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
        if (activeItem) {
            activeItem.classList.add('bg-blue-50', 'border-blue-200');
        }
        
        document.getElementById('deleteConversationBtn').classList.remove('hidden');
        
        $.ajax({
            url: `api/get_conversations.php?conversation_id=${conversationId}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayConversationMessages(response.messages);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading conversation:', error);
            }
        });
    }
    
    function displayConversationMessages(messages) {
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.innerHTML = '<div class="space-y-4"></div>';
        const container = chatMessages.querySelector('.space-y-4');
        
        messages.forEach(message => {
            addMessageToChat(message.message_type, message.content, message.document_name, false);
        });
    }
    
    function deleteConversation(conversationId) {
        if (!confirm('Are you sure you want to delete this conversation? This action cannot be undone.')) {
            return;
        }
        
        $.ajax({
            url: 'api/delete_conversation.php',
            method: 'POST',
            data: JSON.stringify({ conversation_id: conversationId }),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    loadConversations();
                    if (currentConversationId === conversationId) {
                        startNewConversation();
                    }
                } else {
                    alert('Error deleting conversation: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Error deleting conversation: ' + error);
            }
        });
    }
    
    function deleteCurrentConversation() {
        if (currentConversationId) {
            deleteConversation(currentConversationId);
        }
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 1) {
            return 'Today';
        } else if (diffDays === 2) {
            return 'Yesterday';
        } else if (diffDays <= 7) {
            return `${diffDays - 1} days ago`;
        } else {
            return date.toLocaleDateString();
        }
    }

    function showComingSoon(feature) {
        alert(feature + ' functionality will be implemented in the next phase.');
    }

    function handleChatFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            chatFile = file;
            const preview = document.getElementById('chatFilePreview');
            preview.innerHTML = `
                <div class="flex items-center space-x-2 bg-blue-50 px-3 py-1 rounded-lg">
                    <i class="fas fa-file text-blue-600"></i>
                    <span class="text-sm text-blue-800">${file.name}</span>
                    <button onclick="removeChatFile()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            `;
        }
    }

    function removeChatFile() {
        chatFile = null;
        document.getElementById('chatFilePreview').innerHTML = '';
        document.getElementById('chatFileInput').value = '';
    }

    function sendChatMessage() {
        const input = document.getElementById('chatInput');
        const query = input.value.trim();
        
        if (!query) {
            alert('Please enter a message');
            return;
        }

        addMessageToChat('user', query, chatFile ? chatFile.name : null);
        
        input.value = '';
        updateCharCount();
        
        addTypingIndicator();
        
        const formData = new FormData();
        formData.append('query', query);
        
        if (currentConversationId) {
            formData.append('conversation_id', currentConversationId);
        }
        
        if (chatFile) {
            formData.append('document', chatFile);
        }
        
        $.ajax({
            url: 'api/ai_contract_review.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                removeTypingIndicator();
                if (response.success) {
                    if (!currentConversationId && response.conversation_id) {
                        currentConversationId = response.conversation_id;
                        document.getElementById('deleteConversationBtn').classList.remove('hidden');
                        loadConversations();
                    }
                    
                    addMessageToChat('assistant', response.response);
                    
                    if (response.webhook_success) {
                        console.log('✅ Webhook response received successfully');
                    } else {
                        console.log('⚠️ AI analysis service unavailable');
                    }
                } else {
                    addMessageToChat('assistant', 'I apologize, but I encountered an error processing your request. Please try again.');
                }
                removeChatFile();
            },
            error: function(xhr, status, error) {
                removeTypingIndicator();
                addMessageToChat('assistant', 'I apologize, but I encountered an error processing your request. Please try again.');
                console.error('AI Query Error:', error);
            }
        });
    }

    function addMessageToChat(sender, message, documentName = null, animate = true) {
        const chatMessages = document.getElementById('chatMessages');
        const container = chatMessages.querySelector('.space-y-4') || chatMessages;
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex items-start space-x-3 ${animate ? 'animate-fade-in' : ''}`;
        
        if (sender === 'user') {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white text-sm">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <div class="bg-blue-600 text-white p-4 rounded-lg shadow-sm max-w-2xl">
                    ${documentName ? `<div class="mb-2 text-blue-100 text-sm"><i class="fas fa-paperclip mr-1"></i>${documentName}</div>` : ''}
                    <p class="whitespace-pre-wrap">${message}</p>
                </div>
            `;
        } else if (sender === 'assistant') {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm max-w-2xl">
                    <p class="text-gray-800 whitespace-pre-wrap">${message}</p>
                </div>
            `;
        }
        
        container.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function addTypingIndicator() {
        const chatMessages = document.getElementById('chatMessages');
        const container = chatMessages.querySelector('.space-y-4') || chatMessages;
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.className = 'flex items-start space-x-3';
        typingDiv.innerHTML = `
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                <i class="fas fa-robot"></i>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        `;
        container.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function removeTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    function updateCharCount() {
        const input = document.getElementById('chatInput');
        const charCount = document.getElementById('charCount');
        charCount.textContent = input.value.length;
    }

    document.getElementById('chatInput').addEventListener('input', updateCharCount);
    document.getElementById('chatInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendChatMessage();
        }
    });

    function handleFileSelect(event) {
        const files = Array.from(event.target.files);
        selectedFiles = files;
        displayFileList();
        document.getElementById('uploadBtn').disabled = files.length === 0;
    }

    function displayFileList() {
        const fileList = document.getElementById('fileList');
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
                <button onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        displayFileList();
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

        $.ajax({
            url: 'api/upload.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Documents uploaded successfully!');
                closeUploadModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr, status, error) {
                alert('Error uploading documents: ' + error);
            },
            complete: function() {
                uploadBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload';
                uploadBtn.disabled = false;
            }
        });
    }

    function openCreateDocumentModal() {
        alert('Create Document functionality: Choose from professional templates, customize with AI assistance, and generate contracts instantly.');
    }

    function openESignatureModal() {
        alert('eSignature functionality: Upload documents, add signature fields, send to multiple parties, and track signing progress in real-time.');
    }

    function openLegalResearchModal() {
        alert('Legal Research functionality: Search case law, statutes, regulations, and legal precedents with AI-powered analysis.');
    }

    function openComplianceModal() {
        alert('Compliance Check functionality: Verify documents against regulatory requirements, industry standards, and legal compliance frameworks.');
    }

    const uploadArea = document.querySelector('#uploadModal .border-dashed');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });
    
    const contractDropArea = document.getElementById('fileDropArea');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        contractDropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    ['dragenter', 'dragover'].forEach(eventName => {
        contractDropArea.addEventListener(eventName, highlightContract, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        contractDropArea.addEventListener(eventName, unhighlightContract, false);
    });
    
    function highlightContract(e) {
        contractDropArea.classList.add('border-blue-500', 'bg-blue-50');
    }
    
    function unhighlightContract(e) {
        contractDropArea.classList.remove('border-blue-500', 'bg-blue-50');
    }
    
    contractDropArea.addEventListener('drop', handleContractDrop, false);
    
    function handleContractDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            const file = files[0];
            document.getElementById('contractFile').files = files;
            handleContractFileSelect({ target: { files: [file] } });
        }
    }

    function highlight(e) {
        uploadArea.classList.add('border-blue-500', 'bg-blue-50');
    }

    function unhighlight(e) {
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
    }

    uploadArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        selectedFiles = Array.from(files);
        displayFileList();
        document.getElementById('uploadBtn').disabled = files.length === 0;
    }

    const style = document.createElement('style');
    style.textContent = `
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
        .conversation-item:hover {
            background-color: #f9fafb;
        }
        .conversation-item.active {
            background-color: #eff6ff;
            border-color: #dbeafe;
        }
    `;
    document.head.appendChild(style);
</script>