<style>
    /* Content Area */
    .content-area {
        padding: 35px;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* Dashboard Header */
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

    /* Main Content Card */
    .main-card {
        background: var(--white);
        border-radius: 18px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        margin-bottom: 35px;
    }

    .card-header {
        padding: 28px 32px;
        border-bottom: 1px solid var(--border-color);
        background: linear-gradient(to right, #f8fafc, #f1f5f9);
    }

    .card-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-title i {
        color: var(--primary-blue);
        font-size: 20px;
    }

    .card-subtitle {
        color: var(--text-light);
        font-size: 14px;
        margin-top: 8px;
    }

    .card-body {
        padding: 30px; /* Added padding for general use, dashboard overrides might be 0 */
    }
</style>
