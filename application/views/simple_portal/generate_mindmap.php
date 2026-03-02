<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Mindmap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <?php $this->load->view('simple_portal/components/student_sidebar_css'); ?>
    
    <style>
        body {
            background: var(--light-bg) !important;
        }

        .mindmap-container {
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

        .generate-btn {
            padding: 14px 30px;
            background: linear-gradient(135deg, var(--success-green), #8BAD5A);
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
            box-shadow: 0 4px 12px rgba(117, 155, 73, 0.3);
        }

        .generate-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .mindmap-display {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: none;
        }

        .mindmap-display.active {
            display: block;
        }

        #mindmapCanvas {
            width: 100%;
            min-height: 800px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: #fafafa;
            overflow: visible;
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

        .download-buttons {
            display: flex;
            gap: 10px;
        }

        .download-buttons .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .download-buttons .btn-primary {
            background: linear-gradient(135deg, #4A76A8, #3a5f8a);
            color: white;
        }

        .download-buttons .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 118, 168, 0.3);
        }

        .download-buttons .btn-success {
            background: linear-gradient(135deg, #759B49, #5f7d3a);
            color: white;
        }

        .download-buttons .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(117, 155, 73, 0.3);
        }
    </style>
</head>
<body>

<?php $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'mindmap']); ?>

<div class="mindmap-container">
    <div class="page-header">
        <h1><i class="fas fa-project-diagram"></i> Generate Mindmap</h1>
        <p>Select resources to create a comprehensive mindmap visualization</p>
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
            
            <button class="generate-btn" id="generateBtn" onclick="generateMindmap()" disabled>
                <i class="fas fa-magic"></i>
                Generate Mindmap
            </button>
        </div>
    </div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
        <p>Generating your mindmap... This may take a moment.</p>
    </div>

    <div class="mindmap-display" id="mindmapDisplay">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2><i class="fas fa-sitemap"></i> Your Mindmap</h2>
            <div class="download-buttons">
                <button class="btn btn-primary" onclick="downloadMindmapAsPNG()" style="margin-right: 10px;">
                    <i class="fas fa-download"></i> Download PNG
                </button>
                <button class="btn btn-success" onclick="downloadMindmapAsPDF()">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </button>
            </div>
        </div>
        <div id="mindmapCanvas"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
let selectedSubjectId = null;
let selectedResources = [];
let subjectResources = {};

// Load resources for selected subject
function loadSubjectResources() {
    const select = document.getElementById('subjectSelect');
    selectedSubjectId = select.value;
    
    if (!selectedSubjectId) {
        document.getElementById('resourcesSection').style.display = 'none';
        return;
    }

    // Fetch resources for this subject
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

// Generate mindmap
function generateMindmap() {
    if (selectedResources.length === 0) {
        showAlert('Please select at least one resource.', 'error');
        return;
    }

    // Show loading
    document.getElementById('loadingSpinner').classList.add('active');
    document.getElementById('mindmapDisplay').classList.remove('active');
    document.getElementById('generateBtn').disabled = true;

    // Send request
    fetch('<?php echo base_url('simple_portal/process_mindmap_generation'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `resource_ids=${JSON.stringify(selectedResources)}&subject_id=${selectedSubjectId}`
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loadingSpinner').classList.remove('active');
        document.getElementById('generateBtn').disabled = false;
        
        console.log('Mindmap response:', data);
        
        if (data.success) {
            console.log('Mindmap data:', data.mindmap_data);
            showAlert('Mindmap generated successfully!', 'success');
            renderMindmap(data.mindmap_data);
            document.getElementById('mindmapDisplay').classList.add('active');
            
            // Scroll to mindmap
            document.getElementById('mindmapDisplay').scrollIntoView({ behavior: 'smooth' });
        } else {
            showAlert('Error: ' + (data.error || 'Failed to generate mindmap'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loadingSpinner').classList.remove('active');
        document.getElementById('generateBtn').disabled = false;
        showAlert('Failed to generate mindmap. Please try again.', 'error');
    });
}

// Render mindmap using D3.js
function renderMindmap(data) {
    console.log('renderMindmap called with data:', data);
    
    const container = document.getElementById('mindmapCanvas');
    container.innerHTML = '';
    
    // Validate data structure
    if (!data || !data.central_topic || !data.branches || !Array.isArray(data.branches)) {
        console.error('Invalid mindmap data structure:', data);
        showAlert('Invalid mindmap data received. Please try again.', 'error');
        return;
    }
    
    if (data.branches.length === 0) {
        console.error('No branches in mindmap data');
        showAlert('Mindmap has no content. Please try with different resources.', 'error');
        return;
    }
    
    console.log('Central topic:', data.central_topic);
    console.log('Number of branches:', data.branches.length);
    
    // Get container width - use parent width if container width is 0
    let width = container.clientWidth;
    if (width === 0) {
        width = container.parentElement.clientWidth - 60; // Account for padding
    }
    if (width < 800) {
        width = 1000; // Increased minimum width for better spacing
    }
    const height = 800; // Increased height for better spacing
    
    console.log('Canvas dimensions:', width, 'x', height);
    
    const svg = d3.select('#mindmapCanvas')
        .append('svg')
        .attr('width', width)
        .attr('height', height);
    
    const g = svg.append('g')
        .attr('transform', `translate(${width/2},${height/2})`);
    
    // Create hierarchical data structure
    const root = {
        name: data.central_topic,
        children: data.branches.map(branch => ({
            name: branch.title,
            description: branch.description,
            children: branch.sub_branches ? branch.sub_branches.map(sub => ({
                name: sub.title,
                description: sub.description
            })) : []
        }))
    };
    
    console.log('Root structure created:', root);
    
    // Use a collapsible indented tree layout for better readability
    const nodeWidth = 200;
    const nodeHeight = 40;
    
    const tree = d3.tree()
        .nodeSize([nodeHeight, nodeWidth])
        .separation((a, b) => {
            return a.parent == b.parent ? 1 : 1.2;
        });
    
    const hierarchy = d3.hierarchy(root);
    const treeData = tree(hierarchy);
    
    console.log('Tree data created, descendants:', treeData.descendants().length);
    
    // Position tree starting from left
    const g2 = g.append('g')
        .attr('transform', `translate(${-width/2 + 100}, ${-height/2 + 50})`);
    
    // Draw links (horizontal connections)
    const links = g2.selectAll('.link')
        .data(treeData.links())
        .enter().append('path')
        .attr('class', 'link')
        .attr('d', d => {
            return `M${d.source.y},${d.source.x}
                    C${(d.source.y + d.target.y) / 2},${d.source.x}
                     ${(d.source.y + d.target.y) / 2},${d.target.x}
                     ${d.target.y},${d.target.x}`;
        })
        .style('fill', 'none')
        .style('stroke', '#4A76A8')
        .style('stroke-width', 2);
    
    console.log('Links drawn:', links.size());
    
    // Draw nodes
    const node = g2.selectAll('.node')
        .data(treeData.descendants())
        .enter().append('g')
        .attr('class', 'node')
        .attr('transform', d => `translate(${d.y},${d.x})`)
        .style('cursor', 'pointer');
    
    node.append('circle')
        .attr('r', d => d.depth === 0 ? 12 : (d.depth === 1 ? 9 : 6))
        .style('fill', d => d.depth === 0 ? '#1D4486' : (d.depth === 1 ? '#4A76A8' : '#759B49'))
        .style('stroke', '#fff')
        .style('stroke-width', 2)
        .on('mouseover', function(event, d) {
            d3.select(this)
                .transition()
                .duration(200)
                .attr('r', d.depth === 0 ? 15 : (d.depth === 1 ? 12 : 9))
                .style('stroke-width', 3);
        })
        .on('mouseout', function(event, d) {
            d3.select(this)
                .transition()
                .duration(200)
                .attr('r', d.depth === 0 ? 12 : (d.depth === 1 ? 9 : 6))
                .style('stroke-width', 2);
        });
    
    // Add text labels with better positioning
    node.append('text')
        .attr('x', d => d.children ? -15 : 15)
        .attr('dy', '0.35em')
        .attr('text-anchor', d => d.children ? 'end' : 'start')
        .text(d => {
            // Show full text, let it wrap naturally
            const maxLength = 40;
            return d.data.name.length > maxLength ? 
                d.data.name.substring(0, maxLength) + '...' : 
                d.data.name;
        })
        .style('font-size', d => d.depth === 0 ? '15px' : (d.depth === 1 ? '13px' : '11px'))
        .style('font-weight', d => d.depth === 0 ? 'bold' : (d.depth === 1 ? '600' : 'normal'))
        .style('fill', '#333')
        .style('pointer-events', 'none');
    
    // Add tooltip
    node.append('title')
        .text(d => {
            let text = d.data.name;
            if (d.data.description) {
                text += '\n\n' + d.data.description;
            }
            return text;
        });
    
    // Add click to focus with corrected positioning
    node.on('click', function(event, d) {
        event.stopPropagation();
        
        // Get current transform
        const currentTransform = d3.zoomTransform(svg.node());
        
        // Calculate new position to center the clicked node
        const scale = 1.5;
        const x = width / 2 - (d.y * scale);
        const y = height / 2 - (d.x * scale);
        
        svg.transition()
            .duration(750)
            .call(
                zoom.transform,
                d3.zoomIdentity.translate(x, y).scale(scale)
            );
    });
    
    // Add zoom and pan functionality
    const zoom = d3.zoom()
        .scaleExtent([0.3, 3])
        .on('zoom', (event) => {
            g.attr('transform', event.transform);
        });
    
    svg.call(zoom);
    
    // Add control buttons
    const controls = svg.append('g')
        .attr('class', 'controls')
        .attr('transform', 'translate(10, 10)');
    
    // Zoom in button
    const zoomInBtn = controls.append('g')
        .attr('cursor', 'pointer')
        .on('click', () => {
            svg.transition().duration(300).call(zoom.scaleBy, 1.3);
        });
    
    zoomInBtn.append('rect')
        .attr('width', 30)
        .attr('height', 30)
        .attr('rx', 5)
        .style('fill', '#4A76A8')
        .style('stroke', '#fff')
        .style('stroke-width', 2);
    
    zoomInBtn.append('text')
        .attr('x', 15)
        .attr('y', 20)
        .attr('text-anchor', 'middle')
        .style('fill', 'white')
        .style('font-size', '20px')
        .style('font-weight', 'bold')
        .style('pointer-events', 'none')
        .text('+');
    
    // Zoom out button
    const zoomOutBtn = controls.append('g')
        .attr('transform', 'translate(0, 35)')
        .attr('cursor', 'pointer')
        .on('click', () => {
            svg.transition().duration(300).call(zoom.scaleBy, 0.7);
        });
    
    zoomOutBtn.append('rect')
        .attr('width', 30)
        .attr('height', 30)
        .attr('rx', 5)
        .style('fill', '#4A76A8')
        .style('stroke', '#fff')
        .style('stroke-width', 2);
    
    zoomOutBtn.append('text')
        .attr('x', 15)
        .attr('y', 20)
        .attr('text-anchor', 'middle')
        .style('fill', 'white')
        .style('font-size', '20px')
        .style('font-weight', 'bold')
        .style('pointer-events', 'none')
        .text('−');
    
    // Reset button
    const resetBtn = controls.append('g')
        .attr('transform', 'translate(0, 70)')
        .attr('cursor', 'pointer')
        .on('click', () => {
            svg.transition().duration(500).call(zoom.transform, d3.zoomIdentity);
        });
    
    resetBtn.append('rect')
        .attr('width', 30)
        .attr('height', 30)
        .attr('rx', 5)
        .style('fill', '#759B49')
        .style('stroke', '#fff')
        .style('stroke-width', 2);
    
    resetBtn.append('text')
        .attr('x', 15)
        .attr('y', 20)
        .attr('text-anchor', 'middle')
        .style('fill', 'white')
        .style('font-size', '16px')
        .style('font-weight', 'bold')
        .style('pointer-events', 'none')
        .text('⟲');
    
    // Add instructions
    const instructions = svg.append('text')
        .attr('x', width - 10)
        .attr('y', 20)
        .attr('text-anchor', 'end')
        .style('font-size', '12px')
        .style('fill', '#666')
        .text('💡 Click nodes to focus • Scroll to zoom • Drag to pan');
    
    console.log('Nodes drawn:', node.size());
    console.log('Mindmap rendering complete with interactive features');
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

// Download mindmap as PNG
function downloadMindmapAsPNG() {
    const canvas = document.getElementById('mindmapCanvas');
    const svg = canvas.querySelector('svg');
    
    if (!svg) {
        showAlert('No mindmap to download. Please generate a mindmap first.', 'error');
        return;
    }
    
    // Show loading message
    showAlert('Preparing download...', 'success');
    
    // Get SVG dimensions
    const bbox = svg.getBBox();
    const width = Math.max(bbox.width + 100, 1200);
    const height = Math.max(bbox.height + 100, 800);
    
    // Create a canvas element
    const downloadCanvas = document.createElement('canvas');
    downloadCanvas.width = width;
    downloadCanvas.height = height;
    const ctx = downloadCanvas.getContext('2d');
    
    // Fill white background
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, width, height);
    
    // Convert SVG to data URL
    const svgData = new XMLSerializer().serializeToString(svg);
    const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
    const url = URL.createObjectURL(svgBlob);
    
    // Create image from SVG
    const img = new Image();
    img.onload = function() {
        ctx.drawImage(img, 50, 50);
        
        // Convert canvas to blob and download
        downloadCanvas.toBlob(function(blob) {
            const link = document.createElement('a');
            link.download = `mindmap_${Date.now()}.png`;
            link.href = URL.createObjectURL(blob);
            link.click();
            
            URL.revokeObjectURL(url);
            URL.revokeObjectURL(link.href);
            
            showAlert('Mindmap downloaded successfully!', 'success');
        });
    };
    
    img.onerror = function() {
        // Fallback: use html2canvas
        html2canvas(canvas, {
            backgroundColor: '#ffffff',
            scale: 2,
            logging: false
        }).then(canvas => {
            canvas.toBlob(function(blob) {
                const link = document.createElement('a');
                link.download = `mindmap_${Date.now()}.png`;
                link.href = URL.createObjectURL(blob);
                link.click();
                URL.revokeObjectURL(link.href);
                showAlert('Mindmap downloaded successfully!', 'success');
            });
        }).catch(error => {
            console.error('Download error:', error);
            showAlert('Failed to download mindmap. Please try again.', 'error');
        });
    };
    
    img.src = url;
}

// Download mindmap as PDF
function downloadMindmapAsPDF() {
    const canvas = document.getElementById('mindmapCanvas');
    const svg = canvas.querySelector('svg');
    
    if (!svg) {
        showAlert('No mindmap to download. Please generate a mindmap first.', 'error');
        return;
    }
    
    // Show loading message
    showAlert('Preparing PDF download...', 'success');
    
    // Use html2canvas to capture the mindmap
    html2canvas(canvas, {
        backgroundColor: '#ffffff',
        scale: 2,
        logging: false
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        
        // Calculate PDF dimensions
        const imgWidth = 297; // A4 width in mm (landscape)
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: imgHeight > imgWidth ? 'portrait' : 'landscape',
            unit: 'mm',
            format: 'a4'
        });
        
        // Add title
        pdf.setFontSize(16);
        pdf.setTextColor(29, 68, 134);
        pdf.text('Mindmap - Generated by Academic Portal', 10, 10);
        
        // Add date
        pdf.setFontSize(10);
        pdf.setTextColor(100, 100, 100);
        pdf.text(`Generated on: ${new Date().toLocaleDateString()}`, 10, 17);
        
        // Add image
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();
        const margin = 20;
        
        const availableWidth = pdfWidth - (2 * margin);
        const availableHeight = pdfHeight - 30 - margin;
        
        let finalWidth = availableWidth;
        let finalHeight = (canvas.height * availableWidth) / canvas.width;
        
        if (finalHeight > availableHeight) {
            finalHeight = availableHeight;
            finalWidth = (canvas.width * availableHeight) / canvas.height;
        }
        
        const xOffset = (pdfWidth - finalWidth) / 2;
        const yOffset = 25;
        
        pdf.addImage(imgData, 'PNG', xOffset, yOffset, finalWidth, finalHeight);
        
        // Save PDF
        pdf.save(`mindmap_${Date.now()}.pdf`);
        
        showAlert('PDF downloaded successfully!', 'success');
    }).catch(error => {
        console.error('PDF generation error:', error);
        showAlert('Failed to generate PDF. Please try again.', 'error');
    });
}
</script>

</body>
</html>
