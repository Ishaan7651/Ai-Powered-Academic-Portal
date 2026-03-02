<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments - AI Powered Academic Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->load->view('simple_portal/components/admin_sidebar_css'); ?>
    <style>
        /* Topbar Styles */
        .topbar {
            background: white;
            padding: 0 35px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            position: relative;
            padding-left: 15px;
        }

        .page-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 22px;
            background: linear-gradient(to bottom, #1f5ea8, #3b82f6);
            border-radius: 2px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 16px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-profile:hover {
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 3px 8px rgba(102, 126, 234, 0.4);
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 700;
            font-size: 15px;
            color: #1e293b;
        }

        .user-role {
            font-size: 12px;
            color: #64748b;
            background: rgba(99, 102, 241, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
            font-weight: 600;
        }

        /* Content Area */
        .content-area {
            padding: 35px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-title h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 5px 0;
        }

        .header-title p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }

        .departments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .department-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .department-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .department-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 12px;
        }

        .department-name {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .department-code {
            background: #e0f2fe;
            color: #0369a1;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .department-description {
            color: #64748b;
            font-size: 14px;
            margin: 8px 0;
            line-height: 1.5;
        }

        .department-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-edit, .btn-delete {
            flex: 1;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: #3b82f6;
            color: white;
        }

        .btn-edit:hover {
            background: #2563eb;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .add-department-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .add-department-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1e293b;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body class="authenticated">

<div class="portal-container">
    <?php $this->load->view('simple_portal/components/admin_sidebar', ['active_page' => 'manage_departments']); ?>

    <main class="main-content">
        <div class="topbar">
            <div class="page-title">Manage Departments</div>
            <div class="user-profile">
                <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                <div class="user-info">
                    <div class="user-name"><?php echo $username; ?></div>
                    <div class="user-role">Admin</div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Department Management</h1>
                    <p>Add and manage academic departments</p>
                </div>
                <button class="add-department-btn" onclick="openAddModal()">
                    <i class="fa fa-plus"></i> Add Department
                </button>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($departments)): ?>
                <div class="empty-state">
                    <i class="fa fa-building"></i>
                    <h3>No Departments Yet</h3>
                    <p>Click "Add Department" to create your first department</p>
                </div>
            <?php else: ?>
                <div class="departments-grid">
                    <?php foreach ($departments as $dept): ?>
                        <div class="department-card">
                            <div class="department-header">
                                <h3 class="department-name"><?php echo htmlspecialchars($dept->name); ?></h3>
                                <span class="department-code"><?php echo htmlspecialchars($dept->code); ?></span>
                            </div>
                            <?php if ($dept->description): ?>
                                <p class="department-description"><?php echo htmlspecialchars($dept->description); ?></p>
                            <?php endif; ?>
                            <div class="department-actions">
                                <button class="btn-edit" onclick='openEditModal(<?php echo json_encode(array("id" => $dept->id, "name" => $dept->name, "code" => $dept->code, "description" => $dept->description)); ?>)'>
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                                <button class="btn-delete" onclick="deleteDepartment(<?php echo $dept->id; ?>, '<?php echo htmlspecialchars($dept->name, ENT_QUOTES); ?>')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Add/Edit Department Modal -->
<div id="departmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Add Department</h2>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <form id="departmentForm" method="POST" action="<?php echo base_url('simple_portal/save_department'); ?>">
            <input type="hidden" name="department_id" id="department_id">
            
            <div class="form-group">
                <label class="form-label" for="name">Department Name *</label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="e.g., Computer Science">
            </div>

            <div class="form-group">
                <label class="form-label" for="code">Department Code *</label>
                <input type="text" class="form-control" id="code" name="code" required placeholder="e.g., CS" maxlength="20">
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Brief description of the department"></textarea>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa fa-save"></i> Save Department
            </button>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Department';
    document.getElementById('departmentForm').reset();
    document.getElementById('department_id').value = '';
    document.getElementById('departmentModal').classList.add('active');
}

function openEditModal(dept) {
    document.getElementById('modalTitle').textContent = 'Edit Department';
    document.getElementById('department_id').value = dept.id;
    document.getElementById('name').value = dept.name;
    document.getElementById('code').value = dept.code;
    document.getElementById('description').value = dept.description || '';
    document.getElementById('departmentModal').classList.add('active');
}

function closeModal() {
    document.getElementById('departmentModal').classList.remove('active');
}

function deleteDepartment(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"?\n\nNote: Faculty members in this department will have their department unset.`)) {
        window.location.href = '<?php echo base_url('simple_portal/delete_department/'); ?>' + id;
    }
}

// Close modal when clicking outside
document.getElementById('departmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

</body>
</html>
