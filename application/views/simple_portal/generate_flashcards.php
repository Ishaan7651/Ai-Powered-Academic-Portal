<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Flashcards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <?php $this->load->view('simple_portal/components/student_sidebar_css'); ?>
    
    <style>
        body {
            background: var(--light-bg) !important;
        }

        .flashcard-container {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--text-light);
        }

        .generation-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .subject-selector {
            margin-bottom: 25px;
        }

        .subject-selector label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
            display: block;
        }

        .subject-selector select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
        }

        .resources-section {
            margin-top: 25px;
        }

        .resources-section h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-dark);
        }

        .resource-checkbox {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .resource-checkbox:hover {
            border-color: var(--primary-blue);
            background: #f0f7ff;
        }

        .resource-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
        }

        .resource-info {
            flex: 1;
        }

        .resource-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .resource-meta {
            font-size: 13px;
            color: var(--text-light);
        }

        .num-cards-selector {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .num-cards-selector label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
            display: block;
        }

        .num-cards-selector input {
            width: 150px;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }

        .generate-btn {
            padding: 14px 30px;
            background: linear-gradient(135deg, #FF6B6B, #FF8E53);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .generate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        .generate-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .flashcards-display {
            display: none;
        }

        .flashcards-display.active {
            display: block;
        }

        .flashcards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .flashcard {
            perspective: 1000px;
            height: 250px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .flashcard-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }

        .flashcard.flipped .flashcard-inner {
            transform: rotateY(180deg);
        }

        .flashcard-front, .flashcard-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 12px;
            padding: 25px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .flashcard-front::-webkit-scrollbar,
        .flashcard-back::-webkit-scrollbar {
            width: 6px;
        }

        .flashcard-front::-webkit-scrollbar-track,
        .flashcard-back::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .flashcard-front::-webkit-scrollbar-thumb,
        .flashcard-back::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .flashcard-front::-webkit-scrollbar-thumb:hover,
        .flashcard-back::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        .flashcard-front {
            background: linear-gradient(135deg, #a8c0ff 0%, #c5a3ff 100%);
            color: #2d3748;
        }

        .flashcard-back {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
            color: #2d3748;
            transform: rotateY(180deg);
        }

        .flashcard-front.easy {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .flashcard-front.medium {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }

        .flashcard-front.hard {
            background: linear-gradient(135deg, #ffd3a5 0%, #fd6585 100%);
        }

        .flashcard-category {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.6);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #4a5568;
        }

        .flashcard-number {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.6);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #4a5568;
        }

        .flashcard-content {
            font-size: 16px;
            font-weight: 500;
            line-height: 1.6;
            margin-top: 50px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            width: 100%;
        }

        .flashcard-content strong,
        .modal-card-content strong {
            font-weight: 700;
            color: #1a202c;
        }

        .flashcard-content em,
        .modal-card-content em {
            font-style: italic;
            color: #2d3748;
        }

        .flashcard-content ul,
        .modal-card-content ul {
            text-align: left;
            margin: 10px 0;
            padding-left: 20px;
        }

        .flashcard-content li,
        .modal-card-content li {
            margin: 5px 0;
        }

        .flashcard-content code,
        .modal-card-content code {
            background: rgba(255, 255, 255, 0.6);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #2d3748;
        }

        .flashcard-hint {
            margin-top: auto;
            padding-top: 15px;
            font-size: 13px;
            opacity: 0.9;
        }

        /* Modal styles */
        .flashcard-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            animation: fadeIn 0.3s;
        }

        .flashcard-modal.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 700px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            animation: slideUp 0.3s;
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 30px;
            font-weight: bold;
            color: #666;
            cursor: pointer;
            z-index: 10;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f0f0f0;
            transition: all 0.3s;
        }

        .modal-close:hover {
            background: #e0e0e0;
            transform: rotate(90deg);
        }

        .modal-card {
            perspective: 1000px;
            min-height: 400px;
            padding: 20px;
        }

        .modal-card-inner {
            position: relative;
            width: 100%;
            min-height: 400px;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }

        .modal-card.flipped .modal-card-inner {
            transform: rotateY(180deg);
        }

        .modal-card-front, .modal-card-back {
            position: absolute;
            width: 100%;
            min-height: 400px;
            backface-visibility: hidden;
            border-radius: 15px;
            padding: 60px 40px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .modal-card-front {
            background: linear-gradient(135deg, #a8c0ff 0%, #c5a3ff 100%);
            color: #2d3748;
        }

        .modal-card-back {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
            color: #2d3748;
            transform: rotateY(180deg);
        }

        .modal-card-front.easy {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .modal-card-front.medium {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }

        .modal-card-front.hard {
            background: linear-gradient(135deg, #ffd3a5 0%, #fd6585 100%);
        }

        .modal-card-content {
            font-size: 22px;
            font-weight: 500;
            line-height: 1.8;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .modal-flip-btn {
            margin-top: 30px;
            padding: 12px 30px;
            background: rgba(255, 255, 255, 0.7);
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 25px;
            color: #4a5568;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .modal-flip-btn:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .modal-navigation {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 0 0 20px 20px;
        }

        .nav-btn {
            padding: 10px 20px;
            background: #4A76A8;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .nav-btn:hover {
            background: #3a5f8a;
            transform: translateY(-2px);
        }

        .nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .loading-spinner {
            text-align: center;
            padding: 40px;
            display: none;
        }

        .loading-spinner.active {
            display: block;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-blue);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .flashcard-controls {
            text-align: center;
            margin: 30px 0;
        }

        .control-btn {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .flip-all-btn {
            background: #4A76A8;
            color: white;
        }

        .flip-all-btn:hover {
            background: #3a5f8a;
        }

        .shuffle-btn {
            background: #759B49;
            color: white;
        }

        .shuffle-btn:hover {
            background: #5f7d3a;
        }
    </style>
</head>
<body>

<?php $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'flashcards']); ?>

<div class="flashcard-container">
    <div class="page-header">
        <h1><i class="fas fa-layer-group"></i> Generate Flashcards</h1>
        <p>Select resources to create interactive study flashcards</p>
    </div>

    <div id="alertContainer"></div>

    <div class="generation-card">
        <div class="subject-selector">
            <label for="subjectSelect">
                <i class="fas fa-book"></i> Select Subject
            </label>
            <select id="subjectSelect" onchange="loadSubjectResources()">
                <option value="">-- Choose a subject --</option>
                <?php foreach ($enrolled_subjects as $subject): ?>
                    <option value="<?php echo $subject->id; ?>">
                        <?php echo htmlspecialchars($subject->subject_code . ' - ' . $subject->subject_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="resources-section" id="resourcesSection" style="display: none;">
            <h3><i class="fas fa-folder-open"></i> Select Resources (Choose multiple)</h3>
            <div id="resourcesList"></div>
            
            <div class="num-cards-selector">
                <label for="numCards">
                    <i class="fas fa-hashtag"></i> Number of Flashcards
                </label>
                <input type="number" id="numCards" value="15" min="5" max="30" step="5">
            </div>

            <button class="generate-btn" id="generateBtn" onclick="generateFlashcards()" disabled>
                <i class="fas fa-magic"></i>
                Generate Flashcards
            </button>
        </div>
    </div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
        <p>Generating your flashcards... This may take a moment.</p>
    </div>

    <div class="flashcards-display" id="flashcardsDisplay">
        <h2><i class="fas fa-layer-group"></i> Your Flashcards</h2>
        <p>Click on any card to view it in detail</p>
        
        <div class="flashcard-controls">
            <button class="control-btn flip-all-btn" onclick="flipAllCards()">
                <i class="fas fa-sync-alt"></i> Flip All
            </button>
            <button class="control-btn shuffle-btn" onclick="shuffleCards()">
                <i class="fas fa-random"></i> Shuffle
            </button>
        </div>

        <div class="flashcards-grid" id="flashcardsGrid"></div>
    </div>

    <!-- Modal for expanded flashcard view -->
    <div class="flashcard-modal" id="flashcardModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <div class="modal-card" id="modalCard">
                <div class="modal-card-inner" id="modalCardInner">
                    <div class="modal-card-front" id="modalFront">
                        <div class="flashcard-number" id="modalNumber"></div>
                        <div class="flashcard-category" id="modalCategory"></div>
                        <div class="modal-card-content" id="modalFrontContent"></div>
                        <button class="modal-flip-btn" onclick="flipModalCard()">
                            <i class="fas fa-sync-alt"></i> Flip to see answer
                        </button>
                    </div>
                    <div class="modal-card-back">
                        <div class="flashcard-number" id="modalNumberBack"></div>
                        <div class="flashcard-category" id="modalCategoryBack"></div>
                        <div class="modal-card-content" id="modalBackContent"></div>
                        <button class="modal-flip-btn" onclick="flipModalCard()">
                            <i class="fas fa-sync-alt"></i> Flip to see question
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-navigation">
                <button class="nav-btn" id="prevBtn" onclick="navigateCard(-1)">
                    <i class="fas fa-chevron-left"></i> Previous
                </button>
                <span id="cardCounter" style="align-self: center; font-weight: 600;"></span>
                <button class="nav-btn" id="nextBtn" onclick="navigateCard(1)">
                    Next <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedSubjectId = null;
let selectedResources = [];
let flashcardsData = [];
let currentCardIndex = 0;

// Convert markdown to HTML
function markdownToHtml(text) {
    if (!text) return '';
    
    // Convert to string if not already
    text = String(text);
    
    // Bold: **text** or __text__
    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    text = text.replace(/__(.+?)__/g, '<strong>$1</strong>');
    
    // Italic: *text* or _text_
    text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
    text = text.replace(/_(.+?)_/g, '<em>$1</em>');
    
    // Code: `code`
    text = text.replace(/`(.+?)`/g, '<code>$1</code>');
    
    // Line breaks
    text = text.replace(/\n/g, '<br>');
    
    // Bullet points: - item or * item
    text = text.replace(/^[\-\*]\s+(.+)$/gm, '<li>$1</li>');
    text = text.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
    
    return text;
}

// Load resources for selected subject
function loadSubjectResources() {
    const select = document.getElementById('subjectSelect');
    selectedSubjectId = select.value;
    
    if (!selectedSubjectId) {
        document.getElementById('resourcesSection').style.display = 'none';
        return;
    }

    fetch(`<?php echo base_url('simple_portal/get_subject_resources'); ?>?subject_id=${selectedSubjectId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.resources.length > 0) {
                displayResources(data.resources);
                document.getElementById('resourcesSection').style.display = 'block';
            } else {
                showAlert('No resources found for this subject.', 'error');
                document.getElementById('resourcesSection').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to load resources.', 'error');
        });
}

// Display resources as checkboxes
function displayResources(resources) {
    const container = document.getElementById('resourcesList');
    container.innerHTML = '';
    
    resources.forEach(resource => {
        const div = document.createElement('div');
        div.className = 'resource-checkbox';
        div.innerHTML = `
            <input type="checkbox" 
                   id="resource_${resource.id}" 
                   value="${resource.id}"
                   onchange="updateSelectedResources()">
            <div class="resource-info">
                <div class="resource-title">
                    <i class="fas fa-file-${resource.file_type === 'pdf' ? 'pdf' : 'alt'}"></i>
                    ${resource.title}
                </div>
                <div class="resource-meta">
                    ${resource.file_type.toUpperCase()} • ${resource.subject_code}
                </div>
            </div>
        `;
        container.appendChild(div);
    });
}

// Update selected resources array
function updateSelectedResources() {
    selectedResources = [];
    document.querySelectorAll('#resourcesList input[type="checkbox"]:checked').forEach(checkbox => {
        selectedResources.push(checkbox.value);
    });
    
    document.getElementById('generateBtn').disabled = selectedResources.length === 0;
}

// Generate flashcards
function generateFlashcards() {
    if (selectedResources.length === 0) {
        showAlert('Please select at least one resource.', 'error');
        return;
    }

    const numCards = document.getElementById('numCards').value;

    document.getElementById('loadingSpinner').classList.add('active');
    document.getElementById('flashcardsDisplay').classList.remove('active');
    document.getElementById('generateBtn').disabled = true;

    fetch('<?php echo base_url('simple_portal/process_flashcard_generation'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `resource_ids=${JSON.stringify(selectedResources)}&subject_id=${selectedSubjectId}&num_cards=${numCards}`
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loadingSpinner').classList.remove('active');
        document.getElementById('generateBtn').disabled = false;
        
        if (data.success) {
            showAlert('Flashcards generated successfully!', 'success');
            flashcardsData = data.flashcard_data.flashcards;
            renderFlashcards(flashcardsData);
            document.getElementById('flashcardsDisplay').classList.add('active');
            
            document.getElementById('flashcardsDisplay').scrollIntoView({ behavior: 'smooth' });
        } else {
            showAlert('Error: ' + (data.error || 'Failed to generate flashcards'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loadingSpinner').classList.remove('active');
        document.getElementById('generateBtn').disabled = false;
        showAlert('Failed to generate flashcards. Please try again.', 'error');
    });
}

// Render flashcards
function renderFlashcards(flashcards) {
    const container = document.getElementById('flashcardsGrid');
    container.innerHTML = '';
    
    flashcards.forEach((card, index) => {
        const cardDiv = document.createElement('div');
        cardDiv.className = 'flashcard';
        cardDiv.onclick = function() { openModal(index); };
        
        const difficulty = card.difficulty || 'medium';
        
        // Convert markdown to HTML
        const frontHtml = markdownToHtml(card.front);
        const backHtml = markdownToHtml(card.back);
        
        cardDiv.innerHTML = `
            <div class="flashcard-inner">
                <div class="flashcard-front ${difficulty}">
                    <div class="flashcard-number">#${index + 1}</div>
                    <div class="flashcard-category">${card.category || 'General'}</div>
                    <div class="flashcard-content">${frontHtml}</div>
                    <div class="flashcard-hint"><i class="fas fa-expand"></i> Click to expand</div>
                </div>
                <div class="flashcard-back">
                    <div class="flashcard-number">#${index + 1}</div>
                    <div class="flashcard-category">${card.category || 'General'}</div>
                    <div class="flashcard-content">${backHtml}</div>
                    <div class="flashcard-hint"><i class="fas fa-expand"></i> Click to expand</div>
                </div>
            </div>
        `;
        
        container.appendChild(cardDiv);
    });
}

// Open modal with card
function openModal(index) {
    currentCardIndex = index;
    const card = flashcardsData[index];
    const difficulty = card.difficulty || 'medium';
    
    // Convert markdown to HTML
    const frontHtml = markdownToHtml(card.front);
    const backHtml = markdownToHtml(card.back);
    
    // Update modal content
    document.getElementById('modalNumber').textContent = `#${index + 1}`;
    document.getElementById('modalNumberBack').textContent = `#${index + 1}`;
    document.getElementById('modalCategory').textContent = card.category || 'General';
    document.getElementById('modalCategoryBack').textContent = card.category || 'General';
    document.getElementById('modalFrontContent').innerHTML = frontHtml;
    document.getElementById('modalBackContent').innerHTML = backHtml;
    
    // Set difficulty color
    document.getElementById('modalFront').className = `modal-card-front ${difficulty}`;
    
    // Reset flip state
    document.getElementById('modalCard').classList.remove('flipped');
    
    // Update counter
    document.getElementById('cardCounter').textContent = `${index + 1} / ${flashcardsData.length}`;
    
    // Update navigation buttons
    document.getElementById('prevBtn').disabled = index === 0;
    document.getElementById('nextBtn').disabled = index === flashcardsData.length - 1;
    
    // Show modal
    document.getElementById('flashcardModal').classList.add('active');
}

// Close modal
function closeModal() {
    document.getElementById('flashcardModal').classList.remove('active');
}

// Flip modal card
function flipModalCard() {
    document.getElementById('modalCard').classList.toggle('flipped');
}

// Navigate between cards
function navigateCard(direction) {
    const newIndex = currentCardIndex + direction;
    if (newIndex >= 0 && newIndex < flashcardsData.length) {
        openModal(newIndex);
    }
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('flashcardModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('flashcardModal');
    if (modal.classList.contains('active')) {
        if (event.key === 'Escape') {
            closeModal();
        } else if (event.key === 'ArrowLeft') {
            navigateCard(-1);
        } else if (event.key === 'ArrowRight') {
            navigateCard(1);
        } else if (event.key === ' ') {
            event.preventDefault();
            flipModalCard();
        }
    }
});

// Flip all cards
function flipAllCards() {
    const cards = document.querySelectorAll('.flashcard');
    cards.forEach(card => card.classList.toggle('flipped'));
}

// Shuffle cards
function shuffleCards() {
    const shuffled = [...flashcardsData].sort(() => Math.random() - 0.5);
    renderFlashcards(shuffled);
}

// Show alert message
function showAlert(message, type) {
    const container = document.getElementById('alertContainer');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        ${message}
    `;
    container.innerHTML = '';
    container.appendChild(alert);
    
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}
</script>

</body>
</html>
