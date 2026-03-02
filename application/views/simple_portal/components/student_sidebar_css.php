<?php
/**
 * Shared CSS for Student Portal Sidebar and Layout
 * This file contains the standard styling used across all student-facing pages.
 */
?>
<style>
    :root {
        --primary-blue: #1f5ea8;
        --primary-dark: #0b2a4a;
        --primary-light: #114a7d;
        --success-green: #78b83f;
        --warning-orange: #f59e0b;
        --info-teal: #0ea5e9;
        --light-bg: #f8fafc;
        --white: #ffffff;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --border-color: #e2e8f0;
        --sidebar-width: 260px;
        --topbar-height: 80px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
        background: var(--light-bg);
        color: var(--text-dark);
        min-height: 100vh;
    }

    .portal-container {
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar - Dark Theme */
    .sidebar {
        width: var(--sidebar-width);
        background: linear-gradient(180deg, #0f172a, #1e293b);
        color: white;
        padding: 30px 0 0 0;
        position: fixed;
        height: 100vh;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: flex;
        flex-direction: column;
    }

    .brand {
        padding: 0 25px 30px;
        margin-bottom: 30px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
        flex-shrink: 0;
    }

    .brand-logo-img {
        max-width: 100%;
        height: auto;
        max-height: 60px;
        object-fit: contain;
    }

    .brand h1 {
        font-size: 32px;
        font-weight: 800;
        letter-spacing: 1px;
        color: var(--white);
        background: linear-gradient(45deg, #60a5fa, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-bottom: 20px;
    }

    .sidebar-nav::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    .sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .nav-section {
        padding: 0 20px;
        margin-bottom: 30px;
    }

    .nav-title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: rgba(255, 255, 255, 0.4);
        margin-bottom: 15px;
        font-weight: 600;
        padding-left: 5px;
    }

    .nav-item {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        margin-bottom: 6px;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 14px;
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .nav-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .nav-item i {
        width: 22px;
        margin-right: 14px;
        font-size: 17px;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .nav-item.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(139, 92, 246, 0.1));
        color: #ffffff;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .nav-item.active::before {
        transform: scaleY(1);
    }

    .nav-item.active i {
        transform: scale(1.1);
    }

    .nav-item:hover:not(.active) {
        background: rgba(255, 255, 255, 0.08);
        color: var(--white);
        transform: translateX(5px);
    }

    /* Main Content */
    .main-content {
        flex: 1;
        margin-left: var(--sidebar-width);
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }

    /* Topbar - Clean Design */
    .topbar {
        background: var(--white);
        padding: 0 35px;
        height: var(--topbar-height);
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border-color);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .page-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-dark);
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
        background: linear-gradient(to bottom, var(--primary-blue), var(--primary-light));
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
        border: 1px solid transparent;
        text-decoration: none;
    }

    .user-profile:hover {
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        border-color: #cbd5e1;
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
        color: var(--text-dark);
    }

    .user-role {
        font-size: 12px;
        color: var(--text-light);
        background: rgba(99, 102, 241, 0.1);
        padding: 2px 8px;
        border-radius: 12px;
        display: inline-block;
        font-weight: 600;
    }

    /* Content Area */
    .content-area {
        padding: 35px;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* Dashboard Header Style (Consistent across pages) */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
    }

    .header-title h1 {
        font-size: 32px;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 12px;
        background: linear-gradient(135deg, var(--text-dark), #475569);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .header-title p {
        color: var(--text-light);
        font-size: 16px;
        line-height: 1.5;
        max-width: 600px;
    }

    /* Badges */
    .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-new { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .badge-pdf { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
    .badge-active { background: rgba(34, 197, 94, 0.1); color: #16a34a; }

    /* Responsive */
    @media (max-width: 1024px) {
        .sidebar {
            width: 80px;
            padding: 20px 0;
        }
        .brand h1, .nav-item span, .nav-title {
            display: none;
        }
        .nav-item {
            justify-content: center;
            padding: 15px;
        }
        .nav-item i {
            margin-right: 0;
            font-size: 20px;
        }
        .main-content {
            margin-left: 80px;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .main-content {
            margin-left: 0;
        }
        .topbar {
            padding: 0 15px;
        }
        .content-area {
            padding: 20px;
        }
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }
    }
</style>
