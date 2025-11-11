<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create Product Import Temporary Table
 *
 * Temporary table to store preview data before actual import
 */
class Migration_Create_product_import_temp extends CI_Migration {

    public function up()
    {
        // Create temporary import table
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ],
            'session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => FALSE,
            ],
            'row_number' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => FALSE,
            ],
            'sku' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => TRUE,
            ],
            'product_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
            ],
            'category_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => TRUE,
            ],
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => TRUE,
            ],
            'brand_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => TRUE,
            ],
            'brand_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => TRUE,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'stock' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'product_slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['NEW', 'UPDATE', 'ERROR'],
                'default'    => 'NEW',
            ],
            'validation_errors' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'is_valid' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('session_id');
        $this->dbforge->create_table('product_import_temp', TRUE);

        echo "Table 'product_import_temp' created successfully.\n";
    }

    public function down()
    {
        $this->dbforge->drop_table('product_import_temp', TRUE);
        echo "Table 'product_import_temp' dropped successfully.\n";
    }
}
