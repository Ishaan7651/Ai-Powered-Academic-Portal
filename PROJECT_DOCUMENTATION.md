# SLAi - Smart Learning AI Portal

## Project Overview

SLAi is a comprehensive educational portal built with CodeIgniter 3 that integrates AI capabilities to enhance learning and teaching experiences. The system supports three user roles: Admin, Faculty, and Students, each with specialized features.

## Technology Stack

- **Framework**: CodeIgniter 3.1.13
- **Language**: PHP 7.4+
- **Database**: MySQL
- **AI Service**: Google Gemini 2.5 Flash API
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5, D3.js
- **PDF Processing**: Smalot PDF Parser (via Composer)

## Database Configuration

**Location**: `application/config/database.php`

```php
'hostname' => 'localhost',
'username' => 'root',
'password' => 'Tinkerbell8877',
'database' => 'college_academic_portal'
```

## System Architecture

### MVC Structure

```
application/
├── controllers/
│   ├── Simple_portal.php      # Main portal controller (all roles)
│   ├── AI_buddy.php            # AI chat controller
│   ├── Auth_simple.php         # Authentication
│   ├── Admin_working.php       # Admin functions
│   ├── Faculty_working.php     # Faculty functions
│   └── Student_working.php     # Student functions
├── models/
│   ├── AI_buddy_model.php      # AI chat data
│   ├── Resource_model.php      # Learning resources
│   ├── Subject_model.php       # Subjects/courses
│   ├── Faculty_model.php       # Faculty data
│   └── Simple_user_model.php   # User management
├── views/
│   ├── simple_portal/          # Main portal views
│   ├── ai_buddy/               # AI chat views
│   └── templates/              # Shared templates
└── libraries/
    ├── AI_service.php          # Gemini AI integration
    └── Simple_pdf_parser.php   # PDF text extraction
```

## User Roles & Features

### 1. Admin

**Access**: Full system control

**Features**:
- User management (create/bulk upload faculty and students)
- Subject management (CRUD operations, bulk upload)
- Faculty assignment to subjects
- System monitoring and statistics
- CSV template downloads for bulk operations

**Key Routes**:
- `/simple_portal` - Admin dashboard
- `/simple_portal/create_faculty` - Create faculty
- `/simple_portal/create_student` - Create student
- `/simple_portal/manage_subjects` - Manage subjects
- `/simple_portal/manage_faculty` - Faculty assignments

### 2. Faculty

**Access**: Teaching and content management

**Features**:
- **Resource Management**: Upload learning materials (PDF, DOCX, PPTX, etc.)
- **AI Generators**:
  - Question Paper Generator
  - Quiz Generator
  - Assignment Generator
  - Mindmap Generator (for content analysis)
- **Publishing System**: Publish content to students
- **Student Management**: View enrolled students
- **AI Chat**: Chat with uploaded resources

**Key Routes**:
- `/simple_portal` - Faculty dashboard
- `/simple_portal/resources` - Resource management
- `/simple_portal/upload_resource` - Upload materials
- `/simple_portal/generate_question_paper` - AI question papers
- `/simple_portal/generate_quiz` - AI quizzes
- `/simple_portal/generate_assignment` - AI assignments
- `/simple_portal/ai_chat` - AI chat interface

### 3. Students

**Access**: Learning and assessment

**Features**:
- **Resources**: Access learning materials by semester/subject
- **AI Buddy**: Chat with subject-specific resources
  - Select semester → Select subject → Chat with resources
  - Context-aware responses based on uploaded materials
- **Mindmap Generator**: Create visual mindmaps from multiple resources
- **Published Content**:
  - View question papers
  - Take quizzes
  - View assignments
- **Auto-logout**: Session ends when browser closes

**Key Routes**:
- `/simple_portal` - Student dashboard
- `/simple_portal/student_resources` - Browse resources
- `/simple_portal/ai_chat` - AI Buddy (semester/subject selection)
- `/simple_portal/generate_mindmap` - Mindmap generator
- `/simple_portal/student_question_papers` - View question papers
- `/simple_portal/student_quizzes` - Take quizzes
- `/simple_portal/student_assignments` - View assignments

## Core Features

### 1. AI Integration (Gemini 2.5 Flash)

**AI Service**: `application/libraries/AI_service.php`

**Capabilities**:
- **Chat**: Context-aware conversations with document content
- **Quiz Generation**: Generate multiple-choice questions from documents
- **Question Paper Generation**: Create structured exam papers
- **Assignment Generation**: Generate assignments with rubrics
- **Mindmap Generation**: Create hierarchical mindmaps from content
- **PDF Text Extraction**: Extract text from PDF files

**API Configuration**:
```php
$this->api_key = 'AIzaSyCI6e7CWUQaBRwd9FDtKdmezAWu02E5Dss';
$this->api_endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
```

### 2. AI Buddy (Student Chat)

**Flow**:
1. Student clicks "AI BUDDY" in sidebar
2. Selects semester (shows 8 semesters, locked/unlocked based on enrollment)
3. Selects subject from enrolled subjects
4. Views resources for that subject
5. Selects a resource to chat about
6. AI provides context-aware responses based on resource content

**Features**:
- Session-based chat history
- Resource-specific context
- Markdown formatting in responses (bold, italic, code, lists)
- Switch between resources without losing chat history
- PDF, DOCX, PPTX content extraction

**Implementation**:
- Controller: `Simple_portal::ai_chat()`, `select_semester_for_chat()`, `select_subject_for_chat()`, `start_subject_chat()`
- AJAX: `send_ai_chat_message()`
- Views: `ai_buddy/select_semester_chat.php`, `select_subject_chat.php`, `chat_with_subject.php`

### 3. Mindmap Generator

**Purpose**: Create visual mindmaps from multiple learning resources

**Flow**:
1. Student clicks "MINDMAP" in sidebar
2. Selects a subject
3. Checks multiple resources to include
4. Clicks "Generate Mindmap"
5. AI analyzes content and creates hierarchical structure
6. D3.js renders interactive tree visualization

**Structure**:
- Central topic (subject name)
- Main branches (4-6 major concepts)
- Sub-branches (2-4 key points per concept)

**Implementation**:
- Controller: `Simple_portal::generate_mindmap()`, `process_mindmap_generation()`
- AI Method: `AI_service::generate_mindmap()`
- View: `simple_portal/generate_mindmap.php`
- Visualization: D3.js v7 horizontal tree layout

### 4. Resource Management

**Supported Formats**: PDF, DOCX, PPTX, XLS, XLSX, CSV, EPUB, TXT

**Features**:
- File upload with validation (10MB limit)
- Web link resources
- Subject and semester categorization
- Faculty ownership
- Student access based on enrollment

**Database**: `resources` table
- Columns: id, title, description, file_type, file_path, subject_id, semester, uploaded_by, created_at

**Access Control**:
- Faculty: Upload and manage own resources
- Students: View resources for enrolled subjects only (LEFT JOIN with faculty table to handle NULL faculty_id)

### 5. Session Management

**Auto-Logout Feature**:
- Session expires when browser closes (`sess_expiration = 0`)
- JavaScript `beforeunload` event triggers silent logout
- Implemented in both `Auth_working.php` and `Auth_simple.php`
- Added to header templates for all pages

**Configuration**: `application/config/config.php`
```php
$config['sess_expiration'] = 0; // Expires on browser close
```

## Database Schema

### Core Tables

**users**
- id, username, email, password_hash, role (admin/faculty/student), is_active, created_at

**students**
- id, user_id, student_id, current_semester, enrollment_year, created_at

**faculty**
- id, user_id, employee_id, department, created_at

**subjects**
- id, subject_code, subject_name, semester, credits, is_active, created_at

**student_enrollments**
- id, student_id, subject_id, enrollment_date

**resources**
- id, title, description, file_type, file_path, file_size, original_filename, subject_id, semester, uploaded_by, is_active, created_at

**ai_chat_sessions**
- id, user_id, resource_id, session_name, created_at, updated_at

**ai_chat_messages**
- id, session_id, role (user/assistant), message, created_at

**ai_question_papers**
- id, user_id, subject_id, title, content, config, is_published, created_at

**ai_quizzes**
- id, user_id, subject_id, title, questions, is_published, created_at

**ai_assignments**
- id, user_id, subject_id, title, content, is_published, created_at

## File Structure

### Uploads Directory

```
uploads/
├── resources/          # Learning materials
├── assignments/        # Generated assignments
├── question_papers/    # Generated question papers
└── temp/              # Temporary files
```

### Views Structure

```
application/views/
├── simple_portal/
│   ├── components/
│   │   ├── student_sidebar.php      # Student navigation
│   │   ├── student_sidebar_css.php  # Sidebar styles
│   │   ├── faculty_sidebar.php      # Faculty navigation
│   │   └── admin_sidebar.php        # Admin navigation
│   ├── admin_dashboard.php
│   ├── faculty_dashboard.php
│   ├── student_dashboard.php
│   ├── generate_mindmap.php         # Mindmap generator
│   ├── student_resources.php        # Resource browser
│   ├── student_quizzes.php
│   ├── student_assignments.php
│   └── student_question_papers.php
├── ai_buddy/
│   ├── select_semester_chat.php     # Step 1: Semester selection
│   ├── select_subject_chat.php      # Step 2: Subject selection
│   └── chat_with_subject.php        # Step 3: Chat interface
└── templates/
    ├── simple_header.php            # Header with auto-logout
    └── simple_footer.php
```

## Key Implementation Details

### 1. Resource Visibility Fix

**Issue**: Resources with `faculty_id = NULL` weren't showing
**Solution**: Changed INNER JOIN to LEFT JOIN in `Resource_model::get_resources_by_subject()`

```php
$this->db->join('faculty f', 'r.uploaded_by = f.user_id', 'left');
$this->db->join('users u', 'f.user_id = u.id', 'left');
```

### 2. AI Chat Subject Selection

**Implementation**: Three-step process
1. Semester selection (shows 8 semesters with lock/unlock status)
2. Subject selection (filtered by semester and enrollment)
3. Chat interface (resource-specific context)

**Session Reset**: When switching resources, `sessionId = null` to create new chat context

### 3. Markdown Formatting in Chat

**JavaScript Function**: `formatMarkdown()`
- Converts `**bold**` to `<strong>`
- Converts `*italic*` to `<em>`
- Converts `` `code` `` to `<code>`
- Converts headings (#, ##, ###)
- Converts bullet points and numbered lists

### 4. Mindmap Visualization

**Layout**: Horizontal tree (left to right)
- Central topic on left
- Main branches in middle
- Sub-branches on right

**D3.js Configuration**:
- Canvas: 1000px × 800px (minimum)
- Node sizes: 15px (center), 10px (branches), 7px (sub-branches)
- Colors: #1D4486 (center), #4A76A8 (branches), #759B49 (sub-branches)
- Text truncation: 35/30/25 characters by depth
- Separation: 1.5-2x between nodes

## API Endpoints (AJAX)

### Student Endpoints

- `POST /simple_portal/send_ai_chat_message` - Send chat message
  - Params: session_id, message, resource_id
  - Returns: {success, message, session_id}

- `GET /simple_portal/get_subject_resources` - Get resources for subject
  - Params: subject_id
  - Returns: {success, resources[]}

- `POST /simple_portal/process_mindmap_generation` - Generate mindmap
  - Params: resource_ids (JSON array), subject_id
  - Returns: {success, mindmap_data, subject_name, resources_used}

### Faculty Endpoints

- `POST /simple_portal/upload_resource` - Upload resource
- `POST /simple_portal/generate_question_paper` - Generate question paper
- `POST /simple_portal/generate_quiz` - Generate quiz
- `POST /simple_portal/generate_assignment` - Generate assignment

## Security Features

1. **Session Validation**: All routes check `logged_in` status
2. **Role-Based Access**: Routes verify user role before access
3. **Input Sanitization**: Form validation on all inputs
4. **File Upload Validation**: Type and size checks
5. **SQL Injection Prevention**: CodeIgniter Query Builder
6. **XSS Protection**: `htmlspecialchars()` on output
7. **CSRF Protection**: CodeIgniter CSRF tokens
8. **Password Hashing**: `password_hash()` with bcrypt

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer (for PDF parser)
- Web server (Apache/Nginx)

### Steps

1. **Clone/Extract Project**
   ```bash
   # Place in web server directory
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Database Setup**
   - Create database: `college_academic_portal`
   - Import schema: `databas/college_academic_portal.sql`
   - Update config: `application/config/database.php`

4. **Configure Base URL**
   ```php
   // application/config/config.php
   $config['base_url'] = 'http://localhost:8000/';
   ```

5. **Set Permissions**
   ```bash
   chmod -R 755 uploads/
   chmod -R 755 application/cache/
   chmod -R 755 application/logs/
   ```

6. **Start Server**
   ```bash
   php -S localhost:8000
   ```

7. **Access Portal**
   - URL: `http://localhost:8000/simple_portal`
   - Default admin: Create via database or setup script

## Common Issues & Solutions

### 1. Resources Not Showing
- **Cause**: INNER JOIN with faculty table fails on NULL faculty_id
- **Solution**: Use LEFT JOIN in Resource_model

### 2. AI Chat Not Working
- **Cause**: PDF extraction failing or API key invalid
- **Solution**: Check Composer dependencies, verify API key

### 3. Mindmap Empty/White
- **Cause**: Container width = 0 when rendering
- **Solution**: Use parent width or set minimum width (1000px)

### 4. Session Not Ending
- **Cause**: Browser not triggering beforeunload
- **Solution**: Verify JavaScript in header template

### 5. Text Overlapping in Mindmap
- **Cause**: Radial layout with too many nodes
- **Solution**: Use horizontal tree layout with increased spacing

## Development Guidelines

### Adding New Features

1. **Controller Method**: Add to `Simple_portal.php`
2. **Model**: Create/update model in `application/models/`
3. **View**: Create view in `application/views/simple_portal/`
4. **Route**: Add to `application/config/routes.php` if needed
5. **Navigation**: Update sidebar component
6. **Database**: Add tables/columns as needed

### Code Style

- Follow CodeIgniter conventions
- Use meaningful variable names
- Comment complex logic
- Validate all inputs
- Handle errors gracefully
- Log important events

### Testing

- Test all user roles
- Test file uploads (various formats)
- Test AI features (with actual content)
- Test on different browsers
- Test mobile responsiveness
- Check error handling

## Future Enhancements

### Planned Features
1. **Mindmap Persistence**: Save generated mindmaps
2. **Export Options**: Download mindmaps as PNG/PDF/SVG
3. **Collaborative Features**: Share mindmaps with classmates
4. **Quiz Analytics**: Track student performance
5. **Assignment Submissions**: Upload and grade assignments
6. **Real-time Notifications**: WebSocket integration
7. **Mobile App**: React Native/Flutter app
8. **Advanced Analytics**: Learning insights dashboard
9. **Video Resources**: Support for video uploads
10. **Discussion Forums**: Subject-wise discussion boards

## Support & Maintenance

### Logs Location
- Application logs: `application/logs/log-YYYY-MM-DD.php`
- Error logs: `application/logs/error_handler.log`

### Backup Strategy
- Database: Daily automated backups
- Uploads: Weekly backups
- Code: Version control (Git)

### Monitoring
- Check logs daily for errors
- Monitor disk space (uploads folder)
- Monitor API usage (Gemini quotas)
- Check database performance

## Credits

- **Framework**: CodeIgniter 3
- **AI**: Google Gemini API
- **PDF Parser**: Smalot PDF Parser
- **Visualization**: D3.js
- **Icons**: Font Awesome
- **CSS**: Bootstrap 5

## License

This project is proprietary software developed for educational purposes.

---

**Last Updated**: January 2026
**Version**: 1.0
**Status**: Production Ready
