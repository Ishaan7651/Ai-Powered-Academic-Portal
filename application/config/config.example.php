<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Configuration Example
|--------------------------------------------------------------------------
| Copy this file to config.php and update with your settings
*/

$config['base_url'] = 'http://localhost:8000/';
$config['index_page'] = '';
$config['uri_protocol']	= 'REQUEST_URI';
$config['url_suffix'] = '';
$config['language']	= 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = FALSE;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';
$config['allow_get_array'] = TRUE;
$config['log_threshold'] = 1;
$config['log_path'] = APPPATH . 'logs/';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['error_views_path'] = '';
$config['cache_path'] = APPPATH . 'cache/';
$config['cache_query_string'] = FALSE;

// IMPORTANT: Change this to a random string for security
$config['encryption_key'] = 'CHANGE-THIS-TO-A-RANDOM-SECRET-KEY';

// Session Configuration
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 0;
$config['sess_save_path'] = sys_get_temp_dir();
$config['sess_match_ip'] = TRUE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = TRUE;

// Cookie Configuration
$config['cookie_prefix']	= 'cap_';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE; // Set to TRUE in production with HTTPS
$config['cookie_httponly'] 	= TRUE;

// Security
$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = FALSE;
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_token';
$config['csrf_cookie_name'] = 'csrf_cookie';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();

// Other Settings
$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';
