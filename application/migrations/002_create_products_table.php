<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create Products Table
 *
 * Creates the products table for e-commerce functionality with full specifications
 * including SKU, pricing, stock management, images, and SEO fields.
 *
 * @package Putra Elektronik
 * @category Migrations
 * @version 1.0.0
 */
class Migration_Create_products_table extends CI_Migration
{
    /**
     * Run migration - Create products table
     */
    public function up()
    {
        // Create products table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'comment' => 'Foreign key to categories table'
            ],
            'sku' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Stock Keeping Unit - unique identifier'
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Product name'
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'SEO-friendly URL slug'
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Product description (HTML allowed)'
            ],
            'specifications' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'JSON format: {"brand": "Samsung", "warranty": "1 year"}'
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Selling price'
            ],
            'compare_price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => TRUE,
                'comment' => 'Original price for discount display'
            ],
            'cost_price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => TRUE,
                'comment' => 'Internal cost (admin only, hidden from public)'
            ],
            'stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Available quantity'
            ],
            'low_stock_threshold' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 10,
                'comment' => 'Alert when stock below this number'
            ],
            'images' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'JSON array: ["uploads/products/img1.jpg", "img2.jpg"]'
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1=visible in shop, 0=hidden'
            ],
            'is_featured' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1=show on homepage featured section'
            ],
            'view_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Track product popularity'
            ],
            'meta_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'comment' => 'SEO title'
            ],
            'meta_description' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE,
                'comment' => 'SEO description'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
                'comment' => 'Soft delete timestamp'
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('products', TRUE);

        // Add unique keys
        $this->db->query('ALTER TABLE `products` ADD UNIQUE KEY `uk_sku` (`sku`)');
        $this->db->query('ALTER TABLE `products` ADD UNIQUE KEY `uk_slug` (`slug`)');

        // Add indexes for performance
        $this->db->query('ALTER TABLE `products` ADD KEY `idx_category` (`category_id`)');
        $this->db->query('ALTER TABLE `products` ADD KEY `idx_active_featured` (`is_active`, `is_featured`)');
        $this->db->query('ALTER TABLE `products` ADD KEY `idx_stock` (`stock`)');
        $this->db->query('ALTER TABLE `products` ADD KEY `idx_created` (`created_at`)');

        // Add foreign key constraint
        $this->db->query('ALTER TABLE `products`
            ADD CONSTRAINT `fk_product_category`
            FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
            ON DELETE RESTRICT ON UPDATE CASCADE');

        // Set default timestamps
        $this->db->query('ALTER TABLE `products`
            MODIFY `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->db->query('ALTER TABLE `products`
            MODIFY `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

        echo "✓ Products table created successfully with indexes and foreign keys\n";
    }

    /**
     * Rollback migration - Drop products table
     */
    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE `products` DROP FOREIGN KEY `fk_product_category`');

        // Drop table
        $this->dbforge->drop_table('products', TRUE);

        echo "✓ Products table dropped\n";
    }
}
