<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Test Route Controller
 * Simple controller to test if routing is working on Hostinger
 * 
 * Access at: https://yourdomain.com/test_route
 * Or with index.php: https://yourdomain.com/index.php/test_route
 * 
 * DELETE THIS FILE AFTER TESTING!
 */
class Test_route extends CI_Controller
{
    public function index()
    {
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Route Test - Success!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .success {
            background: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .info h2 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .test-item {
            margin: 10px 0;
            padding: 10px;
            background: #f9f9f9;
            border-left: 4px solid #2196F3;
        }
        code {
            background: #eee;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="success">
        ✅ Routing is Working!
    </div>
    
    <div class="info">
        <h2>Test Results</h2>
        
        <div class="test-item">
            <strong>Controller:</strong> Test_route
        </div>
        
        <div class="test-item">
            <strong>Method:</strong> index()
        </div>
        
        <div class="test-item">
            <strong>Current URL:</strong> <code>' . current_url() . '</code>
        </div>
        
        <div class="test-item">
            <strong>Base URL:</strong> <code>' . base_url() . '</code>
        </div>
        
        <div class="test-item">
            <strong>URI String:</strong> <code>' . $this->uri->uri_string() . '</code>
        </div>
        
        <h2>What This Means</h2>
        <p>If you can see this page, it means:</p>
        <ul>
            <li>✅ CodeIgniter routing is working</li>
            <li>✅ Controllers are accessible</li>
            <li>✅ Base configuration is correct</li>
        </ul>
        
        <h2>Next Steps</h2>
        <p>Now test the AI_buddy routes:</p>
        <ol>
            <li>Try: <code>' . base_url('ai_buddy/generate_quiz') . '</code></li>
            <li>Try: <code>' . base_url('ai_buddy/generate_question_paper') . '</code></li>
            <li>Try: <code>' . base_url('ai_buddy/generate_assignment') . '</code></li>
        </ol>
        
        <p>If those don\'t work but this page does, the issue is specific to the AI_buddy controller.</p>
        
        <div class="warning">
            <strong>⚠️ Important:</strong> Delete this <code>Test_route.php</code> file after testing!
        </div>
    </div>
</body>
</html>';
    }
    
    public function test_method()
    {
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Test Method - Success!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .success {
            background: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 24px;
        }
    </style>
</head>
<body>
    <div class="success">
        ✅ Test Method Working!
    </div>
    <p style="text-align: center; margin-top: 20px;">
        URL: <code>' . current_url() . '</code>
    </p>
</body>
</html>';
    }
}
