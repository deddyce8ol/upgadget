<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * API Configuration
 *
 * Configuration for REST API including:
 * - API keys for authentication
 * - Rate limiting settings
 * - Default pagination settings
 */

// Valid API keys for authentication
// Add your API keys here. You can generate keys using: bin2hex(random_bytes(32))
$config['valid_api_keys'] = [
    'dev_key_12345',  // Development key
    // Add more keys here for production
    // Example: 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6'
];

// API version
$config['api_version'] = 'v1';

// Default pagination settings
$config['api_default_per_page'] = 20;
$config['api_max_per_page'] = 100;

// Rate limiting (requests per minute)
$config['api_rate_limit'] = 60;

// Enable/disable API key requirement
$config['api_require_key'] = false;  // Set to true in production

// API response settings
$config['api_pretty_print'] = true;
$config['api_unescaped_unicode'] = true;

// Allowed origins for CORS
$config['api_allowed_origins'] = ['*'];  // Use specific domains in production

// API documentation URL
$config['api_docs_url'] = base_url('api/docs');
