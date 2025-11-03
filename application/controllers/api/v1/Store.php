<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/API_Controller.php';

/**
 * Store API Controller
 *
 * Provides store/site information endpoints:
 * - info: Get store information (name, contact, address, etc.)
 * - settings: Get all public settings
 * - contact: Get contact information only
 */
class Store extends API_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Set API key requirement (optional)
        $this->api_key_required = $this->config->item('api_require_key', 'api_config');
    }

    /**
     * Main endpoint - routes to different actions
     * GET /api/v1/store?action=info
     */
    public function index()
    {
        $action = $this->input->get('action', TRUE) ?: 'info';

        switch ($action) {
            case 'info':
                $this->info();
                break;

            case 'settings':
                $this->settings();
                break;

            case 'contact':
                $this->contact();
                break;

            default:
                $this->_json_response(
                    false,
                    'Invalid action. Valid actions: info, settings, contact',
                    null,
                    'ERR_INVALID_ACTION',
                    400
                );
                break;
        }
    }

    /**
     * Get store information
     * GET /api/v1/store?action=info
     *
     * Returns essential store information including:
     * - Store name, tagline, description
     * - Contact information
     * - Address
     * - Logo and favicon
     * - Currency settings
     */
    public function info()
    {
        // Get all settings from database
        $settings_raw = $this->db->get('site_settings')->result_array();

        // Convert to key-value array
        $settings = [];
        foreach ($settings_raw as $setting) {
            $settings[$setting['setting_key']] = $setting['setting_value'];
        }

        // Build store info response
        $store_info = [
            'name' => $settings['site_name'] ?? 'Putra Elektronik',
            'tagline' => $settings['site_tagline'] ?? '',
            'description' => $settings['site_description'] ?? '',
            'email' => $settings['site_email'] ?? $settings['contact_email'] ?? '',
            'phone' => $settings['site_phone'] ?? $settings['contact_phone'] ?? '',
            'whatsapp' => $settings['whatsapp_number'] ?? '',
            'address' => $settings['site_address'] ?? '',
            'logo' => !empty($settings['site_logo'])
                ? base_url('uploads/settings/' . $settings['site_logo'])
                : null,
            'favicon' => !empty($settings['site_favicon'])
                ? base_url('uploads/settings/' . $settings['site_favicon'])
                : null,
            'currency' => [
                'symbol' => $settings['currency_symbol'] ?? 'Rp',
                'code' => $settings['currency_code'] ?? 'IDR'
            ],
            'social_media' => [
                'facebook' => $settings['facebook_url'] ?? null,
                'instagram' => $settings['instagram_url'] ?? null,
                'twitter' => $settings['twitter_url'] ?? null,
                'youtube' => $settings['youtube_url'] ?? null
            ],
            'features' => [
                'wishlist_enabled' => (bool)($settings['enable_wishlist'] ?? false),
                'reviews_enabled' => (bool)($settings['enable_reviews'] ?? false)
            ],
            'seo' => [
                'meta_description' => $settings['meta_description'] ?? '',
                'meta_keywords' => $settings['meta_keywords'] ?? ''
            ]
        ];

        $this->_json_response(
            true,
            'Store information retrieved successfully',
            $store_info,
            null,
            200
        );
    }

    /**
     * Get all public settings
     * GET /api/v1/store?action=settings
     *
     * Returns all public settings in key-value format
     * (excludes sensitive settings like API keys, payment credentials, etc.)
     */
    public function settings()
    {
        // Get all settings
        $settings_raw = $this->db->get('site_settings')->result_array();

        // Private keys that should not be exposed via API
        $private_keys = [
            'smtp_password',
            'payment_api_key',
            'payment_secret',
            'google_analytics_key',
            'api_secret'
        ];

        // Convert to key-value array and filter private keys
        $settings = [];
        foreach ($settings_raw as $setting) {
            // Skip private settings
            if (in_array($setting['setting_key'], $private_keys)) {
                continue;
            }

            $key = $setting['setting_key'];
            $value = $setting['setting_value'];
            $type = $setting['setting_type'];

            // Type casting based on setting_type
            switch ($type) {
                case 'boolean':
                    $value = (bool)$value;
                    break;
                case 'number':
                    $value = is_numeric($value) ? (int)$value : $value;
                    break;
                case 'image':
                    // Convert to full URL if not empty
                    if (!empty($value)) {
                        $value = base_url('uploads/settings/' . $value);
                    }
                    break;
            }

            $settings[$key] = $value;
        }

        $this->_json_response(
            true,
            'Settings retrieved successfully',
            $settings,
            null,
            200,
            ['count' => count($settings)]
        );
    }

    /**
     * Get contact information only
     * GET /api/v1/store?action=contact
     *
     * Returns only contact-related information:
     * - Email
     * - Phone
     * - WhatsApp
     * - Address
     */
    public function contact()
    {
        // Get settings
        $settings_raw = $this->db->get('site_settings')->result_array();

        // Convert to key-value
        $settings = [];
        foreach ($settings_raw as $setting) {
            $settings[$setting['setting_key']] = $setting['setting_value'];
        }

        // Build contact info
        $contact_info = [
            'name' => $settings['site_name'] ?? 'Putra Elektronik',
            'email' => $settings['site_email'] ?? $settings['contact_email'] ?? '',
            'phone' => $settings['site_phone'] ?? $settings['contact_phone'] ?? '',
            'whatsapp' => $settings['whatsapp_number'] ?? '',
            'address' => $settings['site_address'] ?? '',
            'social_media' => [
                'facebook' => $settings['facebook_url'] ?? null,
                'instagram' => $settings['instagram_url'] ?? null,
                'twitter' => $settings['twitter_url'] ?? null,
                'youtube' => $settings['youtube_url'] ?? null
            ],
            'business_hours' => [
                'weekdays' => $settings['business_hours_weekdays'] ?? null,
                'weekend' => $settings['business_hours_weekend'] ?? null
            ]
        ];

        $this->_json_response(
            true,
            'Contact information retrieved successfully',
            $contact_info,
            null,
            200
        );
    }
}
