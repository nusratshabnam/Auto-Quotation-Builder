<?php
/**
 * Plugin Name: Service Quote Builder
 * Plugin URI: https://example.com/service-quote-builder
 * Description: A multi-step service configuration and quote builder for automotive services with dynamic pricing.
 * Version: 1.0.0
 * Author: MiniMax Agent
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: service-quote-builder
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SQB_VERSION', '1.0.0');
define('SQB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SQB_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Plugin Class
 */
class Service_Quote_Builder {

    /**
     * Initialize the plugin
     */
    public function __construct() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Shortcode for frontend display
        add_shortcode('service_quote_builder', array($this, 'render_quote_builder'));

        // AJAX handlers
        add_action('wp_ajax_save_quote', array($this, 'handle_save_quote'));
        add_action('wp_ajax_nopriv_save_quote', array($this, 'handle_save_quote'));
        add_action('wp_ajax_submit_quote_request', array($this, 'handle_quote_submission'));
        add_action('wp_ajax_nopriv_submit_quote_request', array($this, 'handle_quote_submission'));

        // Admin AJAX handlers
        add_action('wp_ajax_sqb_get_quote', array($this, 'ajax_get_quote'));
        add_action('wp_ajax_sqb_change_status', array($this, 'ajax_change_status'));
        add_action('wp_ajax_sqb_delete_quote', array($this, 'ajax_delete_quote'));

        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Load plugin textdomain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'service-quote-builder',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        global $post;

        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'service_quote_builder')) {
            wp_enqueue_style(
                'sqb-frontend',
                SQB_PLUGIN_URL . 'assets/css/frontend.css',
                array(),
                SQB_VERSION
            );

            wp_enqueue_script(
                'sqb-frontend',
                SQB_PLUGIN_URL . 'assets/js/frontend.js',
                array('jquery'),
                SQB_VERSION,
                true
            );

            wp_localize_script('sqb-frontend', 'sqb_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('sqb_nonce'),
                'currency_symbol' => get_option('sqb_currency_symbol', '$'),
                'currency_position' => get_option('sqb_currency_position', 'before'),
            ));
        }
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ($hook === 'toplevel_page_service-quote-builder') {
            wp_enqueue_style(
                'sqb-admin',
                SQB_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                SQB_VERSION
            );

            wp_enqueue_script(
                'sqb-admin',
                SQB_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                SQB_VERSION,
                true
            );
        }
    }

    /**
     * Render the quote builder shortcode
     */
    public function render_quote_builder($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Service Quote Builder',
        ), $atts, 'service_quote_builder');

        ob_start();
        include SQB_PLUGIN_DIR . 'templates/quote-builder.php';
        return ob_get_clean();
    }

    /**
     * Handle quote save via AJAX
     */
    public function handle_save_quote() {
        check_ajax_referer('sqb_nonce', 'nonce');

        $quote_data = isset($_POST['quote_data']) ? sanitize_text_field(wp_json_encode($_POST['quote_data'])) : '';
        $customer_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

        if (empty($quote_data)) {
            wp_send_json_error(array('message' => 'No quote data provided'));
        }

        // Save to database
        global $wpdb;
        $table_name = $wpdb->prefix . 'sqb_quotes';

        $result = $wpdb->insert($table_name, array(
            'quote_data' => $quote_data,
            'customer_email' => $customer_email,
            'created_at' => current_time('mysql'),
        ));

        if ($result) {
            $quote_id = $wpdb->insert_id;
            $share_code = strtoupper(substr(md5($quote_id . time()), 0, 8));

            $wpdb->update(
                $table_name,
                array('share_code' => $share_code),
                array('id' => $quote_id)
            );

            wp_send_json_success(array(
                'quote_id' => $quote_id,
                'share_code' => $share_code,
                'message' => 'Quote saved successfully!'
            ));
        } else {
            wp_send_json_error(array('message' => 'Failed to save quote'));
        }

        wp_die();
    }

    /**
     * Handle quote submission via AJAX
     */
    public function handle_quote_submission() {
        check_ajax_referer('sqb_nonce', 'nonce');

        $quote_data = isset($_POST['quote_data']) ? $_POST['quote_data'] : array();
        $customer_info = isset($_POST['customer_info']) ? $_POST['customer_info'] : array();

        // Sanitize customer info
        $sanitized_info = array(
            'name' => sanitize_text_field($customer_info['name'] ?? ''),
            'email' => sanitize_email($customer_info['email'] ?? ''),
            'phone' => sanitize_text_field($customer_info['phone'] ?? ''),
            'make' => sanitize_text_field($customer_info['make'] ?? ''),
            'model' => sanitize_text_field($customer_info['model'] ?? ''),
            'preferred_date' => sanitize_text_field($customer_info['preferred_date'] ?? ''),
            'address' => sanitize_textarea_field($customer_info['address'] ?? ''),
            'country' => sanitize_text_field($customer_info['country'] ?? ''),
        );

        // Save to database
        global $wpdb;
        $table_name = $wpdb->prefix . 'sqb_quotes';

        $result = $wpdb->insert($table_name, array(
            'quote_data' => wp_json_encode($quote_data),
            'customer_email' => $sanitized_info['email'],
            'customer_name' => $sanitized_info['name'],
            'customer_phone' => $sanitized_info['phone'],
            'vehicle_make' => $sanitized_info['make'],
            'vehicle_model' => $sanitized_info['model'],
            'preferred_date' => $sanitized_info['preferred_date'],
            'address' => $sanitized_info['address'],
            'country' => $sanitized_info['country'],
            'status' => 'pending',
            'created_at' => current_time('mysql'),
        ));

        if ($result) {
            // Send notification email to admin
            $admin_email = get_option('admin_email');
            $subject = sprintf('New Service Quote Request from %s', $sanitized_info['name']);
            $message = $this->generate_email_body($sanitized_info, $quote_data);

            wp_mail($admin_email, $subject, $message);

            // Send confirmation email to customer
            $customer_subject = 'Your Service Quote Request - Received';
            $customer_message = $this->generate_customer_email_body($sanitized_info);
            wp_mail($sanitized_info['email'], $customer_subject, $customer_message);

            wp_send_json_success(array(
                'message' => 'Quote request submitted successfully! We will contact you shortly.',
            ));
        } else {
            wp_send_json_error(array('message' => 'Failed to submit quote request'));
        }

        wp_die();
    }

    /**
     * Generate admin notification email body
     */
    private function generate_email_body($customer, $quote_data) {
        $total = 0;
        foreach ($quote_data['items'] ?? array() as $item) {
            $total += floatval($item['price'] ?? 0);
        }

        $currency = get_option('sqb_currency_symbol', '$');
        $currency_pos = get_option('sqb_currency_position', 'before');

        if ($currency_pos === 'after') {
            $total_formatted = number_format($total, 2) . $currency;
        } else {
            $total_formatted = $currency . number_format($total, 2);
        }

        $body = "New Service Quote Request\n\n";
        $body .= "=====================================\n\n";
        $body .= "Customer Information:\n";
        $body .= "Name: {$customer['name']}\n";
        $body .= "Email: {$customer['email']}\n";
        $body .= "Phone: {$customer['phone']}\n";
        $body .= "Vehicle: {$customer['make']} {$customer['model']}\n";
        $body .= "Preferred Date: {$customer['preferred_date']}\n";
        $body .= "Address: {$customer['address']}\n";
        $body .= "Country: {$customer['country']}\n\n";
        $body .= "=====================================\n\n";
        $body .= "Quote Details:\n";

        foreach ($quote_data['items'] ?? array() as $item) {
            $body .= "- {$item['name']}: ";
            if ($currency_pos === 'after') {
                $body .= number_format(floatval($item['price']), 2) . $currency;
            } else {
                $body .= $currency . number_format(floatval($item['price']), 2);
            }
            $body .= "\n";
        }

        $body .= "\n=====================================\n";
        $body .= "Total: {$total_formatted}\n";

        return $body;
    }

    /**
     * Generate customer confirmation email body
     */
    private function generate_customer_email_body($customer) {
        $body = "Dear {$customer['name']},\n\n";
        $body .= "Thank you for your service quote request!\n\n";
        $body .= "We have received your request and will review it shortly. Our team will contact you within 24-48 hours to discuss your requirements and provide a detailed quote.\n\n";
        $body .= "Vehicle: {$customer['make']} {$customer['model']}\n";
        $body .= "Preferred Date: {$customer['preferred_date']}\n\n";
        $body .= "If you have any questions, please don't hesitate to contact us.\n\n";
        $body .= "Best regards,\n";
        $body .= "Service Team\n\n";
        $body .= "Note: All estimates require a visual inspection of your vehicle before a final quote can be made.";

        return $body;
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            'Service Quote Builder',
            'Quote Builder',
            'manage_options',
            'service-quote-builder',
            array($this, 'render_admin_page'),
            'dashicons-cart',
            30
        );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        include SQB_PLUGIN_DIR . 'templates/admin-page.php';
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('sqb_settings', 'sqb_currency_symbol');
        register_setting('sqb_settings', 'sqb_currency_position');
        register_setting('sqb_settings', 'sqb_vat_rate');
        register_setting('sqb_settings', 'sqb_email_subject');
        register_setting('sqb_settings', 'sqb_notification_email');

        add_settings_section(
            'sqb_general_settings',
            'General Settings',
            array($this, 'render_settings_section'),
            'service-quote-builder'
        );

        add_settings_field(
            'sqb_currency_symbol',
            'Currency Symbol',
            array($this, 'render_currency_symbol_field'),
            'service-quote-builder',
            'sqb_general_settings'
        );

        add_settings_field(
            'sqb_currency_position',
            'Currency Position',
            array($this, 'render_currency_position_field'),
            'service-quote-builder',
            'sqb_general_settings'
        );

        add_settings_field(
            'sqb_vat_rate',
            'VAT Rate (%)',
            array($this, 'render_vat_rate_field'),
            'service-quote-builder',
            'sqb_general_settings'
        );

        add_settings_field(
            'sqb_notification_email',
            'Notification Email',
            array($this, 'render_notification_email_field'),
            'service-quote-builder',
            'sqb_general_settings'
        );
    }

    /**
     * Render settings section
     */
    public function render_settings_section() {
        echo '<p>Configure the service quote builder settings below.</p>';
    }

    /**
     * Render currency symbol field
     */
    public function render_currency_symbol_field() {
        $value = get_option('sqb_currency_symbol', '$');
        echo '<input type="text" name="sqb_currency_symbol" value="' . esc_attr($value) . '" class="regular-text" maxlength="5" />';
        echo '<p class="description">Enter the currency symbol (e.g., $, €, £, ¥)</p>';
    }

    /**
     * Render currency position field
     */
    public function render_currency_position_field() {
        $value = get_option('sqb_currency_position', 'before');
        echo '<select name="sqb_currency_position">';
        echo '<option value="before" ' . selected($value, 'before', false) . '>Before amount ($100)</option>';
        echo '<option value="after" ' . selected($value, 'after', false) . '>After amount (100$)</option>';
        echo '</select>';
        echo '<p class="description">Choose where to display the currency symbol.</p>';
    }

    /**
     * Render VAT rate field
     */
    public function render_vat_rate_field() {
        $value = get_option('sqb_vat_rate', '0');
        echo '<input type="number" name="sqb_vat_rate" value="' . esc_attr($value) . '" class="regular-text" min="0" max="100" step="0.01" />';
        echo '<p class="description">Enter VAT rate as percentage (e.g., 20 for 20%).</p>';
    }

    /**
     * Render notification email field
     */
    public function render_notification_email_field() {
        $value = get_option('sqb_notification_email', get_option('admin_email'));
        echo '<input type="email" name="sqb_notification_email" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">Email address to receive quote notifications.</p>';
    }

    /**
     * Create database tables on activation
     */
    public static function activate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'sqb_quotes';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            quote_data longtext NOT NULL,
            customer_name varchar(255) DEFAULT '',
            customer_email varchar(255) DEFAULT '',
            customer_phone varchar(50) DEFAULT '',
            vehicle_make varchar(100) DEFAULT '',
            vehicle_model varchar(100) DEFAULT '',
            preferred_date date DEFAULT NULL,
            address text,
            country varchar(100) DEFAULT '',
            share_code varchar(20) DEFAULT '',
            status varchar(50) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY share_code (share_code),
            KEY customer_email (customer_email)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Set default options
        add_option('sqb_currency_symbol', '$');
        add_option('sqb_currency_position', 'before');
        add_option('sqb_vat_rate', '0');

        // Clear any cached data
        wp_cache_flush();
    }

    /**
     * AJAX: Get quote details
     */
    public function ajax_get_quote() {
        check_ajax_referer('sqb_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $quote_id = isset($_POST['quote_id']) ? intval($_POST['quote_id']) : 0;

        global $wpdb;
        $table_name = $wpdb->prefix . 'sqb_quotes';

        $quote = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $quote_id), ARRAY_A);

        if (!$quote) {
            wp_send_json_error(array('message' => 'Quote not found'));
        }

        $quote_data = json_decode($quote['quote_data'], true);
        $currency = get_option('sqb_currency_symbol', '$');
        $currency_pos = get_option('sqb_currency_position', 'before');

        $html = '<div class="sqb-quote-section">';
        $html .= '<h4>' . __('Customer Information', 'service-quote-builder') . '</h4>';
        $html .= '<p><strong>' . __('Name', 'service-quote-builder') . ':</strong> ' . esc_html($quote['customer_name'] ?: 'N/A') . '</p>';
        $html .= '<p><strong>' . __('Email', 'service-quote-builder') . ':</strong> ' . esc_html($quote['customer_email']) . '</p>';
        $html .= '<p><strong>' . __('Phone', 'service-quote-builder') . ':</strong> ' . esc_html($quote['customer_phone'] ?: 'N/A') . '</p>';
        $html .= '<p><strong>' . __('Vehicle', 'service-quote-builder') . ':</strong> ' . esc_html($quote['vehicle_make'] . ' ' . $quote['vehicle_model']) . '</p>';
        $html .= '<p><strong>' . __('Preferred Date', 'service-quote-builder') . ':</strong> ' . esc_html($quote['preferred_date'] ?: 'N/A') . '</p>';
        $html .= '<p><strong>' . __('Country', 'service-quote-builder') . ':</strong> ' . esc_html($quote['country'] ?: 'N/A') . '</p>';
        $html .= '<p><strong>' . __('Address', 'service-quote-builder') . ':</strong> ' . nl2br(esc_html($quote['address'] ?: 'N/A')) . '</p>';
        $html .= '</div>';

        $html .= '<div class="sqb-quote-section">';
        $html .= '<h4>' . __('Quote Items', 'service-quote-builder') . '</h4>';

        if (!empty($quote_data['items'])) {
            $html .= '<ul class="sqb-quote-items">';
            $total = 0;
            foreach ($quote_data['items'] as $item) {
                $price = floatval($item['price'] ?? 0);
                $total += $price;
                $price_display = $currency_pos === 'after' ? number_format($price, 2) . $currency : $currency . number_format($price, 2);
                $html .= '<li><span>' . esc_html($item['name']) . '</span><strong>' . $price_display . '</strong></li>';
            }
            $total_display = $currency_pos === 'after' ? number_format($total, 2) . $currency : $currency . number_format($total, 2);
            $html .= '<li style="border-top: 2px solid #e2e8f0; margin-top: 10px; padding-top: 10px;"><strong>' . __('Total', 'service-quote-builder') . '</strong><strong style="color: #2563eb;">' . $total_display . '</strong></li>';
            $html .= '</ul>';
        } else {
            $html .= '<p>' . __('No items in quote', 'service-quote-builder') . '</p>';
        }
        $html .= '</div>';

        $html .= '<div class="sqb-quote-section">';
        $html .= '<h4>' . __('Quote Info', 'service-quote-builder') . '</h4>';
        $html .= '<p><strong>' . __('Share Code', 'service-quote-builder') . ':</strong> ' . esc_html($quote['share_code'] ?: 'N/A') . '</p>';
        $html .= '<p><strong>' . __('Status', 'service-quote-builder') . ':</strong> <span class="sqb-status sqb-status-' . esc_attr($quote['status']) . '">' . ucfirst($quote['status']) . '</span></p>';
        $html .= '<p><strong>' . __('Submitted', 'service-quote-builder') . ':</strong> ' . esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($quote['created_at']))) . '</p>';
        $html .= '</div>';

        $html .= '<div class="sqb-quote-section" style="border-top: 1px solid #f1f5f9; margin-top: 15px; padding-top: 15px;">';
        $html .= '<p style="font-size: 12px; color: #94a3b8;">' . __('Note: All estimates require a visual inspection of your vehicle before a final quote can be made.', 'service-quote-builder') . '</p>';
        $html .= '</div>';

        wp_send_json_success(array('html' => $html));
        wp_die();
    }

    /**
     * AJAX: Change quote status
     */
    public function ajax_change_status() {
        check_ajax_referer('sqb_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $quote_id = isset($_POST['quote_id']) ? intval($_POST['quote_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

        if (!in_array($status, array('pending', 'completed', 'cancelled'))) {
            wp_send_json_error(array('message' => 'Invalid status'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'sqb_quotes';

        $result = $wpdb->update($table_name, array('status' => $status), array('id' => $quote_id));

        if ($result !== false) {
            wp_send_json_success(array('message' => 'Status updated successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update status'));
        }

        wp_die();
    }

    /**
     * AJAX: Delete quote
     */
    public function ajax_delete_quote() {
        check_ajax_referer('sqb_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $quote_id = isset($_POST['quote_id']) ? intval($_POST['quote_id']) : 0;

        global $wpdb;
        $table_name = $wpdb->prefix . 'sqb_quotes';

        $result = $wpdb->delete($table_name, array('id' => $quote_id));

        if ($result) {
            wp_send_json_success(array('message' => 'Quote deleted successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to delete quote'));
        }

        wp_die();
    }

    /**
     * Drop database tables on uninstall
     */
    public static function uninstall() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sqb_quotes';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        delete_option('sqb_currency_symbol');
        delete_option('sqb_currency_position');
        delete_option('sqb_vat_rate');
        delete_option('sqb_notification_email');
    }
}

// Initialize the plugin
new Service_Quote_Builder();

// Activation/Deactivation hooks
register_activation_hook(__FILE__, array('Service_Quote_Builder', 'activate'));
register_uninstall_hook(__FILE__, array('Service_Quote_Builder', 'uninstall'));