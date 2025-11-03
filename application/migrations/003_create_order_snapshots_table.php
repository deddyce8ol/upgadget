<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create Order Snapshots Table
 *
 * Creates table to track WhatsApp order submissions for analytics and customer service.
 * This is NOT a full order management system - just snapshot for tracking.
 *
 * @package Putra Elektronik
 * @category Migrations
 * @version 1.0.0
 */
class Migration_Create_order_snapshots_table extends CI_Migration
{
    /**
     * Run migration - Create order_snapshots table
     */
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'order_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Format: ORD-YYYYMMDD-XXXXX'
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'comment' => 'NULL if guest checkout'
            ],
            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'customer_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'customer_address' => [
                'type' => 'TEXT',
            ],
            'customer_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'items' => [
                'type' => 'TEXT',
                'comment' => 'JSON: [{"id":1,"name":"Product","qty":2,"price":1000}]'
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'shipping_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Future: calculated shipping'
            ],
            'grand_total' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'whatsapp_message' => [
                'type' => 'TEXT',
                'comment' => 'The exact message sent to WhatsApp'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'sent', 'cancelled'],
                'default' => 'sent',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Internal notes from admin'
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => TRUE,
                'comment' => 'Customer IP for fraud detection'
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'comment' => 'Browser info'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('order_snapshots', TRUE);

        // Add unique key for order number
        $this->db->query('ALTER TABLE `order_snapshots` ADD UNIQUE KEY `uk_order_number` (`order_number`)');

        // Add indexes
        $this->db->query('ALTER TABLE `order_snapshots` ADD KEY `idx_user` (`user_id`)');
        $this->db->query('ALTER TABLE `order_snapshots` ADD KEY `idx_phone` (`customer_phone`)');
        $this->db->query('ALTER TABLE `order_snapshots` ADD KEY `idx_created` (`created_at`)');
        $this->db->query('ALTER TABLE `order_snapshots` ADD KEY `idx_status` (`status`)');

        // Set default timestamps
        $this->db->query('ALTER TABLE `order_snapshots`
            MODIFY `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->db->query('ALTER TABLE `order_snapshots`
            MODIFY `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

        echo "✓ Order snapshots table created successfully\n";
    }

    /**
     * Rollback migration - Drop order_snapshots table
     */
    public function down()
    {
        $this->dbforge->drop_table('order_snapshots', TRUE);
        echo "✓ Order snapshots table dropped\n";
    }
}
