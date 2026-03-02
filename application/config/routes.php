<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| URI ROUTING
|--------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Examples:
|	$route['default_controller'] = 'welcome';
|	$route['my-controller/my-method'] = 'welcome/index';
|	$route['my-controller/(:any)'] = 'welcome/view/$1';
|
| Requirements: 7.2 - Follow CodeIgniter framework MVC architecture patterns
*/

$route['default_controller'] = 'simple_portal';

// Enhanced Simple Portal Routes
$route['simple_portal/resources'] = 'simple_portal/resources';
$route['simple_portal/upload_resource'] = 'simple_portal/upload_resource';
$route['simple_portal/student_resources/(:num)'] = 'simple_portal/student_resources/$1';
$route['simple_portal/student_resources'] = 'simple_portal/student_resources';
$route['simple_portal/download_resource/(:num)'] = 'simple_portal/download_resource/$1';
$route['simple_portal/settings'] = 'simple_portal/settings';
$route['simple_portal/settings_update_password'] = 'simple_portal/settings_update_password';
$route['simple_portal/manage_departments'] = 'simple_portal/manage_departments';
$route['simple_portal/save_department'] = 'simple_portal/save_department';
$route['simple_portal/delete_department/(:num)'] = 'simple_portal/delete_department/$1';
$route['simple_portal/generate_assignment'] = 'simple_portal/generate_assignment';
$route['simple_portal/generate_quiz'] = 'simple_portal/generate_quiz';
$route['simple_portal/generate_question_paper'] = 'simple_portal/generate_question_paper';
$route['simple_portal/process_quiz_generation'] = 'simple_portal/process_quiz_generation';
$route['simple_portal/process_assignment_generation'] = 'simple_portal/process_assignment_generation';
$route['simple_portal/process_question_paper_generation'] = 'simple_portal/process_question_paper_generation';
$route['simple_portal/publish_quiz'] = 'simple_portal/publish_quiz';
$route['simple_portal/publish_assignment'] = 'simple_portal/publish_assignment';
$route['simple_portal/publish_question_paper'] = 'simple_portal/publish_question_paper';
$route['simple_portal/ai_features'] = 'simple_portal/ai_features';
$route['simple_portal/manage_subjects'] = 'simple_portal/manage_subjects';

// Student Published Content Routes
$route['simple_portal/student_question_papers'] = 'simple_portal/student_question_papers';
$route['simple_portal/student_quizzes'] = 'simple_portal/student_quizzes';
$route['simple_portal/student_assignments'] = 'simple_portal/student_assignments';
$route['simple_portal/view_question_paper/(:num)'] = 'simple_portal/view_question_paper/$1';
$route['simple_portal/download_question_paper/(:num)'] = 'simple_portal/download_question_paper/$1';
$route['simple_portal/take_quiz/(:num)'] = 'simple_portal/take_quiz/$1';
$route['simple_portal/submit_quiz'] = 'simple_portal/submit_quiz';
$route['simple_portal/view_assignment/(:num)'] = 'simple_portal/view_assignment/$1';

// AI Chat Routes within Simple Portal
$route['simple_portal/ai_chat'] = 'simple_portal/ai_chat';
$route['simple_portal/ai_chat/(:num)'] = 'simple_portal/ai_chat/$1';
$route['simple_portal/create_ai_chat_session'] = 'simple_portal/create_ai_chat_session';
$route['simple_portal/send_ai_chat_message'] = 'simple_portal/send_ai_chat_message';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Working Version)
|--------------------------------------------------------------------------
*/
$route['auth'] = 'auth_working/index';
$route['login'] = 'auth_working/index';
$route['logout'] = 'auth_working/logout';
$route['login/(:any)'] = 'auth_working/login/$1';

/*
|--------------------------------------------------------------------------
| Admin Routes (Working Version)
|--------------------------------------------------------------------------
*/
$route['admin'] = 'admin_working/dashboard';
$route['admin/dashboard'] = 'admin_working/dashboard';
$route['admin/users'] = 'admin_working/users';
$route['admin/faculty'] = 'admin_working/faculty';
$route['admin/create_faculty'] = 'admin/create_faculty';
$route['admin/edit_faculty/(:num)'] = 'admin/edit_faculty/$1';
$route['admin/remove_faculty/(:num)'] = 'admin/remove_faculty/$1';
$route['admin/assign_subject'] = 'admin/assign_subject';
$route['admin/remove_subject_assignment'] = 'admin/remove_subject_assignment';
$route['admin/permissions'] = 'admin/permissions';
$route['admin/update_role'] = 'admin/update_role';
$route['admin/toggle_user_status'] = 'admin/toggle_user_status';

/*
|--------------------------------------------------------------------------
| Faculty Routes (Working Version)
|--------------------------------------------------------------------------
*/
$route['faculty'] = 'faculty_working/dashboard';
$route['faculty/dashboard'] = 'faculty_working/dashboard';
$route['faculty/resources'] = 'faculty_working/resource_management';
$route['faculty/resource_management'] = 'faculty_working/resource_management';
$route['faculty/upload_resource'] = 'faculty_working/upload_resource';
$route['faculty/edit_resource/(:num)'] = 'faculty/edit_resource/$1';
$route['faculty/delete_resource/(:num)'] = 'faculty/delete_resource/$1';
$route['faculty/download_resource/(:num)'] = 'faculty/download_resource/$1';
$route['faculty/assignments'] = 'faculty/assignment_management';
$route['faculty/question_papers'] = 'faculty/question_paper_management';
$route['faculty/question_paper_management'] = 'faculty/question_paper_management';
$route['faculty/generate_question_paper'] = 'faculty/generate_question_paper';
$route['faculty/view_question_paper/(:num)'] = 'faculty/view_question_paper/$1';
$route['faculty/download_question_paper/(:num)'] = 'faculty/download_question_paper/$1';
$route['faculty/regenerate_question_paper/(:num)'] = 'faculty/regenerate_question_paper/$1';
$route['faculty/delete_question_paper/(:num)'] = 'faculty/delete_question_paper/$1';
$route['faculty/create_format_template'] = 'faculty/create_format_template';
$route['faculty/delete_format_template/(:num)'] = 'faculty/delete_format_template/$1';
/*
|--------------------------------------------------------------------------
| AI Buddy Routes
|--------------------------------------------------------------------------
*/
$route['ai_buddy'] = 'ai_buddy/index';
$route['ai_buddy/index'] = 'ai_buddy/index';
$route['ai_buddy/chat'] = 'ai_buddy/chat';
$route['ai_buddy/chat/(:num)'] = 'ai_buddy/chat/$1';
$route['ai_buddy/generate_quiz'] = 'ai_buddy/generate_quiz';
$route['ai_buddy/generate_quiz/(:num)'] = 'ai_buddy/generate_quiz/$1';
$route['ai_buddy/generate_assignment'] = 'ai_buddy/generate_assignment';
$route['ai_buddy/generate_assignment/(:num)'] = 'ai_buddy/generate_assignment/$1';
$route['ai_buddy/generate_question_paper'] = 'ai_buddy/generate_question_paper';
$route['ai_buddy/generate_question_paper/(:num)'] = 'ai_buddy/generate_question_paper/$1';
$route['ai_buddy/process_quiz_generation'] = 'ai_buddy/process_quiz_generation';
$route['ai_buddy/process_assignment_generation'] = 'ai_buddy/process_assignment_generation';
$route['ai_buddy/process_question_paper_generation'] = 'ai_buddy/process_question_paper_generation';

/*
|--------------------------------------------------------------------------
| Student Routes (Working Version)
|--------------------------------------------------------------------------
*/
$route['student'] = 'student_working/dashboard';
$route['student/dashboard'] = 'student_working/dashboard';
$route['student/resources'] = 'student_working/resource_access';
$route['student/resource_access/(:num)'] = 'student_working/resource_access/$1';
$route['student/download_resource/(:num)'] = 'student_working/download_resource/$1';
$route['student/document_chat/(:num)'] = 'student/document_chat/$1';
$route['student/send_chat_message'] = 'student/send_chat_message';
$route['student/get_chat_history'] = 'student/get_chat_history';
$route['student/update_semester_progression'] = 'student/update_semester_progression';
$route['student/my_chat_sessions'] = 'student/my_chat_sessions';
$route['student/delete_chat_session'] = 'student/delete_chat_session';

/*
|--------------------------------------------------------------------------
| Migration Routes
|--------------------------------------------------------------------------
| Requirements: 7.1, 7.3 - Database setup with CodeIgniter abstraction
*/
$route['migrate'] = 'migrate/index';
$route['migrate/(:any)'] = 'migrate/$1';

/*
|--------------------------------------------------------------------------
| Error Monitor Routes
|--------------------------------------------------------------------------
*/
$route['error_monitor'] = 'error_monitor/dashboard';
$route['error_monitor/(:any)'] = 'error_monitor/$1';