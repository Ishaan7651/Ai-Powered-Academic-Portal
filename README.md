# 🎓 AI-Powered Academic Portal

A comprehensive web-based academic management system built with CodeIgniter 3, featuring AI-powered tools for generating assignments, quizzes, question papers, and interactive learning resources.

## ✨ Features

### 👨‍💼 Admin Features
- User management (students, faculty, admins)
- Department and subject management
- Faculty assignment to subjects
- System-wide analytics and reporting
- Resource oversight and approval

### 👨‍🏫 Faculty Features
- **Resource Management**: Upload and manage course materials (PDFs, PPTs, Excel, EPUB)
- **AI-Powered Content Generation**:
  - Generate assignments with customizable difficulty levels
  - Create question papers with various formats
  - Generate quizzes with multiple question types
  - Create flashcards for quick revision
  - Generate mind maps for visual learning
- **Student Progress Tracking**: Monitor student performance and engagement
- **Subject Management**: Manage multiple subjects and semesters

### 👨‍🎓 Student Features
- **Semester-Based Access**: Access resources based on current semester
- **AI Study Buddy**: Interactive chat with course materials
- **Assignment Submission**: Submit and track assignments
- **Quiz Taking**: Take AI-generated quizzes with instant feedback
- **Resource Library**: Access PDFs, presentations, and web links
- **Schedule Management**: View class schedules and deadlines
- **Progress Dashboard**: Track academic performance

### 🤖 AI Features
- **Chat with Resources**: Ask questions about uploaded course materials
- **Smart Assignment Generation**: Create contextual assignments from PDFs
- **Question Paper Generation**: Auto-generate exam papers with customizable formats
- **Quiz Generation**: Create multiple-choice quizzes with explanations
- **Flashcard Generation**: Extract key concepts for quick revision
- **Mind Map Creation**: Visual representation of topics and concepts

## 🛠️ Technology Stack

- **Backend**: PHP 7.2+ with CodeIgniter 3
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **AI Integration**: OpenAI API (GPT models)
- **PDF Processing**: Custom PDF parser library
- **Session Management**: File-based sessions with enhanced security

## 📋 Requirements

- PHP 7.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server
- Composer (optional, for dependencies)
- OpenAI API key (for AI features)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/Ishaan7651/Ai-Powered-Academic-Portal.git
cd Ai-Powered-Academic-Portal
```

### 2. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE college_academic_portal;
exit;

# Import database schema
mysql -u root -p college_academic_portal < databas/college_academic_portal.sql

# (Optional) Add faculty departments
mysql -u root -p college_academic_portal < add_faculty_departments.sql
```

### 3. Configure Application

Edit `application/config/database.php`:
```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'your_db_username',
    'password' => 'your_db_password',
    'database' => 'college_academic_portal',
);
```

Edit `application/config/config.php`:
```php
$config['base_url'] = 'http://your-domain.com/';
$config['encryption_key'] = 'your-secret-key-here'; // Change this!
```

### 4. Set Up File Permissions
```bash
chmod -R 755 application/cache
chmod -R 755 application/logs
chmod -R 755 uploads
```

### 5. Configure AI Service

Edit `application/libraries/AI_service.php` and add your OpenAI API key:
```php
private $api_key = 'your-openai-api-key-here';
```

### 6. Web Server Configuration

#### Apache (.htaccess included)
Ensure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx
Add to your server block:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 📁 Project Structure

```
├── application/
│   ├── controllers/     # Application controllers
│   ├── models/          # Database models
│   ├── views/           # View templates
│   ├── libraries/       # Custom libraries (AI service, PDF parser)
│   ├── config/          # Configuration files
│   └── logs/            # Application logs
├── assets/
│   ├── css/             # Stylesheets
│   └── js/              # JavaScript files
├── uploads/             # User-uploaded files
├── system/              # CodeIgniter core files
└── databas/             # Database schema files
```

## 🔐 Default Credentials

After installation, create an admin user through the database or use the registration system.

**Security Note**: Change all default passwords immediately after installation.

## 🎯 Usage

### For Administrators
1. Log in with admin credentials
2. Create departments and subjects
3. Add faculty members and assign subjects
4. Create student accounts
5. Monitor system usage and analytics

### For Faculty
1. Log in with faculty credentials
2. Upload course materials (PDFs, presentations)
3. Use AI tools to generate assignments and quizzes
4. Publish resources for students
5. Track student progress

### For Students
1. Log in with student credentials
2. Access semester-specific resources
3. Use AI Study Buddy to chat with course materials
4. Complete assignments and quizzes
5. Track your academic progress

## 🔧 Configuration

### AI Features Configuration
- Configure AI models in `application/libraries/AI_service.php`
- Adjust generation parameters (temperature, max tokens)
- Customize prompt templates for different content types

### Upload Limits
Edit `application/config/upload.php` to modify:
- Maximum file size
- Allowed file types
- Upload directory paths

### Session Security
Configure in `application/config/config.php`:
- Session expiration time
- Cookie security settings
- CSRF protection

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🐛 Known Issues

- Large PDF files (>50MB) may timeout during AI processing
- Session timeout requires browser refresh in some cases
- AI generation may take 30-60 seconds for complex content

## 🔮 Future Enhancements

- [ ] Mobile application (iOS/Android)
- [ ] Real-time notifications
- [ ] Video lecture integration
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Integration with LMS platforms
- [ ] Plagiarism detection
- [ ] Automated grading system

## 📧 Support

For issues, questions, or suggestions:
- Open an issue on [GitHub](https://github.com/Ishaan7651/Ai-Powered-Academic-Portal/issues)

## 🙏 Acknowledgments

- CodeIgniter framework
- OpenAI for AI capabilities
- All contributors and testers

---

**Note**: This is an educational project. Ensure compliance with your institution's policies and data protection regulations before deployment.
