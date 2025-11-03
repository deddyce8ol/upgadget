<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration Controller
 *
 * Simple controller to run database migrations.
 * Access via: http://localhost/putraelektronik/migrate
 *
 * SECURITY: This should be disabled in production or protected by authentication
 *
 * @package Putra Elektronik
 * @category Controllers
 */
class Migrate extends CI_Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // Security: Only allow in development environment
        if (ENVIRONMENT !== 'development') {
            show_404();
        }

        $this->load->library('migration');
    }

    /**
     * Run all pending migrations
     *
     * @return void
     */
    public function index()
    {
        echo '<h1>Database Migration</h1>';
        echo '<pre>';

        if ($this->migration->current() === FALSE) {
            echo '❌ Migration failed: ' . $this->migration->error_string();
        } else {
            echo '✅ Migration successful!';
            echo '\n\nDatabase is now up to date.';
        }

        echo '</pre>';
    }

    /**
     * Rollback to version 0 (remove all migrations)
     *
     * @return void
     */
    public function rollback()
    {
        echo '<h1>Database Rollback</h1>';
        echo '<pre>';

        if ($this->migration->version(0) === FALSE) {
            echo '❌ Rollback failed: ' . $this->migration->error_string();
        } else {
            echo '✅ Rollback successful!';
            echo '\n\nAll migrations have been rolled back.';
        }

        echo '</pre>';
    }
}
