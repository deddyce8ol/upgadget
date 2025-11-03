<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Format currency to Indonesian Rupiah
 *
 * @param float $amount
 * @param bool $with_symbol
 * @return string
 */
if (!function_exists('format_rupiah')) {
    function format_rupiah($amount, $with_symbol = true)
    {
        $formatted = number_format($amount, 0, ',', '.');
        return $with_symbol ? 'Rp ' . $formatted : $formatted;
    }
}

/**
 * Calculate discount percentage
 *
 * @param float $original_price
 * @param float $discount_price
 * @return int
 */
if (!function_exists('calculate_discount_percentage')) {
    function calculate_discount_percentage($original_price, $discount_price)
    {
        if ($original_price <= 0) return 0;
        $discount = (($original_price - $discount_price) / $original_price) * 100;
        return round($discount);
    }
}

/**
 * Get final price (with discount if available)
 *
 * @param object $product
 * @return float
 */
if (!function_exists('get_final_price')) {
    function get_final_price($product)
    {
        if (isset($product->discount_price) && $product->discount_price > 0 && $product->discount_price < $product->price) {
            return $product->discount_price;
        }
        return $product->price;
    }
}

/**
 * Check if product has discount
 *
 * @param object $product
 * @return bool
 */
if (!function_exists('has_discount')) {
    function has_discount($product)
    {
        return isset($product->discount_price) && $product->discount_price > 0 && $product->discount_price < $product->price;
    }
}

/**
 * Generate WhatsApp message for order
 *
 * @param object $order
 * @param array $order_items
 * @return string
 */
if (!function_exists('generate_whatsapp_message')) {
    function generate_whatsapp_message($order, $order_items)
    {
        $message = "*PEMESANAN BARU*\n\n";
        $message .= "No. Pesanan: *{$order->order_number}*\n";
        $message .= "Tanggal: " . date('d/m/Y H:i', strtotime($order->created_at)) . "\n\n";

        $message .= "*DATA PELANGGAN*\n";
        $message .= "Nama: {$order->customer_name}\n";
        $message .= "No. HP: {$order->customer_phone}\n";
        if (!empty($order->customer_email)) {
            $message .= "Email: {$order->customer_email}\n";
        }
        $message .= "\n";

        $message .= "*ALAMAT PENGIRIMAN*\n";
        $message .= "{$order->shipping_address}\n";
        $message .= "{$order->shipping_city}, {$order->shipping_province}\n";
        if (!empty($order->shipping_postal_code)) {
            $message .= "Kode Pos: {$order->shipping_postal_code}\n";
        }
        $message .= "\n";

        $message .= "*DETAIL PESANAN*\n";
        foreach ($order_items as $item) {
            $message .= "• {$item->product_name}\n";
            $message .= "  " . format_rupiah($item->price) . " x {$item->quantity} = " . format_rupiah($item->subtotal) . "\n";
        }
        $message .= "\n";

        $message .= "*RINGKASAN*\n";
        $message .= "Subtotal: " . format_rupiah($order->subtotal) . "\n";
        $message .= "Ongkir: " . format_rupiah($order->shipping_cost) . "\n";
        $message .= "─────────────────\n";
        $message .= "*TOTAL: " . format_rupiah($order->total_amount) . "*\n";

        if (!empty($order->notes)) {
            $message .= "\n*CATATAN*\n";
            $message .= $order->notes . "\n";
        }

        return $message;
    }
}

/**
 * Generate WhatsApp order URL
 *
 * @param string $phone
 * @param string $message
 * @return string
 */
if (!function_exists('generate_whatsapp_url')) {
    function generate_whatsapp_url($phone, $message)
    {
        // Remove any non-digit characters from phone
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure phone starts with 62 (Indonesia country code)
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        $encoded_message = urlencode($message);
        return "https://wa.me/{$phone}?text={$encoded_message}";
    }
}

/**
 * Get product image URL
 *
 * @param string $image_path
 * @param string $default
 * @return string
 */
if (!function_exists('get_product_image')) {
    function get_product_image($image_path, $default = 'assets/img/no-image.jpg')
    {
        if (empty($image_path)) {
            return base_url($default);
        }

        // Check if image file exists
        $full_path = FCPATH . 'uploads/products/' . $image_path;
        if (!file_exists($full_path)) {
            return base_url($default);
        }

        return base_url('uploads/products/' . $image_path);
    }
}

/**
 * Get brand logo URL
 *
 * @param string $logo_path
 * @param string $default
 * @return string
 */
if (!function_exists('get_brand_logo')) {
    function get_brand_logo($logo_path, $default = 'assets/img/no-logo.jpg')
    {
        if (empty($logo_path)) {
            return base_url($default);
        }
        return base_url('uploads/brands/' . $logo_path);
    }
}

/**
 * Get banner image URL
 *
 * @param string $image_path
 * @param string $default
 * @return string
 */
if (!function_exists('get_banner_image')) {
    function get_banner_image($image_path, $default = 'assets/img/no-banner.jpg')
    {
        if (empty($image_path)) {
            return base_url($default);
        }
        return base_url('uploads/banners/' . $image_path);
    }
}

/**
 * Get order status badge
 *
 * @param string $status
 * @return string
 */
if (!function_exists('get_order_status_badge')) {
    function get_order_status_badge($status)
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'confirmed' => '<span class="badge bg-info">Dikonfirmasi</span>',
            'processing' => '<span class="badge bg-primary">Diproses</span>',
            'shipped' => '<span class="badge bg-secondary">Dikirim</span>',
            'delivered' => '<span class="badge bg-success">Selesai</span>',
            'cancelled' => '<span class="badge bg-danger">Dibatalkan</span>',
        ];
        return $badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }
}

/**
 * Get payment status badge
 *
 * @param string $status
 * @return string
 */
if (!function_exists('get_payment_status_badge')) {
    function get_payment_status_badge($status)
    {
        $badges = [
            'unpaid' => '<span class="badge bg-warning">Belum Bayar</span>',
            'paid' => '<span class="badge bg-success">Sudah Bayar</span>',
            'refunded' => '<span class="badge bg-danger">Refund</span>',
        ];
        return $badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }
}

/**
 * Get stock status
 *
 * @param int $stock
 * @return string
 */
if (!function_exists('get_stock_status')) {
    function get_stock_status($stock)
    {
        if ($stock <= 0) {
            return '<span class="badge bg-danger">Habis</span>';
        } elseif ($stock < 5) {
            return '<span class="badge bg-warning">Sisa ' . $stock . '</span>';
        } else {
            return '<span class="badge bg-success">Tersedia</span>';
        }
    }
}

/**
 * Truncate text with ellipsis
 *
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
if (!function_exists('truncate_text')) {
    function truncate_text($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $suffix;
    }
}

/**
 * Generate star rating HTML
 *
 * @param float $rating
 * @param int $max
 * @return string
 */
if (!function_exists('generate_star_rating')) {
    function generate_star_rating($rating, $max = 5)
    {
        $html = '';
        for ($i = 1; $i <= $max; $i++) {
            if ($i <= $rating) {
                $html .= '<i class="fas fa-star text-warning"></i>';
            } elseif ($i - 0.5 <= $rating) {
                $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
            } else {
                $html .= '<i class="far fa-star text-warning"></i>';
            }
        }
        return $html;
    }
}
