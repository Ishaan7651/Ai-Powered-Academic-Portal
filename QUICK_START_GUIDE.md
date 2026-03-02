# College Academic Portal - Quick Start Guide

## Overview
A comprehensive academic portal built with CodeIgniter 3, featuring AI-powered tools for students, faculty, and administrators.

## Tech Stack
- **Backend**: PHP 7.4+ with CodeIgniter 3
- **Database**: MySQL 5.7+
- **AI Integration**: Google Gemini 2.5 Flash API
- **Frontend**: Bootstrap 5, D3.js (for mindmaps)

## Quick Setup

### 1. Database Configuration
Edit `application/config/database.php`:
```php
'hostname' => 'localhost',
'username' => 'root',
'password' => 'Tinkerbell8877',
'database' => 'college_academic_portal',
```

### 2. Import Database
```bash
mysql -u root -p college_academic_portal < database_schema.sql
```

### 3. Install Dependencies
```bash
composer install
```

### 4. Set Permissions
```bash
chmod -R 777 uploads/
chmod -R 777 application/logs/
```

### 5. Access Portal
- URL: `http://localhost/your-project-folder/`
- Default Admin: `admin` / `admin123`
- Default Student: `student1` / `password123`
- Default Faculty: `faculty1` / `password123`

## Key Features

### For Students
1. **AI Buddy Chat** - Chat with AI about course materials
2. **Mindmap Generation** - Visual learning with interactive mindmaps
3. **Flashcard Generation** - Study cards with flip and expand functionality
4. **Quiz Taking** - Practice with AI-generated quizzes
5. **Resource Access** - View and download course materials
6. **Assignment Submission** - Submit and track assignments

### For Faculty
1. **Resource Management** - Upload PDFs, DOCX, PPTX files
2. **Quiz Generation** - AI-powered quiz creation
3. **Assignment Creation** - Generate assignments with AI
4. **Question Paper Generation** - Create exam papers
5. **Student Management** - View enrolled students
6. **Grade Management** - Track student performance

### For Administrators
1. **User Management** - Create/manage students and faculty
2. **Bulk Upload** - CSV import for users and subjects
3. **Subject Management** - Add/edit/delete subjects
4. **System Monitoring** - View logs and system status

## Recent Updates

### Flashcard Feature (Latest)
- **Location**: Student Dashboard → FLASHCARDS
- **Features**:
  - Select multiple resources to generate flashcards
  - Colorful cards with questions and answers
  - Click any card to expand in modal view
  - Flip cards to see answers
  - Navigate with Previous/Next buttons
  - Keyboard shortcuts: Arrow keys (navigate), Space (flip), Escape (close)
  - Scrollable content in both grid and modal views
  - Flip All and Shuffle buttons for study flexibility

### Mindmap Feature
- **Location**: Student Dashboard → MINDMAP
- **Features**:
  - Horizontal tree layout for better readability
  - Zoom and pan controls
  - Interactive nodes with hover effects
  - Click to focus on specific branches
  - Generated from course resources using AI

## File Structure
```
project/
├── application/
│   ├── controllers/
│   │   ├── Simple_portal.php (Main controller)
│   │   ├── AI_buddy.php (Chat functionality)
│   │   └── ...
│   ├── models/
│   │   ├── Resource_model.php
│   │   ├── Subject_model.php
│   │   └── ...
│   ├── views/
│   │   └── simple_portal/
│   │       ├── generate_flashcards.php
│   │       ├── generate_mindmap.php
│   │       └── ...
│   └── libraries/
│       └── AI_service.php (Gemini API integration)
├── uploads/ (Resource files)
├── assets/ (CSS, JS)
└── database_schema.sql
```

## API Configuration

### Gemini API
The system uses Google Gemini 2.5 Flash for AI features:
- API Key is configured in `application/libraries/AI_service.php`
- Includes retry logic with exponential backoff for rate limits
- Supports: Chat, Quiz Generation, Mindmap, Flashcards, Assignments

## Common Issues & Solutions

### 1. PDF Extraction Not Working
**Solution**: Install Smalot PDF Parser
```bash
composer require smalot/pdfparser
```

### 2. AI Rate Limit (429 Error)
**Solution**: The system automatically retries with delays (2s, 4s, 8s). Wait a moment and try again.

### 3. Upload Errors
**Solution**: Check folder permissions
```bash
chmod -R 777 uploads/
```

### 4. Session Issues
**Solution**: Clear browser cookies and cache, or use incognito mode

### 5. Database Connection Failed
**Solution**: Verify credentials in `application/config/database.php`

## Development Guidelines

### Adding New Features
1. Create controller method in `Simple_portal.php`
2. Create view in `application/views/simple_portal/`
3. Add navigation link in appropriate sidebar
4. Test with different user roles

### AI Integration
Use the `AI_service` library for AI features:
```php
$this->load->library('AI_service');
$response = $this->ai_service->generate_flashcards($content, $subject, 15);
```

### Database Queries
Use CodeIgniter's Query Builder:
```php
$this->db->where('user_id', $user_id);
$query = $this->db->get('students');
```

## Security Notes
- All passwords are hashed using `password_hash()`
- Session-based authentication
- Role-based access control (admin, faculty, student)
- File upload validation and sanitization
- SQL injection protection via Query Builder

## Support & Documentation
- Full documentation: `PROJECT_DOCUMENTATION.md`
- Installation guide: `INSTALLATION.md`
- Database schema: `database_schema.sql`

## Future Enhancements
- Email notifications
- Real-time chat
- Mobile app
- Advanced analytics
- Plagiarism detection
- Video conferencing integration

---

**Last Updated**: January 29, 2026
**Version**: 2.0
