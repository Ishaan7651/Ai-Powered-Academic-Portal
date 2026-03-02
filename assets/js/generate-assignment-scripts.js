/**
 * Generate Assignment Page JavaScript
 * Enhanced functionality for AI assignment generation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize assignment generator
    initializeAssignmentGenerator();
    initializeTopicsManagement();
    initializeDifficultySelection();
    initializeQuestionCountSelector();
    initializeFormValidation();
    initializeFormSubmission();
    initializeRecentActions();
    initializeAIExamples();
    initializeKeyboardShortcuts();
});

/**
 * Initialize assignment generator
 */
function initializeAssignmentGenerator() {
    // Set focus on first input
    const subjectSelect = document.getElementById('subject');
    if (subjectSelect) {
        subjectSelect.focus();
    }
    
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize auto-save
    initializeAutoSave();
    
    // Initialize preview generation
    initializePreviewGeneration();
}

/**
 * Initialize topics management
 */
function initializeTopicsManagement() {
    const topicsInput = document.getElementById('topics');
    const topicsPreview = document.getElementById('topicsPreview');
    const topicsList = document.getElementById('topicsList');
    
    if (!topicsInput || !topicsPreview || !topicsList) return;
    
    // Load saved topics
    loadSavedTopics();
    
    // Handle input changes
    topicsInput.addEventListener('input', function() {
        updateTopicsPreview();
        saveTopics();
    });
    
    // Handle paste event
    topicsInput.addEventListener('paste', function(e) {
        setTimeout(() => {
            updateTopicsPreview();
            saveTopics();
        }, 100);
    });
}

/**
 * Update topics preview
 */
function updateTopicsPreview() {
    const topicsInput = document.getElementById('topics');
    const topicsPreview = document.getElementById('topicsPreview');
    const topicsList = document.getElementById('topicsList');
    
    if (!topicsInput || !topicsPreview || !topicsList) return;
    
    const topicsText = topicsInput.value.trim();
    topicsList.innerHTML = '';
    
    if (!topicsText) {
        topicsPreview.classList.remove('show');
        return;
    }
    
    // Split topics by comma, trim whitespace, filter out empty strings
    const topics = topicsText.split(',')
        .map(topic => topic.trim())
        .filter(topic => topic.length > 0);
    
    if (topics.length === 0) {
        topicsPreview.classList.remove('show');
        return;
    }
    
    // Create topic tags
    topics.forEach((topic, index) => {
        const topicTag = document.createElement('div');
        topicTag.className = 'topic-tag';
        topicTag.innerHTML = `
            ${topic}
            <button type="button" class="remove-topic" onclick="removeTopic(${index})">
                <i class="fa fa-times"></i>
            </button>
        `;
        topicsList.appendChild(topicTag);
    });
    
    topicsPreview.classList.add('show');
}

/**
 * Add topic from example
 */
function addTopic(topic) {
    const topicsInput = document.getElementById('topics');
    const currentTopics = topicsInput.value.trim();
    
    if (currentTopics) {
        // Check if topic already exists
        const topics = currentTopics.split(',').map(t => t.trim());
        if (topics.includes(topic)) {
            showNotification(`"${topic}" is already added`, 'info');
            return;
        }
        
        // Add topic with comma separator
        topicsInput.value = currentTopics + ', ' + topic;
    } else {
        topicsInput.value = topic;
    }
    
    updateTopicsPreview();
    saveTopics();
    
    // Show feedback
    const tag = event.target.closest('.example-tag');
    if (tag) {
        tag.style.transform = 'scale(0.95)';
        setTimeout(() => {
            tag.style.transform = '';
        }, 200);
    }
}

/**
 * Remove topic by index
 */
function removeTopic(index) {
    const topicsInput = document.getElementById('topics');
    const currentTopics = topicsInput.value.trim();
    
    if (!currentTopics) return;
    
    const topics = currentTopics.split(',')
        .map(topic => topic.trim())
        .filter(topic => topic.length > 0);
    
    if (index >= 0 && index < topics.length) {
        topics.splice(index, 1);
        topicsInput.value = topics.join(', ');
        updateTopicsPreview();
        saveTopics();
    }
}

/**
 * Clear all topics
 */
function clearAllTopics() {
    const topicsInput = document.getElementById('topics');
    const topicsPreview = document.getElementById('topicsPreview');
    
    topicsInput.value = '';
    topicsPreview.classList.remove('show');
    saveTopics();
    
    showNotification('All topics cleared', 'info');
}

/**
 * Save topics to localStorage
 */
function saveTopics() {
    const topicsInput = document.getElementById('topics');
    if (topicsInput) {
        localStorage.setItem('ai_assignment_topics', topicsInput.value);
    }
}

/**
 * Load saved topics from localStorage
 */
function loadSavedTopics() {
    const savedTopics = localStorage.getItem('ai_assignment_topics');
    if (savedTopics) {
        const topicsInput = document.getElementById('topics');
        topicsInput.value = savedTopics;
        updateTopicsPreview();
    }
}

/**
 * Initialize difficulty selection
 */
function initializeDifficultySelection() {
    const difficultyOptions = document.querySelectorAll('.difficulty-option');
    
    difficultyOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            difficultyOptions.forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add active class to clicked option
            this.classList.add('selected');
            
            // Save selection
            const level = this.getAttribute('data-level');
            localStorage.setItem('ai_assignment_difficulty', level);
            
            // Update preview if exists
            updateDifficultyPreview(level);
        });
    });
    
    // Load saved difficulty
    const savedDifficulty = localStorage.getItem('ai_assignment_difficulty');
    if (savedDifficulty) {
        const option = document.querySelector(`.difficulty-option[data-level="${savedDifficulty}"]`);
        if (option) {
            option.click();
        }
    }
}

/**
 * Update difficulty preview
 */
function updateDifficultyPreview(level) {
    const previewElement = document.getElementById('difficultyPreview');
    if (!previewElement) return;
    
    const levels = {
        beginner: { text: 'Beginner Level', color: '#10b981', icon: 'fa-seedling' },
        intermediate: { text: 'Intermediate Level', color: '#f59e0b', icon: 'fa-tree' },
        advanced: { text: 'Advanced Level', color: '#ef4444', icon: 'fa-mountain' }
    };
    
    const levelInfo = levels[level];
    if (levelInfo) {
        previewElement.innerHTML = `
            <i class="fa ${levelInfo.icon}" style="color: ${levelInfo.color}"></i>
            <span>${levelInfo.text}</span>
        `;
        previewElement.style.display = 'flex';
    }
}

/**
 * Initialize question count selector
 */
function initializeQuestionCountSelector() {
    const countButtons = document.querySelectorAll('.count-btn');
    const questionDots = document.querySelectorAll('.question-dot');
    const countText = document.getElementById('countText');
    const hiddenInput = document.getElementById('question_count');
    
    // Set initial active state
    setQuestionCount(5);
    
    countButtons.forEach(button => {
        button.addEventListener('click', function() {
            const count = parseInt(this.getAttribute('data-count'));
            setQuestionCount(count);
        });
    });
}

/**
 * Set question count
 */
function setQuestionCount(count) {
    const countButtons = document.querySelectorAll('.count-btn');
    const questionDots = document.querySelectorAll('.question-dot');
    const countText = document.getElementById('countText');
    const hiddenInput = document.getElementById('question_count');
    
    // Update active button
    countButtons.forEach(button => {
        const buttonCount = parseInt(button.getAttribute('data-count'));
        if (buttonCount === count) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    });
    
    // Update visual dots
    questionDots.forEach((dot, index) => {
        if (index < count) {
            dot.classList.add('active');
            dot.style.background = '#8b5cf6';
        } else {
            dot.classList.remove('active');
            dot.style.background = '#cbd5e1';
        }
    });
    
    // Update text and hidden input
    countText.textContent = `${count} question${count !== 1 ? 's' : ''} selected`;
    hiddenInput.value = count;
    
    // Save preference
    localStorage.setItem('ai_assignment_question_count', count);
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    const form = document.getElementById('assignmentForm');
    if (!form) return;
    
    // Validate subject selection
    const subjectSelect = document.getElementById('subject');
    if (subjectSelect) {
        subjectSelect.addEventListener('change', function() {
            validateField(this);
        });
    }
    
    // Validate topics
    const topicsInput = document.getElementById('topics');
    if (topicsInput) {
        topicsInput.addEventListener('blur', function() {
            validateTopics(this);
        });
    }
    
    // Validate difficulty
    const difficultyRadios = document.querySelectorAll('input[name="difficulty"]');
    difficultyRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            validateDifficulty();
        });
    });
}

/**
 * Validate field
 */
function validateField(field) {
    const value = field.value.trim();
    const isValid = value.length > 0;
    
    if (isValid) {
        field.classList.remove('invalid');
        field.classList.add('valid');
    } else {
        field.classList.remove('valid');
        field.classList.add('invalid');
    }
    
    return isValid;
}

/**
 * Validate topics
 */
function validateTopics(field) {
    const value = field.value.trim();
    const topics = value.split(',').map(t => t.trim()).filter(t => t.length > 0);
    const isValid = topics.length > 0 && topics.length <= 10;
    
    if (isValid) {
        field.classList.remove('invalid');
        field.classList.add('valid');
    } else {
        field.classList.remove('valid');
        field.classList.add('invalid');
        
        if (topics.length === 0) {
            showNotification('Please enter at least one topic', 'error');
        } else if (topics.length > 10) {
            showNotification('Maximum 10 topics allowed', 'error');
        }
    }
    
    return isValid;
}

/**
 * Validate difficulty
 */
function validateDifficulty() {
    const difficultySelected = document.querySelector('input[name="difficulty"]:checked');
    const isValid = !!difficultySelected;
    
    const difficultyOptions = document.querySelectorAll('.difficulty-option');
    difficultyOptions.forEach(option => {
        if (isValid) {
            option.classList.remove('invalid');
        } else {
            option.classList.add('invalid');
        }
    });
    
    return isValid;
}

/**
 * Validate entire form
 */
function validateForm() {
    let isValid = true;
    
    // Validate subject
    const subjectSelect = document.getElementById('subject');
    if (!validateField(subjectSelect)) {
        isValid = false;
    }
    
    // Validate topics
    const topicsInput = document.getElementById('topics');
    if (!validateTopics(topicsInput)) {
        isValid = false;
    }
    
    // Validate difficulty
    if (!validateDifficulty()) {
        isValid = false;
    }
    
    // Validate semester
    const semesterSelect = document.getElementById('semester');
    if (!validateField(semesterSelect)) {
        isValid = false;
    }
    
    return isValid;
}

/**
 * Initialize form submission
 */
function initializeFormSubmission() {
    const form = document.getElementById('assignmentForm');
    if (!form) return;
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            showNotification('Please fill in all required fields correctly', 'error');
            scrollToFirstError();
            return;
        }
        
        // Show loading state
        showGeneratingState();
        
        // Simulate API call (replace with actual API call)
        simulateAIGeneration();
    });
    
    // Add form reset handler
    const resetButton = form.querySelector('button[type="reset"]');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            setTimeout(() => {
                resetForm();
                showNotification('Form has been reset', 'info');
            }, 100);
        });
    }
}

/**
 * Reset form
 */
function resetForm() {
    const form = document.getElementById('assignmentForm');
    if (!form) return;
    
    form.reset();
    
    // Reset custom UI elements
    const topicsPreview = document.getElementById('topicsPreview');
    if (topicsPreview) {
        topicsPreview.classList.remove('show');
    }
    
    const countButtons = document.querySelectorAll('.count-btn');
    countButtons.forEach(button => {
        button.classList.remove('active');
    });
    
    // Reset to default values
    setQuestionCount(5);
    
    // Clear validation states
    const validatedElements = form.querySelectorAll('.valid, .invalid');
    validatedElements.forEach(element => {
        element.classList.remove('valid', 'invalid');
    });
    
    // Clear localStorage for this form
    localStorage.removeItem('ai_assignment_topics');
    localStorage.removeItem('ai_assignment_difficulty');
    localStorage.removeItem('ai_assignment_question_count');
}

/**
 * Scroll to first error
 */
function scrollToFirstError() {
    const firstError = document.querySelector('.invalid');
    if (firstError) {
        firstError.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        firstError.focus();
    }
}

/**
 * Show generating state
 */
function showGeneratingState() {
    const generateBtn = document.getElementById('generateBtn');
    if (!generateBtn) return;
    
    // Save original content
    const originalContent = generateBtn.innerHTML;
    const originalWidth = generateBtn.offsetWidth;
    
    // Update button state
    generateBtn.disabled = true;
    generateBtn.style.width = originalWidth + 'px';
    generateBtn.innerHTML = `
        <div class="generating-content">
            <div class="ai-spinner">
                <div class="spinner-dot"></div>
                <div class="spinner-dot"></div>
                <div class="spinner-dot"></div>
            </div>
            <span>Generating with AI...</span>
        </div>
    `;
    
    // Store original state
    generateBtn.dataset.originalContent = originalContent;
    
    // Add generating styles
    const generatingStyles = document.createElement('style');
    generatingStyles.textContent = `
        .generating-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .ai-spinner {
            display: flex;
            gap: 4px;
        }
        
        .spinner-dot {
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            animation: aiPulse 1.4s infinite ease-in-out;
        }
        
        .spinner-dot:nth-child(1) {
            animation-delay: -0.32s;
        }
        
        .spinner-dot:nth-child(2) {
            animation-delay: -0.16s;
        }
        
        @keyframes aiPulse {
            0%, 80%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(generatingStyles);
}

/**
 * Restore generate button
 */
function restoreGenerateButton() {
    const generateBtn = document.getElementById('generateBtn');
    if (!generateBtn || !generateBtn.dataset.originalContent) return;
    
    generateBtn.disabled = false;
    generateBtn.innerHTML = generateBtn.dataset.originalContent;
    generateBtn.style.width = 'auto';
    delete generateBtn.dataset.originalContent;
}

/**
 * Simulate AI generation
 */
function simulateAIGeneration() {
    // Get form data
    const formData = new FormData(document.getElementById('assignmentForm'));
    const data = Object.fromEntries(formData.entries());
    
    // Show progress
    showGenerationProgress();
    
    // Simulate API delay (3-6 seconds)
    const delay = 3000 + Math.random() * 3000;
    
    setTimeout(() => {
        // Hide progress
        hideGenerationProgress();
        
        // Restore button
        restoreGenerateButton();
        
        // Show success message
        showNotification('Assignment generated successfully!', 'success');
        
        // In a real app, this would redirect to the assignment result page
        // For now, simulate a redirect
        setTimeout(() => {
            // Save assignment data
            const assignment = {
                id: Date.now(),
                title: `AI Assignment - ${data.subject}`,
                subject: data.subject,
                difficulty: data.difficulty,
                question_count: data.question_count,
                time_limit: data.time_limit,
                created_at: new Date().toISOString()
            };
            
            // Save to localStorage for recent assignments
            saveRecentAssignment(assignment);
            
            // Redirect to result page (simulated)
            window.location.href = '<?php echo base_url('simple_portal/assignment_result'); ?>?generated=true';
        }, 1000);
    }, delay);
}

/**
 * Show generation progress
 */
function showGenerationProgress() {
    const progressOverlay = document.createElement('div');
    progressOverlay.id = 'generation-progress';
    progressOverlay.innerHTML = `
        <div class="progress-content">
            <div class="ai-thinking">
                <i class="fa fa-robot"></i>
                <div class="thinking-dots">
                    <div class="thinking-dot"></div>
                    <div class="thinking-dot"></div>
                    <div class="thinking-dot"></div>
                </div>
            </div>
            <h4>AI is Generating Your Assignment</h4>
            <p>Gemini AI is creating questions based on your parameters...</p>
            <div class="progress-stages">
                <div class="stage active">
                    <span class="stage-icon">1</span>
                    <span class="stage-text">Analyzing Topics</span>
                </div>
                <div class="stage">
                    <span class="stage-icon">2</span>
                    <span class="stage-text">Generating Questions</span>
                </div>
                <div class="stage">
                    <span class="stage-icon">3</span>
                    <span class="stage-text">Formatting Assignment</span>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(progressOverlay);
    document.body.style.overflow = 'hidden';
    
    // Add progress styles
    const progressStyles = document.createElement('style');
    progressStyles.textContent = `
        #generation-progress {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }
        
        .progress-content {
            text-align: center;
            max-width: 500px;
            padding: 40px;
        }
        
        .ai-thinking {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .ai-thinking i {
            font-size: 48px;
            color: #8b5cf6;
        }
        
        .thinking-dots {
            display: flex;
            gap: 8px;
        }
        
        .thinking-dot {
            width: 12px;
            height: 12px;
            background: #8b5cf6;
            border-radius: 50%;
            animation: thinking 1.4s infinite ease-in-out;
        }
        
        .thinking-dot:nth-child(1) {
            animation-delay: -0.32s;
        }
        
        .thinking-dot:nth-child(2) {
            animation-delay: -0.16s;
        }
        
        @keyframes thinking {
            0%, 80%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .progress-content h4 {
            color: #1e293b;
            font-size: 24px;
            margin-bottom: 12px;
            font-weight: 700;
        }
        
        .progress-content p {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 30px;
        }
        
        .progress-stages {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
        }
        
        .stage {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }
        
        .stage.active {
            opacity: 1;
        }
        
        .stage-icon {
            width: 40px;
            height: 40px;
            background: #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #64748b;
        }
        
        .stage.active .stage-icon {
            background: #8b5cf6;
            color: white;
        }
        
        .stage-text {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        
        .stage.active .stage-text {
            color: #1e293b;
            font-weight: 600;
        }
    `;
    document.head.appendChild(progressStyles);
    
    // Animate stages
    animateProgressStages();
}

/**
 * Animate progress stages
 */
function animateProgressStages() {
    const stages = document.querySelectorAll('.stage');
    let currentStage = 0;
    
    const stageInterval = setInterval(() => {
        // Remove active class from all stages
        stages.forEach(stage => stage.classList.remove('active'));
        
        // Add active class to current stage
        stages[currentStage].classList.add('active');
        
        // Move to next stage
        currentStage++;
        
        // Loop back to first stage if we've reached the end
        if (currentStage >= stages.length) {
            currentStage = 0;
        }
    }, 1500);
    
    // Store interval ID for cleanup
    window.progressInterval = stageInterval;
}

/**
 * Hide generation progress
 */
function hideGenerationProgress() {
    const progressOverlay = document.getElementById('generation-progress');
    if (progressOverlay) {
        progressOverlay.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            progressOverlay.remove();
            document.body.style.overflow = '';
        }, 300);
    }
    
    // Clear progress interval
    if (window.progressInterval) {
        clearInterval(window.progressInterval);
        delete window.progressInterval;
    }
    
    // Add fadeOut animation if not exists
    if (!document.querySelector('#fadeOut-animation')) {
        const fadeOutStyle = document.createElement('style');
        fadeOutStyle.id = 'fadeOut-animation';
        fadeOutStyle.textContent = `
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
        `;
        document.head.appendChild(fadeOutStyle);
    }
}

/**
 * Save recent assignment
 */
function saveRecentAssignment(assignment) {
    // Get existing assignments
    let recentAssignments = JSON.parse(localStorage.getItem('recent_ai_assignments') || '[]');
    
    // Add new assignment at the beginning
    recentAssignments.unshift(assignment);
    
    // Keep only last 5 assignments
    if (recentAssignments.length > 5) {
        recentAssignments = recentAssignments.slice(0, 5);
    }
    
    // Save back to localStorage
    localStorage.setItem('recent_ai_assignments', JSON.stringify(recentAssignments));
}

/**
 * Initialize recent actions
 */
function initializeRecentActions() {
    const viewButtons = document.querySelectorAll('[onclick*="viewAssignment"]');
    const regenerateButtons = document.querySelectorAll('[onclick*="regenerate"]');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const assignmentId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            viewAssignment(assignmentId);
        });
    });
    
    regenerateButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const assignmentId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            regenerate(assignmentId);
        });
    });
}

/**
 * View assignment
 */
function viewAssignment(assignmentId) {
    showNotification(`Opening assignment ${assignmentId}...`, 'info');
    // In a real app: window.location.href = `/assignments/view/${assignmentId}`;
}

/**
 * Regenerate assignment
 */
function regenerate(assignmentId) {
    showLoading('Loading assignment data...');
    
    setTimeout(() => {
        hideLoading();
        showNotification('Preparing to regenerate assignment...', 'info');
        
        // In a real app, this would load the assignment data and populate the form
        // For now, just show a message
        setTimeout(() => {
            showNotification('Assignment data loaded. Modify parameters and click Generate.', 'success');
        }, 500);
    }, 1000);
}

/**
 * Initialize AI examples
 */
function initializeAIExamples() {
    // Add example presets
    addExamplePresets();
    
    // Initialize AI tips
    initializeAITips();
}

/**
 * Add example presets
 */
function addExamplePresets() {
    const presetsContainer = document.createElement('div');
    presetsContainer.className = 'ai-presets';
    presetsContainer.innerHTML = `
        <h5><i class="fa fa-star"></i> Quick Presets</h5>
        <div class="presets-grid">
            <button class="preset-btn" onclick="loadPreset('beginner_python')">
                <i class="fa fa-python"></i>
                <span>Python Basics</span>
                <small>Beginner • 5 questions</small>
            </button>
            <button class="preset-btn" onclick="loadPreset('dsa_intermediate')">
                <i class="fa fa-sitemap"></i>
                <span>Data Structures</span>
                <small>Intermediate • 7 questions</small>
            </button>
            <button class="preset-btn" onclick="loadPreset('ai_advanced')">
                <i class="fa fa-brain"></i>
                <span>AI Concepts</span>
                <small>Advanced • 10 questions</small>
            </button>
        </div>
    `;
    
    const generatorCard = document.querySelector('.generator-card .card-body');
    if (generatorCard) {
        generatorCard.insertBefore(presetsContainer, generatorCard.firstChild);
    }
    
    // Add presets styles
    const presetsStyles = document.createElement('style');
    presetsStyles.textContent = `
        .ai-presets {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .ai-presets h5 {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1e40af;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .presets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        
        .preset-btn {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        
        .preset-btn:hover {
            border-color: #8b5cf6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1);
        }
        
        .preset-btn i {
            font-size: 24px;
            color: #8b5cf6;
        }
        
        .preset-btn span {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }
        
        .preset-btn small {
            color: #64748b;
            font-size: 12px;
        }
    `;
    document.head.appendChild(presetsStyles);
}

/**
 * Load preset configuration
 */
function loadPreset(presetName) {
    const presets = {
        beginner_python: {
            subject: 'Programming Fundamentals',
            semester: '2',
            topics: 'Variables, Data Types, Conditionals, Loops, Functions',
            difficulty: 'beginner',
            question_count: '5',
            assignment_type: 'practice',
            time_limit: '45'
        },
        dsa_intermediate: {
            subject: 'Data Structures',
            semester: '4',
            topics: 'Arrays, Linked Lists, Stacks, Queues, Trees, Graphs',
            difficulty: 'intermediate',
            question_count: '7',
            assignment_type: 'homework',
            time_limit: '90'
        },
        ai_advanced: {
            subject: 'Artificial Intelligence',
            semester: '6',
            topics: 'Machine Learning, Neural Networks, Natural Language Processing, Computer Vision, Reinforcement Learning',
            difficulty: 'advanced',
            question_count: '10',
            assignment_type: 'exam',
            time_limit: '120'
        }
    };
    
    const preset = presets[presetName];
    if (!preset) return;
    
    // Fill form with preset data
    Object.keys(preset).forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            if (field.type === 'radio') {
                const radio = document.querySelector(`input[name="${fieldName}"][value="${preset[fieldName]}"]`);
                if (radio) radio.checked = true;
            } else {
                field.value = preset[fieldName];
            }
        }
    });
    
    // Update UI elements
    if (preset.question_count) {
        setQuestionCount(parseInt(preset.question_count));
    }
    
    if (preset.topics) {
        const topicsInput = document.getElementById('topics');
        if (topicsInput) {
            topicsInput.value = preset.topics;
            updateTopicsPreview();
            saveTopics();
        }
    }
    
    // Update difficulty UI
    if (preset.difficulty) {
        const difficultyOptions = document.querySelectorAll('.difficulty-option');
        difficultyOptions.forEach(option => {
            option.classList.remove('selected');
            if (option.getAttribute('data-level') === preset.difficulty) {
                option.classList.add('selected');
            }
        });
        localStorage.setItem('ai_assignment_difficulty', preset.difficulty);
    }
    
    showNotification(`Loaded "${presetName.replace('_', ' ')}" preset`, 'success');
    
    // Scroll to top of form
    document.querySelector('.generator-card').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

/**
 * Initialize AI tips
 */
function initializeAITips() {
    // Tips are already in the HTML, just initialize any interactive features
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    // Add tooltips to form elements
    const formElements = document.querySelectorAll('.form-group label, .example-tag, .difficulty-option');
    formElements.forEach(element => {
        const text = element.getAttribute('title') || element.textContent;
        if (text && !element.querySelector('.tooltip')) {
            element.setAttribute('title', text);
        }
    });
}

/**
 * Initialize auto-save
 */
function initializeAutoSave() {
    // Auto-save form data every 30 seconds
    setInterval(() => {
        saveFormData();
    }, 30000);
    
    // Also save on beforeunload
    window.addEventListener('beforeunload', saveFormData);
}

/**
 * Save form data
 */
function saveFormData() {
    const form = document.getElementById('assignmentForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    // Save to localStorage
    localStorage.setItem('ai_assignment_draft', JSON.stringify(data));
    
    // Show auto-save indicator
    showAutoSaveIndicator();
}

/**
 * Show auto-save indicator
 */
function showAutoSaveIndicator() {
    let indicator = document.getElementById('auto-save-indicator');
    
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'auto-save-indicator';
        indicator.innerHTML = '<i class="fa fa-save"></i> Auto-saved';
        document.body.appendChild(indicator);
        
        // Add indicator styles
        const indicatorStyles = document.createElement('style');
        indicatorStyles.textContent = `
            #auto-save-indicator {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #10b981;
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 6px;
                z-index: 9998;
                animation: slideUpIn 0.3s ease;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            }
            
            @keyframes slideUpIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(indicatorStyles);
    }
    
    // Show indicator
    indicator.style.display = 'flex';
    
    // Hide after 2 seconds
    setTimeout(() => {
        indicator.style.animation = 'slideDownOut 0.3s ease';
        setTimeout(() => {
            indicator.style.display = 'none';
            indicator.style.animation = '';
        }, 300);
    }, 2000);
    
    // Add slideDownOut animation if not exists
    if (!document.querySelector('#slideDownOut-animation')) {
        const slideDownOutStyle = document.createElement('style');
        slideDownOutStyle.id = 'slideDownOut-animation';
        slideDownOutStyle.textContent = `
            @keyframes slideDownOut {
                from {
                    opacity: 1;
                    transform: translateY(0);
                }
                to {
                    opacity: 0;
                    transform: translateY(20px);
                }
            }
        `;
        document.head.appendChild(slideDownOutStyle);
    }
}

/**
 * Initialize preview generation
 */
function initializePreviewGeneration() {
    // This would generate a live preview of the assignment
    // For now, just log to console
    console.log('Preview generation initialized');
}

/**
 * Initialize keyboard shortcuts
 */
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(event) {
        // Ctrl/Cmd + Enter to generate
        if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
            event.preventDefault();
            const generateBtn = document.getElementById('generateBtn');
            if (generateBtn) {
                generateBtn.click();
            }
        }
        
        // Ctrl/Cmd + S to save draft
        if ((event.ctrlKey || event.metaKey) && event.key === 's') {
            event.preventDefault();
            saveFormData();
        }
        
        // Ctrl/Cmd + R to reset
        if ((event.ctrlKey || event.metaKey) && event.key === 'r') {
            event.preventDefault();
            resetForm();
        }
    });
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.custom-notification');
    existingNotifications.forEach(notification => {
        notification.remove();
    });
    
    const notification = document.createElement('div');
    notification.className = `custom-notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fa ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fa fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

/**
 * Show loading overlay
 */
function showLoading(message = 'Loading...') {
    let loadingOverlay = document.getElementById('loading-overlay');
    
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loading-overlay';
        loadingOverlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <div class="loading-text">${message}</div>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
        
        // Add loading styles if not exists
        if (!document.querySelector('#loading-styles')) {
            const loadingStyles = document.createElement('style');
            loadingStyles.id = 'loading-styles';
            loadingStyles.textContent = `
                #loading-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.9);
                    backdrop-filter: blur(4px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9998;
                    animation: fadeIn 0.3s ease;
                }
                
                .loading-content {
                    text-align: center;
                }
                
                .loading-spinner {
                    width: 50px;
                    height: 50px;
                    border: 4px solid #e2e8f0;
                    border-top-color: #8b5cf6;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 16px;
                }
                
                .loading-text {
                    color: #64748b;
                    font-weight: 500;
                }
            `;
            document.head.appendChild(loadingStyles);
        }
    }
    
    loadingOverlay.style.display = 'flex';
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
}