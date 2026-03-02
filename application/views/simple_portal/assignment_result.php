<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Assignment - College Academic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/assignment-style.css'); ?>">

    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .assignment-content { 
            background: #f8f9fa; 
            border-radius: 10px; 
            padding: 20px; 
            white-space: pre-wrap; 
            font-family: 'Courier New', monospace;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url('simple_portal'); ?>">
                <i class="fas fa-graduation-cap me-2"></i>College Academic Portal
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($username); ?> 
                    <span class="badge bg-light text-dark">Faculty</span>
                </span>
                <a class="nav-link" href="<?php echo base_url('simple_portal?action=logout'); ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5><i class="fas fa-magic me-2"></i>Generated Assignment</h5>
                            <small class="text-muted">AI-generated content ready for use</small>
                        </div>
                        <div>
                            <button onclick="copyToClipboard()" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-copy me-1"></i>Copy
                            </button>
                            <button onclick="printAssignment()" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-print me-1"></i>Print
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="assignment-content" id="assignmentContent">
<?php echo htmlspecialchars($generated_assignment); ?>
                        </div>
                        
                        <div class="alert alert-success mt-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Assignment Generated Successfully!</strong> 
                            You can copy this content, print it, or modify it as needed for your students.
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo base_url('simple_portal/generate_assignment'); ?>" class="btn btn-outline-primary me-md-2">
                                <i class="fas fa-plus me-2"></i>Generate Another
                            </a>
                            <a href="<?php echo base_url('simple_portal'); ?>" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-lightbulb me-2"></i>Tips for Using AI-Generated Content</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Review content for accuracy</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Customize difficulty as needed</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Add your own examples</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Include marking rubrics</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Set clear deadlines</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Provide additional resources</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard() {
            const content = document.getElementById('assignmentContent').textContent;
            navigator.clipboard.writeText(content).then(function() {
                // Show success message
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success');
                
                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-primary');
                }, 2000);
            });
        }
        
        function printAssignment() {
            const content = document.getElementById('assignmentContent').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Assignment</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .content { white-space: pre-wrap; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>Assignment</h2>
                        <p>Generated on: ${new Date().toLocaleDateString()}</p>
                    </div>
                    <div class="content">${content}</div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>