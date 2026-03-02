<?php
// application/views/templates/base_template.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'SLAi Portal'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        <?php 
        // Include shared CSS variables and base styles
        require_once APPPATH . 'views/templates/base_styles.php';
        ?>
        <?php echo $custom_css ?? ''; ?>
    </style>
</head>
<body>
    <?php 
    // Check if user is logged in
    if (isset($show_sidebar) && $show_sidebar): 
        include APPPATH . 'views/templates/sidebar.php';
    endif;
    ?>
    
    <div class="main-container">
        <?php 
        // Topbar
        if (isset($show_topbar) && $show_topbar): 
            include APPPATH . 'views/templates/topbar.php';
        endif;
        ?>
        
        <main class="content-area">
            <?php echo $content; ?>
        </main>
    </div>
    
    <script>
        <?php echo $custom_js ?? ''; ?>
    </script>
</body>
</html>