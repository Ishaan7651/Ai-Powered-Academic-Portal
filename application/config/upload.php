<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * File Upload Configuration
 * 
 * Configuration settings for file uploads in the academic portal
 * Requirements: 2.1, 7.4 - File upload validation and CodeIgniter file handling
 */

/*
|--------------------------------------------------------------------------
| Upload Path Configuration
|--------------------------------------------------------------------------
*/
$config['upload_paths'] = array(
    'resources' => './uploads/resources/',
    'assignments' => './uploads/assignments/',
    'question_papers' => './uploads/question_papers/',
    'temp' => './uploads/temp/'
);

/*
|--------------------------------------------------------------------------
| File Size Limits (in KB)
|--------------------------------------------------------------------------
*/
$config['max_file_sizes'] = array(
    'pdf' => 102400,     // 100MB
    'ppt' => 102400,     // 100MB
    'excel' => 102400,   // 100MB
    'csv' => 102400,     // 100MB
    'epub' => 102400     // 100MB
);

/*
|--------------------------------------------------------------------------
| Allowed File Extensions
|--------------------------------------------------------------------------
*/
$config['allowed_extensions'] = array(
    'pdf' => array('pdf'),
    'ppt' => array('ppt', 'pptx'),
    'excel' => array('xls', 'xlsx'),
    'csv' => array('csv'),
    'epub' => array('epub')
);

/*
|--------------------------------------------------------------------------
| MIME Type Validation
|--------------------------------------------------------------------------
*/
$config['mime_types'] = array(
    'pdf' => array('application/pdf'),
    'ppt' => array(
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ),
    'excel' => array(
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ),
    'csv' => array('text/csv', 'application/csv', 'text/plain'),
    'epub' => array('application/epub+zip')
);

/*
|--------------------------------------------------------------------------
| Security Settings
|--------------------------------------------------------------------------
*/
$config['encrypt_name'] = TRUE;
$config['remove_spaces'] = TRUE;
$config['overwrite'] = FALSE;
$config['detect_mime'] = TRUE;

/*
|--------------------------------------------------------------------------
| File Naming Convention
|--------------------------------------------------------------------------
*/
$config['file_name_prefix'] = 'resource_';
$config['max_filename_length'] = 100;

/*
|--------------------------------------------------------------------------
| Enhanced Error Handling Configuration
|--------------------------------------------------------------------------
*/
$config['upload_error_messages'] = array(
    'upload_userfile_not_set' => 'No file was selected for upload.',
    'upload_file_exceeds_limit' => 'The uploaded file exceeds the maximum allowed size of %s.',
    'upload_file_exceeds_form_limit' => 'The uploaded file exceeds the maximum size allowed by the form.',
    'upload_file_partial' => 'The file was only partially uploaded. Please try again.',
    'upload_no_temp_directory' => 'Server configuration error: Missing temporary folder.',
    'upload_unable_to_write_file' => 'Failed to write file to disk. Please try again.',
    'upload_stopped_by_extension' => 'File upload stopped by extension validation.',
    'upload_no_file_selected' => 'You did not select a file to upload.',
    'upload_invalid_filetype' => 'The file type you are attempting to upload is not allowed. Supported formats: %s.',
    'upload_invalid_filesize' => 'The file you are attempting to upload is larger than the permitted size of %s.',
    'upload_invalid_dimensions' => 'The image dimensions are not within allowed limits.',
    'upload_destination_error' => 'A problem was encountered while moving the uploaded file.',
    'upload_no_filepath' => 'The upload path is not configured properly.',
    'upload_no_file_types' => 'No allowed file types have been specified.',
    'upload_bad_filename' => 'A file with this name already exists on the server.',
    'upload_not_writable' => 'The upload destination folder is not writable.'
);

/*
|--------------------------------------------------------------------------
| File Validation Settings
|--------------------------------------------------------------------------
*/
$config['strict_mime_validation'] = TRUE;
$config['validate_file_signature'] = TRUE;
$config['scan_for_malicious_content'] = TRUE;

/*
|--------------------------------------------------------------------------
| File Signature Validation (Magic Numbers)
|--------------------------------------------------------------------------
*/
$config['file_signatures'] = array(
    'pdf' => array('25504446'), // %PDF
    'ppt' => array('504B0304', 'D0CF11E0'), // ZIP and OLE
    'pptx' => array('504B0304'), // ZIP
    'xls' => array('D0CF11E0'), // OLE
    'xlsx' => array('504B0304'), // ZIP
    'csv' => array(), // Text files don't have reliable signatures
    'epub' => array('504B0304') // ZIP
);

/*
|--------------------------------------------------------------------------
| Upload Security Configuration
|--------------------------------------------------------------------------
*/
$config['quarantine_suspicious_files'] = TRUE;
$config['log_upload_attempts'] = TRUE;
$config['max_upload_attempts_per_hour'] = 50;
$config['blocked_extensions'] = array('exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'php', 'asp', 'jsp');

/*
|--------------------------------------------------------------------------
| Content Validation Rules
|--------------------------------------------------------------------------
*/
$config['content_validation'] = array(
    'csv' => array(
        'max_columns' => 100,
        'max_rows' => 10000,
        'required_encoding' => 'UTF-8'
    ),
    'pdf' => array(
        'max_pages' => 1000,
        'allow_password_protected' => FALSE
    )
);