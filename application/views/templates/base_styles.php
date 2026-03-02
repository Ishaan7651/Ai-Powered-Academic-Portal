<?php
// Base styles for the application
?>
<style>
/* UAI Brand Colors */
:root {
    --primary-blue: #4A76A8;
    --primary-dark: #1D4486;
    --primary-light: #6B8BC3;
    --success-green: #759B49;
    --light-bg: #eef2f7;
    --white: #ffffff;
    --text-dark: #333333;
    --text-light: #666666;
    --border-color: #e0e0e0;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    background: var(--light-bg);
    color: var(--text-dark);
    min-height: 100vh;
}

/* Common button styles */
.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: var(--primary-blue);
    color: var(--white);
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 118, 168, 0.2);
}

.btn-success {
    background: var(--success-green);
    color: var(--white);
}

.btn-success:hover {
    background: #6a8a42;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(117, 155, 73, 0.2);
}

.btn-light {
    background: var(--white);
    color: var(--text-dark);
    border: 1px solid var(--border-color);
}

.btn-light:hover {
    background: #f8f9fa;
    color: var(--primary-blue);
}

/* Form styles */
.form-control, .form-select {
    padding: 12px 16px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 0.2rem rgba(74, 118, 168, 0.25);
}

/* Card styles */
.card {
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.card-body {
    padding: 20px;
}

/* Alert styles */
.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 16px;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert-info {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

.alert-warning {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
}

/* Utility classes */
.text-center { text-align: center; }
.text-muted { color: var(--text-light); }
.mb-0 { margin-bottom: 0; }
.mb-1 { margin-bottom: 0.25rem; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-3 { margin-bottom: 1rem; }
.mb-4 { margin-bottom: 1.5rem; }
.mt-2 { margin-top: 0.5rem; }
.mt-3 { margin-top: 1rem; }
.me-1 { margin-right: 0.25rem; }
.me-2 { margin-right: 0.5rem; }
.w-100 { width: 100%; }
.d-flex { display: flex; }
.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }

/* Responsive utilities */
@media (max-width: 768px) {
    .container-fluid {
        padding: 15px;
    }
    
    .card-body {
        padding: 15px;
    }
}
</style>