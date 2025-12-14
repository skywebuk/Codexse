# Edugo LMS - WordPress Learning Management System

A comprehensive WordPress LMS plugin for creating and selling online courses. Features multi-instructor support, frontend dashboards, WooCommerce monetization, quizzes, assignments, certificates, and more.

## Features

### Course Management
- Custom Post Types for Courses, Lessons, Quizzes, and Assignments
- Course categories, tags, and difficulty levels
- Prerequisites and drip content scheduling
- Course curriculum builder

### Instructor System
- Custom Instructor role with permissions
- Frontend Instructor Dashboard
- Course creation and editing from frontend
- Course submission and admin approval workflow
- Instructor earnings and commission tracking

### Student System
- Student enrollment management
- Course progress tracking
- Lesson completion tracking
- Quiz attempts and grading
- Assignment submission
- Course completion certificates (PDF)

### Quiz & Assignment System
**Quiz Features:**
- Multiple choice questions
- True/False questions
- Short answer questions
- Time limits
- Pass/fail logic
- Retake limits

**Assignment Features:**
- File upload support
- Text submissions
- Manual grading by instructors
- Instructor feedback

### Monetization & Payments
- WooCommerce integration
- Course as WooCommerce product
- Free and paid courses
- One-time payment support
- Subscription support (via WooCommerce Subscriptions)
- Instructor commission system
- Coupons and discounts

### Frontend Dashboards
**Student Dashboard:**
- Enrolled courses list
- Progress bars
- Quiz results
- Certificate downloads
- Profile management

**Instructor Dashboard:**
- Courses management
- Students list
- Earnings reports
- Withdrawal request system

### Admin Features
- LMS settings panel
- Course approval mode
- Commission percentage configuration
- Certificate templates
- Quiz rules configuration
- Progress calculation logic
- Email notifications

## Requirements

- WordPress 6.0+
- PHP 8.0+
- MySQL 5.7+
- WooCommerce (optional, for monetization)

## Installation

1. Upload the `edugo-lms` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to **Edugo LMS > Settings** to configure the plugin
4. Create your first course from **Edugo LMS > Courses > Add New**

## Plugin Structure

```
edugo-lms/
├── edugo-lms.php              # Main plugin file
├── includes/
│   ├── Core/                  # Core classes (Loader, Activator, etc.)
│   ├── Admin/                 # Admin functionality
│   ├── Frontend/              # Frontend functionality
│   ├── LMS/                   # LMS functionality
│   │   ├── Course/
│   │   ├── Lesson/
│   │   ├── Quiz/
│   │   ├── Assignment/
│   │   ├── Enrollment/
│   │   ├── Progress/
│   │   └── Certificate/
│   ├── Integrations/          # Third-party integrations
│   │   └── WooCommerce/
│   ├── REST/                  # REST API endpoints
│   └── Helpers/               # Utility functions
├── templates/                 # Template files
│   ├── dashboard/
│   ├── course/
│   ├── quiz/
│   └── certificate/
├── assets/                    # CSS, JS, images
│   ├── css/
│   ├── js/
│   └── images/
└── languages/                 # Translation files
```

## REST API Endpoints

The plugin provides a comprehensive REST API:

- `GET /wp-json/edugo/v1/courses` - List courses
- `GET /wp-json/edugo/v1/courses/{id}` - Get course details
- `GET /wp-json/edugo/v1/courses/{id}/lessons` - Get course lessons
- `POST /wp-json/edugo/v1/enroll` - Enroll in a course
- `GET /wp-json/edugo/v1/enrollments` - Get user enrollments
- `GET /wp-json/edugo/v1/progress/{course_id}` - Get course progress
- `POST /wp-json/edugo/v1/progress/lesson/{id}/complete` - Mark lesson complete
- `POST /wp-json/edugo/v1/quiz/{id}/submit` - Submit quiz
- `GET /wp-json/edugo/v1/certificate/verify/{key}` - Verify certificate

## Hooks & Filters

### Actions
- `edugo_student_enrolled` - Fired when a student enrolls
- `edugo_lesson_complete` - Fired when a lesson is completed
- `edugo_course_completed` - Fired when a course is completed
- `edugo_quiz_attempt_recorded` - Fired after quiz submission
- `edugo_certificate_generated` - Fired after certificate generation

### Filters
- `edugo_course_post_type_args` - Modify course CPT arguments
- `edugo_certificate_content` - Modify certificate content
- `edugo_assignment_allowed_file_types` - Modify allowed file types
- `edugo_assignment_max_file_size` - Modify max upload size

## Shortcodes

- `[edugo_student_dashboard]` - Display student dashboard
- `[edugo_instructor_dashboard]` - Display instructor dashboard
- `[edugo_courses]` - Display courses grid
- `[edugo_my_courses]` - Display enrolled courses
- `[edugo_certificate_verify]` - Certificate verification form

## Customization

### Template Override
Templates can be overridden by copying them to your theme:
```
your-theme/edugo-lms/course/courses-grid.php
```

### Styling
Add custom CSS in your theme or use the provided CSS classes with the `edugo-` prefix.

## Support

For support, please visit [https://edugo-lms.com/support](https://edugo-lms.com/support)

## License

GPL v2 or later - [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

## Changelog

### 1.0.0
- Initial release
- Course, Lesson, Quiz, Assignment CPTs
- Enrollment and progress tracking
- Instructor and student dashboards
- WooCommerce integration
- REST API
- Certificate generation
