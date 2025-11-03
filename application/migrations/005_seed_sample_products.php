<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Seed Sample Products
 *
 * Seeds sample product data for testing and demo purposes.
 * Run this after migrations 002-004 are complete.
 *
 * @package Putra Elektronik
 * @category Migrations
 * @version 1.0.0
 */
class Migration_Seed_sample_products extends CI_Migration
{
    /**
     * Run migration - Seed sample products
     */
    public function up()
    {
        // Check if categories exist
        $categories_count = $this->db->count_all('categories');

        if ($categories_count < 2) {
            echo "⚠ Warning: Not enough categories found. Please create categories first.\n";
            echo "  Skipping sample product seeding.\n";
            return;
        }

        // Get first two categories
        $categories = $this->db->limit(2)->get('categories')->result_array();
        $cat1_id = $categories[0]['id'];
        $cat2_id = isset($categories[1]) ? $categories[1]['id'] : $cat1_id;

        echo "Using categories:\n";
        echo "  - {$categories[0]['name']} (ID: {$cat1_id})\n";
        if (isset($categories[1])) {
            echo "  - {$categories[1]['name']} (ID: {$cat2_id})\n";
        }
        echo "\n";

        // Sample products data
        $sample_products = [
            // Featured products (category 1)
            [
                'category_id' => $cat1_id,
                'sku' => 'KLK-SAM-001',
                'name' => 'Kulkas 2 Pintu Samsung RT38K5032S8',
                'slug' => 'kulkas-samsung-rt38k5032s8',
                'description' => '<p>Kulkas 2 pintu dengan teknologi Digital Inverter yang hemat energi. Kapasitas 384 liter dengan desain modern.</p><ul><li>Digital Inverter Technology</li><li>All Around Cooling</li><li>Deodorizer Filter</li><li>Garansi Kompresor 10 Tahun</li></ul>',
                'specifications' => json_encode([
                    'brand' => 'Samsung',
                    'type' => '2 Pintu',
                    'capacity' => '384 Liter',
                    'warranty' => '10 Tahun Kompresor',
                    'color' => 'Gentle Grey'
                ]),
                'price' => 4799000.00,
                'compare_price' => 5299000.00,
                'cost_price' => 4200000.00,
                'stock' => 12,
                'low_stock_threshold' => 5,
                'images' => json_encode(['product-kulkas-1.jpg', 'product-kulkas-2.jpg']),
                'is_active' => 1,
                'is_featured' => 1,
                'meta_title' => 'Kulkas Samsung RT38K5032S8 - 2 Pintu 384L Hemat Energi',
                'meta_description' => 'Beli Kulkas Samsung 2 Pintu RT38K5032S8 dengan Digital Inverter, All Around Cooling, kapasitas 384 liter. Garansi 10 tahun kompresor.',
            ],
            [
                'category_id' => $cat1_id,
                'sku' => 'TV-LG-43-001',
                'name' => 'Smart TV LG 43 Inch 43UM7290PTF 4K UHD',
                'slug' => 'smart-tv-lg-43-inch-4k',
                'description' => '<p>Smart TV LG 43 inch dengan resolusi 4K UHD, dilengkapi dengan AI ThinQ dan webOS untuk pengalaman menonton yang luar biasa.</p><ul><li>4K Ultra HD (3840 x 2160)</li><li>AI ThinQ & Google Assistant</li><li>webOS Smart TV</li><li>HDR 10 Pro</li></ul>',
                'specifications' => json_encode([
                    'brand' => 'LG',
                    'size' => '43 Inch',
                    'resolution' => '4K UHD 3840x2160',
                    'smart_tv' => 'webOS',
                    'warranty' => '2 Tahun'
                ]),
                'price' => 5299000.00,
                'compare_price' => null,
                'cost_price' => 4800000.00,
                'stock' => 8,
                'low_stock_threshold' => 5,
                'images' => json_encode(['product-tv-1.jpg', 'product-tv-2.jpg']),
                'is_active' => 1,
                'is_featured' => 1,
                'meta_title' => 'Smart TV LG 43 Inch 4K UHD - AI ThinQ webOS',
                'meta_description' => 'Smart TV LG 43UM7290PTF 4K UHD dengan AI ThinQ, Google Assistant, webOS, HDR 10 Pro. Harga terbaik, garansi resmi 2 tahun.',
            ],
            [
                'category_id' => $cat2_id,
                'sku' => 'AC-DAIKIN-1PK',
                'name' => 'AC Split Daikin 1 PK FTKC25TVM4 Inverter',
                'slug' => 'ac-daikin-1pk-inverter',
                'description' => '<p>AC Daikin 1 PK dengan teknologi Inverter yang hemat listrik. Dilengkapi dengan Streamer Technology untuk udara lebih sehat.</p><ul><li>Inverter Technology - Hemat Energi</li><li>Streamer Technology</li><li>Powerful Mode - Cepat Dingin</li><li>Comfort Mode</li></ul>',
                'specifications' => json_encode([
                    'brand' => 'Daikin',
                    'capacity' => '1 PK',
                    'power' => '840 Watt',
                    'refrigerant' => 'R32',
                    'warranty' => '1 Tahun Service + 5 Tahun Spare Part'
                ]),
                'price' => 4199000.00,
                'compare_price' => 4599000.00,
                'cost_price' => 3700000.00,
                'stock' => 5,
                'low_stock_threshold' => 3,
                'images' => json_encode(['product-ac-1.jpg']),
                'is_active' => 1,
                'is_featured' => 0,
                'meta_title' => 'AC Daikin 1 PK Inverter - Hemat Listrik Streamer',
                'meta_description' => 'AC Split Daikin FTKC25TVM4 1 PK dengan Inverter Technology hemat energi, Streamer untuk udara sehat. Garansi resmi.',
            ],
            [
                'category_id' => $cat1_id,
                'sku' => 'MS-SHARP-FL',
                'name' => 'Mesin Cuci Sharp ES-FL1272 Front Loading 7Kg',
                'slug' => 'mesin-cuci-sharp-front-loading',
                'description' => '<p>Mesin cuci front loading Sharp 7kg dengan teknologi Aquamagic untuk hasil cucian lebih bersih dan wangi.</p><ul><li>Kapasitas 7 Kg</li><li>Aquamagic Technology</li><li>14 Program Pencucian</li><li>Delay Timer</li></ul>',
                'specifications' => json_encode([
                    'brand' => 'Sharp',
                    'type' => 'Front Loading',
                    'capacity' => '7 Kg',
                    'warranty' => '2 Tahun Motor',
                    'programs' => 14
                ]),
                'price' => 3450000.00,
                'compare_price' => null,
                'cost_price' => 3100000.00,
                'stock' => 0, // Out of stock for testing
                'low_stock_threshold' => 5,
                'images' => json_encode(['product-mesin-cuci-1.jpg']),
                'is_active' => 1,
                'is_featured' => 0,
                'meta_title' => 'Mesin Cuci Sharp Front Loading 7Kg - Aquamagic',
                'meta_description' => 'Mesin Cuci Sharp ES-FL1272 Front Loading 7kg, Aquamagic Technology, 14 program pencucian, delay timer. Garansi 2 tahun motor.',
            ],
            [
                'category_id' => $cat1_id,
                'sku' => 'KLK-SHARP-SJ',
                'name' => 'Kulkas 1 Pintu Sharp SJ-X185MG-FB 172L',
                'slug' => 'kulkas-sharp-1-pintu-172l',
                'description' => '<p>Kulkas 1 pintu Sharp dengan teknologi J-Tech Inverter hemat energi. Kapasitas 172 liter cocok untuk keluarga kecil.</p><ul><li>J-Tech Inverter</li><li>Hybrid Cooling</li><li>Bottle Pocket</li><li>LED Lighting</li></ul>',
                'specifications' => json_encode([
                    'brand' => 'Sharp',
                    'type' => '1 Pintu',
                    'capacity' => '172 Liter',
                    'warranty' => '5 Tahun Kompresor',
                    'technology' => 'J-Tech Inverter'
                ]),
                'price' => 2299000.00,
                'compare_price' => null,
                'cost_price' => 2000000.00,
                'stock' => 15,
                'low_stock_threshold' => 5,
                'images' => json_encode(['product-kulkas-sharp-1.jpg']),
                'is_active' => 1,
                'is_featured' => 1,
                'meta_title' => 'Kulkas Sharp 1 Pintu 172L J-Tech Inverter Hemat Energi',
                'meta_description' => 'Kulkas Sharp SJ-X185MG-FB 1 Pintu 172L dengan J-Tech Inverter hemat listrik, Hybrid Cooling. Garansi 5 tahun kompresor.',
            ],
            [
                'category_id' => $cat2_id,
                'sku' => 'AC-SHARP-05',
                'name' => 'AC Sharp 1/2 PK AH-AP5SSY Low Watt Plasmacluster',
                'slug' => 'ac-sharp-05pk-plasmacluster',
                'description' => '<p>AC Sharp 1/2 PK low watt dengan teknologi Plasmacluster Ion untuk udara lebih sehat dan bebas bakteri.</p><ul><li>Low Watt - Hemat Energi</li><li>Plasmacluster Ion Technology</li><li>Jet Stream - Cepat Dingin</li><li>Anti Nyamuk</li></ul>',
                'specifications' => json_encode([
                    'brand' => 'Sharp',
                    'capacity' => '1/2 PK',
                    'power' => '320 Watt',
                    'technology' => 'Plasmacluster Ion',
                    'warranty' => '1 Tahun'
                ]),
                'price' => 2799000.00,
                'compare_price' => 3099000.00,
                'cost_price' => 2400000.00,
                'stock' => 10,
                'low_stock_threshold' => 5,
                'images' => json_encode(['product-ac-sharp-1.jpg']),
                'is_active' => 1,
                'is_featured' => 1,
                'meta_title' => 'AC Sharp 1/2 PK Low Watt Plasmacluster - Hemat Listrik',
                'meta_description' => 'AC Sharp AH-AP5SSY 1/2 PK Low Watt 320W dengan Plasmacluster Ion, Jet Stream cepat dingin, Anti Nyamuk. Hemat energi.',
            ],
            [
                'category_id' => $cat1_id,
                'sku' => 'TV-SAMSUNG-32',
                'name' => 'TV LED Samsung 32 Inch UA32T4001 HD Ready',
                'slug' => 'tv-samsung-32-inch-hd',
                'description' => '<p>TV LED Samsung 32 inch dengan teknologi Wide Color Enhancer untuk warna lebih hidup. Cocok untuk kamar tidur atau ruang keluarga kecil.</p><ul><li>HD Ready Resolution</li><li>Wide Color Enhancer</li><li>Clean View</li><li>ConnectShare USB</li></ul>',
                'specifications' => json_encode([
                    'brand' => 'Samsung',
                    'size' => '32 Inch',
                    'resolution' => 'HD Ready 1366x768',
                    'smart_tv' => 'No',
                    'warranty' => '1 Tahun Panel + 2 Tahun Spare Part'
                ]),
                'price' => 2199000.00,
                'compare_price' => null,
                'cost_price' => 1950000.00,
                'stock' => 20,
                'low_stock_threshold' => 10,
                'images' => json_encode(['product-tv-samsung-32-1.jpg']),
                'is_active' => 1,
                'is_featured' => 0,
                'meta_title' => 'TV LED Samsung 32 Inch HD Ready - Wide Color Enhancer',
                'meta_description' => 'TV LED Samsung UA32T4001 32 inch HD Ready dengan Wide Color Enhancer, Clean View, USB. Garansi resmi Samsung.',
            ],
            [
                'category_id' => $cat1_id,
                'sku' => 'DISP-MASPION-190',
                'name' => 'Dispenser Maspion EX-190 Galon Bawah',
                'slug' => 'dispenser-maspion-galon-bawah',
                'description' => '<p>Dispenser Maspion dengan desain galon bawah, dilengkapi Hot & Normal. Hemat tempat dan mudah digunakan.</p><ul><li>Galon Bawah - Hemat Tempat</li><li>Hot & Normal</li><li>Hemat Energi</li><li>Desain Modern</li></ul>',
                'specifications' => json_encode([
                    'brand' => 'Maspion',
                    'type' => 'Galon Bawah',
                    'features' => 'Hot & Normal',
                    'power' => '350 Watt',
                    'warranty' => '1 Tahun Service'
                ]),
                'price' => 599000.00,
                'compare_price' => null,
                'cost_price' => 500000.00,
                'stock' => 25,
                'low_stock_threshold' => 10,
                'images' => json_encode(['product-dispenser-1.jpg']),
                'is_active' => 1,
                'is_featured' => 0,
                'meta_title' => 'Dispenser Maspion Galon Bawah EX-190 Hot & Normal',
                'meta_description' => 'Dispenser Maspion EX-190 galon bawah, Hot & Normal, hemat energi 350W, desain modern. Garansi resmi 1 tahun.',
            ],
        ];

        // Insert products
        $inserted_count = 0;
        foreach ($sample_products as $product) {
            // Check if SKU already exists
            $exists = $this->db->get_where('products', ['sku' => $product['sku']])->num_rows();

            if ($exists == 0) {
                $product['created_at'] = date('Y-m-d H:i:s');
                $product['updated_at'] = date('Y-m-d H:i:s');

                $this->db->insert('products', $product);
                $inserted_count++;

                echo "✓ Added: {$product['name']} (SKU: {$product['sku']})\n";
            } else {
                echo "⊘ Skipped: {$product['name']} (SKU already exists)\n";
            }
        }

        echo "\n";
        echo "═══════════════════════════════════════════════════\n";
        echo "Sample products seeding completed!\n";
        echo "═══════════════════════════════════════════════════\n";
        echo "Summary:\n";
        echo "  • Total products inserted: {$inserted_count}\n";
        echo "  • Featured products: 4\n";
        echo "  • Out of stock (for testing): 1\n";
        echo "═══════════════════════════════════════════════════\n\n";
        echo "Note: Product images are placeholders. Upload actual images to:\n";
        echo "  assets/uploads/products/\n\n";
    }

    /**
     * Rollback migration - Remove sample products
     */
    public function down()
    {
        // Delete sample products by SKU
        $sample_skus = [
            'KLK-SAM-001',
            'TV-LG-43-001',
            'AC-DAIKIN-1PK',
            'MS-SHARP-FL',
            'KLK-SHARP-SJ',
            'AC-SHARP-05',
            'TV-SAMSUNG-32',
            'DISP-MASPION-190'
        ];

        $deleted_count = 0;
        foreach ($sample_skus as $sku) {
            $deleted = $this->db->delete('products', ['sku' => $sku]);
            if ($deleted) {
                $deleted_count++;
                echo "✓ Deleted product with SKU: {$sku}\n";
            }
        }

        echo "\n✓ Sample products removed ({$deleted_count} products)\n";
    }
}
