# College Academic Portal - Installation Guide

## 🚀 Complete Setup Instructions

### Prerequisites

1. **Web Server with PHP 7.4+**
   - XAMPP (Recommended): https://www.apachefriends.org/
   - WAMP: http://www.wampserver.com/
   - MAMP: https://www.mamp.info/

2. **MySQL Database**
   - Included with XAMPP/WAMP/MAMP
   - phpMyAdmin for database management

### Step-by-Step Installation

#### 1. Install Web Server
```bash
# Download and install XAMPP
# Start Apache and MySQL services from XAMPP Control Panel
```

#### 2. Download CodeIgniter System Files
```bash
# Go to https://codeigniter.com/download
# Download CodeIgniter 3.x
# Extract the 'system' folder to your project directory
```

#### 3. Set Up Project Files
```bash
# Copy the college-academic-portal folder to:
# - XAMPP: C:\xampp\htdocs\
# - WAMP: C:\wamp64\www\
# - MAMP: /Applications/MAMP/htdocs/
```

#### 4. Configure Database
```bash
# Open phpMyAdmin: http://localhost/phpmyadmin
# Create database: college_academic_portal
# Or use the automated setup script
```

#### 5. Run Setup Script
```bash
# Visit: http://localhost/college-academic-portal/setup_full_portal.php
# Follow the setup instructions
# Run database setup if needed
```

### Quick Setup (Automated)

1. **Check System Requirements**
   ```
   http://localhost/college-academic-portal/setup_full_portal.php
   ```

2. **Set Up Database**
   ```
   http://localhost/college-academic-portal/setup_database.php
   ```

3. **Launch Portal**
   ```
   http://localhost/college-academic-portal/
   ```

### Default Login Credentials

- **Admin**: `admin` / `admin123`
- **Faculty**: Create via admin panel
- **Student**: Create via admin panel

### Project Structure

```
college-academic-portal/
├── system/                 # CodeIgniter system files (download separately)
├── application/           # Application code
│   ├── controllers/       # MVC Controllers
│   ├── models/           # Database models
│   ├── views/            # UI templates
│   ├── config/           # Configuration files
│   └── libraries/        # Custom libraries (AI integration)
├── assets/               # CSS, JS, images
│   ├── css/             # Custom styles
│   └── js/              # Custom JavaScript
├── uploads/              # File uploads directory
├── database_schema.sql   # Database structure
├── setup_database.php    # Database setup script
└── setup_full_portal.php # System setup script
```

### Configuration Files

#### Database Configuration
File: `application/config/database.php`
```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'college_academic_portal',
    'dbdriver' => 'mysqli',
    // ... other settings
);
```

#### Base URL Configuration
File: `application/config/config.php`
```php
$config['base_url'] = 'http://localhost/college-academic-portal/';
```

### Features Included

✅ **User Management**
- Admin, Faculty, Student roles
- Role-based access control
- Session management

✅ **Content Management**
- File upload system
- Resource organization
- Subject assignment

✅ **AI Integration**
- Gemini API integration
- Document chat functionality
- Assignment generation

✅ **Modern UI/UX**
- Responsive design
- Bootstrap 5 framework
- Custom styling
- Mobile optimization

✅ **Database Features**
- Complete schema
- Sample data
- Migration system

### Troubleshooting

#### Common Issues

1. **"System folder not found"**
   - Download CodeIgniter 3.x system files
   - Extract 'system' folder to project root

2. **Database connection failed**
   - Start MySQL service in XAMPP/WAMP
   - Check database credentials
   - Ensure database exists

3. **Permission denied errors**
   - Set proper file permissions
   - Ensure uploads/ directory is writable

4. **Base URL issues**
   - Update `application/config/config.php`
   - Set correct base_url for your setup

#### Getting Help

1. **Check Setup Status**
   ```
   http://localhost/college-academic-portal/setup_full_portal.php
   ```

2. **View Demo (No Database Required)**
   ```
   http://localhost/college-academic-portal/simple_demo.php
   ```

3. **Database Setup**
   ```
   http://localhost/college-academic-portal/setup_database.php
   ```

### Development vs Production

#### Development (Current Setup)
- Error reporting enabled
- Database debugging on
- Demo credentials active

#### Production Deployment
- Update `ENVIRONMENT` to 'production'
- Change default passwords
- Configure proper database credentials
- Set up SSL/HTTPS
- Configure proper file permissions

### Next Steps

1. **Complete Installation**: Follow the setup scripts
2. **Create Users**: Use admin panel to add faculty/students
3. **Upload Content**: Add course materials
4. **Configure AI**: Set up Gemini API keys
5. **Customize**: Modify styling and features as needed

---

**College Academic Portal** - AI-Integrated Educational Management System