<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz->title); ?> - Quiz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1f5ea8;
            --success-green: #78b83f;
            --purple: #8b5cf6;
            --light-bg: #f8fafc;
            --white: #ffffff;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border-color: #e2e8f0;
            --error-red: #ef4444;
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

        .quiz-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
        }

        .quiz-header {
            background: linear-gradient(135deg, var(--purple), #6d28d9);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.2);
        }

        .quiz-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .quiz-info {
            display: flex;
            gap: 30px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .timer-container {
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .timer {
            font-size: 24px;
            font-weight: 700;
            color: var(--purple);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .timer.warning {
            color: var(--error-red);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        .progress-bar {
            flex: 1;
            margin: 0 30px;
        }

        .progress-text {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .progress-track {
            height: 8px;
            background: var(--border-color);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--purple), #a78bfa);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .question-card {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .question-card.answered {
            border-color: var(--success-green);
        }

        .question-number {
            display: inline-block;
            background: linear-gradient(135deg, var(--purple), #6d28d9);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .question-text {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .option {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: var(--light-bg);
            border: 2px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .option:hover {
            border-color: var(--purple);
            background: #f5f3ff;
        }

        .option input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
            accent-color: var(--purple);
        }

        .option label {
            flex: 1;
            cursor: pointer;
            font-size: 16px;
        }

        .option.selected {
            border-color: var(--purple);
            background: #f5f3ff;
        }

        .quiz-actions {
            position: sticky;
            bottom: 20px;
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 15px;
            justify-content: space-between;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-secondary {
            background: var(--light-bg);
            color: var(--text-dark);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--white);
            border-color: var(--purple);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--purple), #6d28d9);
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Results Modal */
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
            background: var(--white);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .results-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .score-circle {
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 800;
            color: white;
            background: linear-gradient(135deg, var(--purple), #6d28d9);
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
        }

        .results-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .summary-item {
            text-align: center;
            padding: 15px;
            background: var(--light-bg);
            border-radius: 10px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--purple);
        }

        .summary-label {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 5px;
        }

        .result-item {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            border-left: 4px solid;
        }

        .result-item.correct {
            background: #f0fdf4;
            border-color: var(--success-green);
        }

        .result-item.incorrect {
            background: #fef2f2;
            border-color: var(--error-red);
        }

        .result-question {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .result-answer {
            font-size: 14px;
            color: var(--text-light);
        }

        @media (max-width: 768px) {
            .quiz-container {
                padding: 15px;
            }

            .quiz-header {
                padding: 20px;
            }

            .quiz-title {
                font-size: 22px;
            }

            .timer-container {
                flex-direction: column;
                gap: 15px;
            }

            .progress-bar {
                margin: 0;
                width: 100%;
            }

            .results-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="quiz-container">
    <div class="quiz-header">
        <div class="quiz-title"><?php echo htmlspecialchars($quiz->title); ?></div>
        <div style="font-size: 16px; opacity: 0.9;">
            <?php echo htmlspecialchars($quiz->subject_code . ' - ' . $quiz->subject_name); ?>
        </div>
        <div class="quiz-info">
            <div class="info-item">
                <i class="fas fa-list"></i>
                <span><?php echo count($questions); ?> Questions</span>
            </div>
            <div class="info-item">
                <i class="fas fa-clock"></i>
                <span><?php echo isset($quiz->num_questions) && $quiz->num_questions > 0 ? ($quiz->num_questions * 2) : 30; ?> Minutes</span>
            </div>
        </div>
    </div>

    <div class="timer-container">
        <div class="timer" id="timer">
            <i class="fas fa-clock"></i>
            <span id="time-display">00:00</span>
        </div>
        <div class="progress-bar">
            <div class="progress-text">
                Progress: <span id="progress-text">0/<?php echo count($questions); ?></span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="progress-fill" style="width: 0%"></div>
            </div>
        </div>
        <button class="btn btn-secondary" onclick="confirmExit()">
            <i class="fas fa-times"></i>
            Exit Quiz
        </button>
    </div>

    <form id="quiz-form">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz->id; ?>">
        
        <?php foreach ($questions as $index => $question): ?>
            <div class="question-card" data-question="<?php echo $index + 1; ?>">
                <div class="question-number">Question <?php echo $index + 1; ?></div>
                <div class="question-text"><?php echo htmlspecialchars($question['question']); ?></div>
                
                <div class="options">
                    <?php foreach ($question['options'] as $key => $option): ?>
                        <div class="option" onclick="selectOption(this)">
                            <input 
                                type="radio" 
                                name="answers[<?php echo $index + 1; ?>]" 
                                value="<?php echo $key; ?>" 
                                id="q<?php echo $index + 1; ?>_<?php echo $key; ?>"
                                onchange="updateProgress()"
                            >
                            <label for="q<?php echo $index + 1; ?>_<?php echo $key; ?>">
                                <strong><?php echo $key; ?>)</strong> <?php echo htmlspecialchars($option); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </form>

    <div class="quiz-actions">
        <a href="<?php echo base_url('simple_portal/student_quizzes'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Quizzes
        </a>
        <button class="btn btn-primary" onclick="submitQuiz()" id="submit-btn">
            <i class="fas fa-check"></i>
            Submit Quiz
        </button>
    </div>
</div>

<!-- Results Modal -->
<div class="modal" id="results-modal">
    <div class="modal-content">
        <div class="results-header">
            <div class="score-circle" id="score-display">0%</div>
            <h2 id="results-title">Quiz Completed!</h2>
        </div>
        
        <div class="results-summary">
            <div class="summary-item">
                <div class="summary-value" id="correct-count">0</div>
                <div class="summary-label">Correct</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" id="incorrect-count">0</div>
                <div class="summary-label">Incorrect</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" id="time-taken">0:00</div>
                <div class="summary-label">Time Taken</div>
            </div>
        </div>

        <div id="results-details"></div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="<?php echo base_url('simple_portal/student_quizzes'); ?>" class="btn btn-secondary" style="flex: 1;">
                <i class="fas fa-list"></i>
                Back to Quizzes
            </a>
            <a href="<?php echo base_url('simple_portal'); ?>" class="btn btn-primary" style="flex: 1;">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </div>
    </div>
</div>

<script>
const totalQuestions = <?php echo count($questions); ?>;
const duration = <?php echo isset($quiz->num_questions) && $quiz->num_questions > 0 ? ($quiz->num_questions * 2) : 30; ?>;
let startTime = Date.now();
let timerInterval;

// Start timer
function startTimer() {
    const endTime = startTime + (duration * 60 * 1000);
    
    timerInterval = setInterval(() => {
        const now = Date.now();
        const remaining = endTime - now;
        
        if (remaining <= 0) {
            clearInterval(timerInterval);
            autoSubmitQuiz();
            return;
        }
        
        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);
        
        document.getElementById('time-display').textContent = 
            `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        
        // Warning when 5 minutes left
        if (remaining < 5 * 60 * 1000) {
            document.getElementById('timer').classList.add('warning');
        }
    }, 1000);
}

// Select option
function selectOption(element) {
    const radio = element.querySelector('input[type="radio"]');
    radio.checked = true;
    
    // Remove selected class from siblings
    element.parentElement.querySelectorAll('.option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Add selected class
    element.classList.add('selected');
    
    // Mark question card as answered
    const card = element.closest('.question-card');
    card.classList.add('answered');
    
    updateProgress();
}

// Update progress
function updateProgress() {
    const answered = document.querySelectorAll('input[type="radio"]:checked').length;
    const percentage = (answered / totalQuestions) * 100;
    
    document.getElementById('progress-text').textContent = `${answered}/${totalQuestions}`;
    document.getElementById('progress-fill').style.width = `${percentage}%`;
    
    // Enable submit button if all answered
    document.getElementById('submit-btn').disabled = (answered < totalQuestions);
}

// Submit quiz
async function submitQuiz() {
    const answered = document.querySelectorAll('input[type="radio"]:checked').length;
    
    if (answered < totalQuestions) {
        if (!confirm(`You have only answered ${answered} out of ${totalQuestions} questions. Submit anyway?`)) {
            return;
        }
    }
    
    clearInterval(timerInterval);
    
    const formData = new FormData(document.getElementById('quiz-form'));
    const timeTaken = Math.floor((Date.now() - startTime) / 1000);
    formData.append('time_taken', timeTaken);
    
    try {
        const response = await fetch('<?php echo base_url('simple_portal/submit_quiz'); ?>', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showResults(result);
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Failed to submit quiz. Please try again.');
        console.error(error);
    }
}

// Auto submit when time runs out
function autoSubmitQuiz() {
    alert('Time is up! Submitting your quiz...');
    submitQuiz();
}

// Show results
function showResults(result) {
    document.getElementById('score-display').textContent = result.score + '%';
    document.getElementById('correct-count').textContent = result.correct_answers;
    document.getElementById('incorrect-count').textContent = result.total_questions - result.correct_answers;
    
    const minutes = Math.floor(result.time_taken / 60);
    const seconds = result.time_taken % 60;
    document.getElementById('time-taken').textContent = `${minutes}:${String(seconds).padStart(2, '0')}`;
    
    // Show detailed results
    const detailsHtml = result.results.map(r => `
        <div class="result-item ${r.is_correct ? 'correct' : 'incorrect'}">
            <div class="result-question">
                <i class="fas fa-${r.is_correct ? 'check-circle' : 'times-circle'}"></i>
                Question ${r.question_num}: ${r.question}
            </div>
            <div class="result-answer">
                Your answer: ${r.user_answer || 'Not answered'} | 
                Correct answer: ${r.correct_answer}
            </div>
        </div>
    `).join('');
    
    document.getElementById('results-details').innerHTML = detailsHtml;
    document.getElementById('results-modal').classList.add('active');
}

// Confirm exit
function confirmExit() {
    if (confirm('Are you sure you want to exit? Your progress will be lost.')) {
        window.location.href = '<?php echo base_url('simple_portal/student_quizzes'); ?>';
    }
}

// Prevent accidental page close
window.addEventListener('beforeunload', (e) => {
    if (!document.getElementById('results-modal').classList.contains('active')) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Start timer on page load
startTimer();
updateProgress();
</script>

</body>
</html>
