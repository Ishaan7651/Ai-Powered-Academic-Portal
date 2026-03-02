<?php
// Helper for date formatting
function formatEventDate($date) {
    return date('M j, Y', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->load->view('simple_portal/components/student_sidebar_css'); ?>
    <style>
        /* Page-specific styles */
        .schedule-container {
            display: flex;
            gap: 30px;
        }

        .timetable-section {
            flex: 2;
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .timetable-grid {
            display: grid;
            grid-template-columns: 100px repeat(5, 1fr);
            gap: 1px;
            background: var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .grid-header, .grid-time, .grid-cell {
            background: var(--white);
            padding: 15px 10px;
            text-align: center;
            font-size: 13px;
        }

        .grid-header {
            background: #f1f5f9;
            font-weight: 600;
            color: var(--text-dark);
            text-transform: uppercase;
            font-size: 12px;
        }

        .grid-time {
            background: #f8fafc;
            color: var(--text-light);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .grid-cell {
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: all 0.2s;
        }
        
        .grid-cell:hover {
            background-color: #f8fafc;
        }

        .class-slot {
            background: #e0f2fe;
            color: #0369a1;
            padding: 8px;
            border-radius: 6px;
            width: 90%;
            font-size: 12px;
            font-weight: 600;
            border-left: 3px solid #0284c7;
        }

        .class-slot.lab {
            background: #f0fdf4;
            color: #15803d;
            border-left-color: #16a34a;
        }

        .events-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .event-card {
            background: var(--white);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        .event-list {
            list-style: none;
        }

        .event-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .event-item:last-child {
            border-bottom: none;
        }

        .event-date {
            text-align: center;
            min-width: 50px;
        }

        .date-day {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-blue);
            display: block;
        }

        .date-month {
            font-size: 12px;
            color: var(--text-light);
            text-transform: uppercase;
        }

        .event-details h4 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-dark);
        }

        .event-details p {
            font-size: 13px;
            color: var(--text-light);
            margin-bottom: 4px;
        }

        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .tag-quiz {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        .tag-assignment {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        @media (max-width: 1024px) {
            .schedule-container {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .timetable-grid {
                overflow-x: auto;
                min-width: 600px;
            }
        }
    </style>
</head>
<body>

<div class="portal-container">
    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'schedule']); ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="page-title">My Schedule</div>
            <a href="<?php echo base_url('simple_portal/profile'); ?>" class="user-profile">
                <div class="user-avatar"><?php echo strtoupper(substr($username ?? $this->session->userdata('username'), 0, 1)); ?></div>
                <div class="user-info">
                    <div class="user-name"><?php echo $username ?? $this->session->userdata('username'); ?></div>
                    <div class="user-role">Student</div>
                </div>
            </a>
        </div>

        <div class="content-area">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Academic Schedule</h1>
                    <p>View your weekly class timetable and upcoming academic events</p>
                </div>
            </div>

        <div class="schedule-container">
            <!-- Weekly Timetable -->
            <div class="timetable-section">
                <div class="section-title">
                    <i class="fas fa-clock"></i> Weekly Class Timetable
                </div>
                
                <div style="overflow-x: auto;">
                    <div class="timetable-grid">
                        <!-- Header Row -->
                        <div class="grid-header">Time</div>
                        <div class="grid-header">Monday</div>
                        <div class="grid-header">Tuesday</div>
                        <div class="grid-header">Wednesday</div>
                        <div class="grid-header">Thursday</div>
                        <div class="grid-header">Friday</div>

                        <!-- 9:00 AM -->
                        <div class="grid-time">09:00 AM</div>
                        <div class="grid-cell">
                            <div class="class-slot">Mathematics<br><small>Room 101</small></div>
                        </div>
                        <div class="grid-cell">
                           <div class="class-slot">Physics<br><small>Room 102</small></div>
                        </div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell">
                             <div class="class-slot">Chemistry<br><small>Room 103</small></div>
                        </div>
                         <div class="grid-cell">
                            <div class="class-slot">Mathematics<br><small>Room 101</small></div>
                        </div>

                        <!-- 10:00 AM -->
                        <div class="grid-time">10:00 AM</div>
                        <div class="grid-cell">
                             <div class="class-slot">Computer Sci<br><small>Lab A</small></div>
                        </div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell">
                            <div class="class-slot">Physics<br><small>Room 102</small></div>
                        </div>
                        <div class="grid-cell">
                             <div class="class-slot">English<br><small>Room 201</small></div>
                        </div>
                        <div class="grid-cell"></div>

                        <!-- 11:00 AM -->
                        <div class="grid-time">11:00 AM</div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell">
                             <div class="class-slot">Mathematics<br><small>Room 101</small></div>
                        </div>
                        <div class="grid-cell">
                             <div class="class-slot lab">Chemistry Lab<br><small>Lab B</small></div>
                        </div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell">
                             <div class="class-slot">Computer Sci<br><small>Lab A</small></div>
                        </div>

                        <!-- 12:00 PM Break -->
                        <div class="grid-time">12:00 PM</div>
                        <div class="grid-cell" style="grid-column: span 5; background: #fafafa; color: #999; font-style: italic;">LUNCH BREAK</div>

                        <!-- 1:00 PM -->
                        <div class="grid-time">01:00 PM</div>
                        <div class="grid-cell">
                             <div class="class-slot lab">Physics Lab<br><small>Lab C</small></div>
                        </div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell">
                             <div class="class-slot">English<br><small>Room 201</small></div>
                        </div>
                        <div class="grid-cell">
                             <div class="class-slot">History<br><small>Room 205</small></div>
                        </div>
                        <div class="grid-cell"></div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events Sidebar -->
            <div class="events-section">
                <div class="event-card">
                    <div class="section-title">
                        <i class="fas fa-bullhorn"></i> Upcoming Events
                    </div>
                    <?php if (empty($events)): ?>
                        <p class="text-muted text-center py-3">No upcoming events scheduled.</p>
                    <?php else: ?>
                        <ul class="event-list">
                            <?php foreach ($events as $event): ?>
                                <li class="event-item">
                                    <div class="event-date">
                                        <span class="date-day"><?php echo date('d', strtotime($event['date'])); ?></span>
                                        <span class="date-month"><?php echo date('M', strtotime($event['date'])); ?></span>
                                    </div>
                                    <div class="event-details">
                                        <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                                        <p><?php echo htmlspecialchars($event['subject']); ?></p>
                                        <span class="tag tag-<?php echo strtolower($event['type']); ?>">
                                            <?php echo htmlspecialchars($event['type']); ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="event-card" style="background: linear-gradient(135deg, var(--primary-dark), #334155); color: white; border: none;">
                    <h3 style="font-size: 18px; margin-bottom: 10px;">Exam Schedule</h3>
                    <p style="font-size: 14px; opacity: 0.9; margin-bottom: 15px;">
                        Mid-semester exams are approaching. Download the full provisional datasheet.
                    </p>
                    <button class="btn" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); width: 100%; padding: 10px; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-download me-2"></i> Download Datesheet
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>
