<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($paper->title); ?> - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1f5ea8;
            --primary-dark: #0b2a4a;
            --success-green: #78b83f;
            --light-bg: #f8fafc;
            --white: #ffffff;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border-color: #e2e8f0;
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
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--white);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
            transform: translateX(-5px);
        }

        .paper-container {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .paper-header {
            background: linear-gradient(135deg, var(--primary-blue), #114a7d);
            color: white;
            padding: 40px;
        }

        .paper-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .paper-meta {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
        }

        .meta-item i {
            font-size: 18px;
            opacity: 0.8;
        }

        .paper-actions {
            padding: 25px 40px;
            background: var(--light-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), #114a7d);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(31, 94, 168, 0.3);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--text-dark);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }

        .paper-content {
            padding: 40px;
        }

        .content-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
        }

        .content-text {
            font-size: 15px;
            line-height: 1.8;
            color: var(--text-dark);
            white-space: pre-wrap;
        }

        .info-box {
            background: var(--light-bg);
            border-left: 4px solid var(--primary-blue);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .info-box h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .info-box p {
            font-size: 14px;
            color: var(--text-light);
            line-height: 1.6;
        }

        @media print {
            .back-button,
            .paper-actions {
                display: none;
            }

            body {
                padding: 0;
            }

            .paper-container {
                box-shadow: none;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .paper-header {
                padding: 25px;
            }

            .paper-title {
                font-size: 24px;
            }

            .paper-actions {
                padding: 20px;
                flex-direction: column;
            }

            .paper-content {
                padding: 25px;
            }

            .paper-meta {
                gap: 15px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <a href="<?php echo base_url('simple_portal/student_question_papers'); ?>" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Question Papers
    </a>

    <div class="paper-container">
        <div class="paper-header">
            <div class="paper-title"><?php echo htmlspecialchars($paper->title); ?></div>
            <div style="font-size: 16px; opacity: 0.9; margin-bottom: 5px;">
                <?php echo htmlspecialchars($paper->subject_code . ' - ' . $paper->subject_name); ?>
            </div>
            
            <div class="paper-meta">
                <div class="meta-item">
                    <i class="fas fa-star"></i>
                    <span>Total Marks: <?php echo $paper->total_marks; ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span>Duration: <?php echo $paper->duration_minutes; ?> minutes</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span>Published: <?php echo date('M j, Y', strtotime($paper->published_at)); ?></span>
                </div>
            </div>
        </div>

        <div class="paper-actions">
            <a href="<?php echo base_url('simple_portal/download_question_paper/' . $paper->id); ?>" class="btn btn-primary">
                <i class="fas fa-download"></i>
                Download Paper
            </a>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i>
                Print Paper
            </button>
        </div>

        <div class="paper-content">
            <div class="info-box">
                <h3><i class="fas fa-info-circle"></i> Instructions</h3>
                <p>Read all questions carefully before answering. Manage your time wisely and attempt all questions. Show all your work for full credit.</p>
            </div>

            <div class="content-section">
                <div class="section-title">Question Paper</div>
                <div class="content-text"><?php echo htmlspecialchars($paper->formatted_content); ?></div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
