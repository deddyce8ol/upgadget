<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create Categories Table
 *
 * Creates the categories table with complete schema including soft delete support,
 * indexes for performance, and seeds 8 default product categories.
 *
 * @package Putra Elektronik
 * @category Migrations
 */
class Migration_Create_categories_table extends CI_Migration
{
    /**
     * Run migration (create table and seed data)
     *
     * @return void
     */
    public function up()
    {
        // Create categories table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE,
                'comment' => 'Category display name'
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => FALSE,
                'comment' => 'URL-friendly slug'
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'default' => NULL,
                'comment' => 'Category description for customers'
            ],
            'icon_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'default' => NULL,
                'comment' => 'Path to uploaded icon image'
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => FALSE,
                'default' => 1,
                'comment' => '1=Aktif (visible), 0=Tidak Aktif (hidden)'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => FALSE,
                'comment' => 'Record creation timestamp'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => FALSE,
                'comment' => 'Last modification timestamp'
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'default' => NULL,
                'comment' => 'Soft delete timestamp (NULL = active)'
            ]
        ]);

        $this->dbforge->add_key('id', TRUE); // Primary key
        $this->dbforge->create_table('categories', TRUE, ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // Add unique indexes for data integrity
        $this->db->query('CREATE UNIQUE INDEX idx_name ON categories(name)');
        $this->db->query('CREATE UNIQUE INDEX idx_slug ON categories(slug)');

        // Add regular indexes for performance
        $this->db->query('CREATE INDEX idx_status ON categories(status)');
        $this->db->query('CREATE INDEX idx_deleted_at ON categories(deleted_at)');

        // Seed 8 default categories
        $default_categories = [
            [
                'name' => 'Televisi',
                'slug' => 'televisi',
                'description' => 'Berbagai jenis TV dari LED hingga Smart TV',
                'icon_path' => NULL,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => NULL
            ],
            [
                'name' => 'Kulkas',
                'slug' => 'kulkas',
                'description' => 'Kulkas 1 pintu hingga side by side',
                'icon_path' => NULL,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => NULL
            ],
            [
                'name' => 'Mesin Cuci',
                'slug' => 'mesin-cuci',
                'description' => 'Mesin cuci otomatis dan semi otomatis',
                'icon_path' => NULL,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => NULL
            ],
            [
                'name' => 'AC (Air Conditioner)',
                'slug' => 'ac-air-conditioner',
                'description' => 'AC split dan AC window',
                'icon_path' => NULL,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => NULL
            ],
            [
                'name' => 'Audio & Speaker',
                'slug' => 'audio-speaker',
                'description' => 'Sound system, speaker bluetooth, home theater',
                'icon_path' => NULL,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => NULL
            ],
            [
                'name' => 'Laptop & Komputer',
                'slug' => 'laptop-komputer',
                'description' => 'Laptop, PC, dan aksesoris',
                'icon_path' => NULL,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => NULL
            ],
            [
                'name' => 'Handphone & Tablet',
                'slug' => 'handphone-tablet',
                'description' => 'Smartphone dan tablet berbagai merek',
                'icon_path' => NULL,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => NULL
            ],
            [
                'name' => 'Aksesoris',
                'slug' => 'aksesoris',
                'description' => 'Charger, case, powerbank, dll',
                'icon_path' => NULL,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => NULL
            ]
        ];

        $this->db->insert_batch('categories', $default_categories);

        echo "✅ Categories table created with 8 default categories\n";
    }

    /**
     * Rollback migration (drop table)
     *
     * @return void
     */
    public function down()
    {
        $this->dbforge->drop_table('categories', TRUE);
        echo "✅ Categories table dropped\n";
    }
}
