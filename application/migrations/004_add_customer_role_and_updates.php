<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Customer Role and Table Updates
 *
 * 1. Adds Customer role (id=3) for public shop customers
 * 2. Updates categories table with public display fields
 * 3. Updates user_data table with customer-specific fields
 *
 * @package Putra Elektronik
 * @category Migrations
 * @version 1.0.0
 */
class Migration_Add_customer_role_and_updates extends CI_Migration
{
    /**
     * Run migration
     */
    public function up()
    {
        // 1. Add Customer role
        $customer_role = [
            'id' => 3,
            'role' => 'Customer',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('user_role', $customer_role);
        echo "✓ Customer role added (id=3)\n";

        // 2. Update categories table for public display enhancements
        $this->db->query("ALTER TABLE `categories`
            ADD COLUMN `sort_order` INT(11) DEFAULT 0 COMMENT 'Display order (lower = first)' AFTER `status`,
            ADD COLUMN `is_featured` TINYINT(1) DEFAULT 0 COMMENT 'Show on homepage' AFTER `sort_order`,
            ADD COLUMN `banner_image` VARCHAR(255) NULL COMMENT 'Category banner for shop page' AFTER `icon_path`,
            ADD COLUMN `meta_title` VARCHAR(255) NULL COMMENT 'SEO title' AFTER `banner_image`,
            ADD COLUMN `meta_description` VARCHAR(500) NULL COMMENT 'SEO description' AFTER `meta_title`");

        // Add indexes for categories
        $this->db->query('ALTER TABLE `categories` ADD KEY `idx_featured` (`is_featured`)');
        $this->db->query('ALTER TABLE `categories` ADD KEY `idx_sort` (`sort_order`)');

        echo "✓ Categories table updated with public display fields\n";

        // 3. Update user_data table for customer features
        $this->db->query("ALTER TABLE `user_data`
            ADD COLUMN `default_address` TEXT NULL COMMENT 'Primary shipping address' AFTER `address`,
            ADD COLUMN `wishlist` TEXT NULL COMMENT 'JSON array of product IDs (future feature)' AFTER `default_address`,
            ADD COLUMN `last_login_at` TIMESTAMP NULL COMMENT 'Track customer activity' AFTER `updated_at`,
            ADD COLUMN `email_verified` TINYINT(1) DEFAULT 0 COMMENT 'Email verification status' AFTER `email`");

        // Add indexes for user_data
        $this->db->query('ALTER TABLE `user_data` ADD KEY `idx_role` (`role_id`)');
        $this->db->query('ALTER TABLE `user_data` ADD KEY `idx_email` (`email`)');

        // Make email unique if not already
        $check_unique = $this->db->query("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
            AND table_name = 'user_data'
            AND index_name = 'uk_email'
        ")->row();

        if ($check_unique->count == 0) {
            // Check for duplicate emails first
            $duplicates = $this->db->query("
                SELECT email, COUNT(*) as count
                FROM user_data
                GROUP BY email
                HAVING count > 1
            ")->result();

            if (empty($duplicates)) {
                $this->db->query('ALTER TABLE `user_data` ADD UNIQUE KEY `uk_email` (`email`)');
                echo "✓ Email unique constraint added\n";
            } else {
                echo "⚠ Warning: Duplicate emails found, skipping unique constraint. Please clean data first:\n";
                foreach ($duplicates as $dup) {
                    echo "  - {$dup->email} ({$dup->count} times)\n";
                }
            }
        }

        echo "✓ User data table updated with customer fields\n";

        // 4. Update existing active categories with sort order
        $this->db->query("UPDATE categories SET sort_order = id * 10 WHERE status = 1");
        echo "✓ Existing categories updated with sort order\n";

        echo "\n";
        echo "═══════════════════════════════════════════════════\n";
        echo "Migration 004 completed successfully!\n";
        echo "═══════════════════════════════════════════════════\n";
        echo "Summary:\n";
        echo "  • Customer role created (id=3)\n";
        echo "  • Categories enhanced: sort_order, is_featured, banner, SEO\n";
        echo "  • User data enhanced: address, wishlist, last_login, email_verified\n";
        echo "  • Indexes added for performance\n";
        echo "═══════════════════════════════════════════════════\n\n";
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        // Remove Customer role
        $this->db->delete('user_role', ['id' => 3]);
        echo "✓ Customer role removed\n";

        // Revert categories changes
        $this->db->query("ALTER TABLE `categories`
            DROP COLUMN `sort_order`,
            DROP COLUMN `is_featured`,
            DROP COLUMN `banner_image`,
            DROP COLUMN `meta_title`,
            DROP COLUMN `meta_description`,
            DROP KEY `idx_featured`,
            DROP KEY `idx_sort`");

        echo "✓ Categories table reverted\n";

        // Revert user_data changes
        $this->db->query("ALTER TABLE `user_data`
            DROP COLUMN `default_address`,
            DROP COLUMN `wishlist`,
            DROP COLUMN `last_login_at`,
            DROP COLUMN `email_verified`,
            DROP KEY `idx_role`,
            DROP KEY `idx_email`");

        // Drop unique email constraint if exists
        $check_unique = $this->db->query("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
            AND table_name = 'user_data'
            AND index_name = 'uk_email'
        ")->row();

        if ($check_unique->count > 0) {
            $this->db->query('ALTER TABLE `user_data` DROP KEY `uk_email`');
        }

        echo "✓ User data table reverted\n";
        echo "✓ Migration 004 rolled back successfully\n";
    }
}
