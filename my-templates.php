<?php
include_once 'header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get user templates
$query = "SELECT * FROM user_templates WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user_templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_name = $_SESSION['user_name'] ?? 'User';
?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">My Templates</h1>
                    <p class="text-gray-600 mt-1">Your customized legal document templates</p>
                </div>
                <div class="flex space-x-3">
                    <a href="templates.php" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Browse Templates
                    </a>
                    <button onclick="createNewTemplate()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-plus mr-2"></i>Create New
                    </button>
                </div>
            </div>
        </div>

        <!-- Templates Content -->
        <div class="p-8">
            <?php if (empty($user_templates)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No templates yet</h3>
                    <p class="text-gray-600 mb-6">Start by browsing our template library or create your own</p>
                    <div class="flex justify-center space-x-4">
                        <a href="templates.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Browse Templates
                        </a>
                        <button onclick="createNewTemplate()" class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-file-plus mr-2"></i>Create New
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($user_templates as $template): ?>
                        <div class="bg-white rounded-xl shadow-sm p-6 template-card transition-all duration-300 hover:shadow-md">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editTemplate(<?php echo $template['id']; ?>)" class="text-gray-400 hover:text-blue-600">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteTemplate(<?php echo $template['id']; ?>)" class="text-gray-400 hover:text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($template['name']); ?></h3>
                            <p class="text-gray-600 text-sm mb-4">Custom template based on template #<?php echo $template['template_id']; ?></p>
                            
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Created <?php echo date('M j, Y', strtotime($template['created_at'])); ?></span>
                                <button onclick="useTemplate(<?php echo $template['id']; ?>)" class="text-blue-600 hover:text-blue-700 font-medium">
                                    Use Template
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Template Editor Modal -->
<div id="templateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold" id="modalTitle">Edit Template</h2>
                    <button onclick="closeTemplateModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="templateForm">
                    <input type="hidden" id="templateId" name="template_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Template Name</label>
                        <input type="text" id="templateName" name="name" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter template name" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Template Content</label>
                        <textarea id="templateContent" name="content" rows="15" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm" placeholder="Enter your template content here..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeTemplateModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Save Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('hidden');
    }

    function createNewTemplate() {
        document.getElementById('modalTitle').textContent = 'Create New Template';
        document.getElementById('templateForm').reset();
        document.getElementById('templateId').value = '';
        document.getElementById('templateModal').classList.remove('hidden');
    }

    function editTemplate(id) {
        // Fetch template data and populate form
        fetch(`api/get_template.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modalTitle').textContent = 'Edit Template';
                    document.getElementById('templateId').value = id;
                    document.getElementById('templateName').value = data.template.name;
                    
                    // Load template content from file
                    if (data.template.file_path) {
                        fetch(`api/get_template_content.php?id=${id}`)
                            .then(response => response.text())
                            .then(content => {
                                document.getElementById('templateContent').value = content;
                            });
                    }
                    
                    document.getElementById('templateModal').classList.remove('hidden');
                } else {
                    alert('Error loading template: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading template');
            });
    }

    function deleteTemplate(id) {
        if (confirm('Are you sure you want to delete this template?')) {
            fetch('api/delete_template.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ template_id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting template: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting template');
            });
        }
    }

    function useTemplate(id) {
        // This would open the template for use/editing
        alert('Use template functionality will open the template editor.');
    }

    function closeTemplateModal() {
        document.getElementById('templateModal').classList.add('hidden');
    }

    // Handle form submission
    document.getElementById('templateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            template_id: formData.get('template_id') || 'custom',
            document_name: formData.get('name'),
            content: formData.get('content')
        };
        
        fetch('api/use_template.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Template saved successfully!');
                closeTemplateModal();
                location.reload();
            } else {
                alert('Error saving template: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving template');
        });
    });
</script>