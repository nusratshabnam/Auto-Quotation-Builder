<?php
/**
 * Service Quote Builder - Admin Page Template
 */
if (!defined('ABSPATH')) {
    exit;
}

// Get quotes from database
global $wpdb;
$table_name = $wpdb->prefix . 'sqb_quotes';

// Get current tab
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'quotes';

// Handle bulk actions
if (isset($_POST['sqb_bulk_action']) && isset($_POST['sqb_quotes']) && current_user_can('manage_options')) {
    $action = sanitize_text_field($_POST['sqb_bulk_action']);
    $quote_ids = array_map('intval', $_POST['sqb_quotes']);

    if ($action === 'delete') {
        foreach ($quote_ids as $id) {
            $wpdb->delete($table_name, array('id' => $id));
        }
        echo '<div class="notice notice-success"><p>' . sprintf(__('%d quotes deleted.', 'service-quote-builder'), count($quote_ids)) . '</p></div>';
    } elseif ($action === 'mark_completed') {
        foreach ($quote_ids as $id) {
            $wpdb->update($table_name, array('status' => 'completed'), array('id' => $id));
        }
        echo '<div class="notice notice-success"><p>' . sprintf(__('%d quotes marked as completed.', 'service-quote-builder'), count($quote_ids)) . '</p></div>';
    }
}

// Get quotes for quotes tab
$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 20;
$offset = ($paged - 1) * $per_page;

$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$where_clause = '';
if ($status_filter) {
    $where_clause = $wpdb->prepare(" WHERE status = %s", $status_filter);
}

$total_quotes = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where_clause");
$total_pages = ceil($total_quotes / $per_page);

$quotes = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ),
    ARRAY_A
);

$currency_symbol = get_option('sqb_currency_symbol', '$');
$currency_position = get_option('sqb_currency_position', 'before');
?>

<div class="wrap sqb-admin">
    <h1><?php echo esc_html__('Service Quote Builder', 'service-quote-builder'); ?></h1>

    <h2 class="nav-tab-wrapper">
        <a href="?page=service-quote-builder&tab=quotes" class="nav-tab <?php echo $current_tab === 'quotes' ? 'nav-tab-active' : ''; ?>">
            <?php echo esc_html__('Quotes', 'service-quote-builder'); ?>
        </a>
        <a href="?page=service-quote-builder&tab=settings" class="nav-tab <?php echo $current_tab === 'settings' ? 'nav-tab-active' : ''; ?>">
            <?php echo esc_html__('Settings', 'service-quote-builder'); ?>
        </a>
        <a href="?page=service-quote-builder&tab=usage" class="nav-tab <?php echo $current_tab === 'usage' ? 'nav-tab-active' : ''; ?>">
            <?php echo esc_html__('How to Use', 'service-quote-builder'); ?>
        </a>
    </h2>

    <div class="sqb-admin-content">
        <?php if ($current_tab === 'quotes'): ?>
            <!-- Quotes Tab -->
            <div class="sqb-toolbar">
                <form method="get" style="display: inline-block;">
                    <input type="hidden" name="page" value="service-quote-builder">
                    <input type="hidden" name="tab" value="quotes">
                    <select name="status" onchange="this.form.submit()">
                        <option value=""><?php echo esc_html__('All Status', 'service-quote-builder'); ?></option>
                        <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php echo esc_html__('Pending', 'service-quote-builder'); ?></option>
                        <option value="completed" <?php selected($status_filter, 'completed'); ?>><?php echo esc_html__('Completed', 'service-quote-builder'); ?></option>
                        <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>><?php echo esc_html__('Cancelled', 'service-quote-builder'); ?></option>
                    </select>
                </form>
                <span class="sqb-count"><?php echo sprintf(__('%d quotes found', 'service-quote-builder'), $total_quotes); ?></span>
            </div>

            <form method="post" id="sqb-quotes-form">
                <div class="sqb-toolbar sqb-toolbar-top">
                    <select name="sqb_bulk_action">
                        <option value=""><?php echo esc_html__('Bulk Actions', 'service-quote-builder'); ?></option>
                        <option value="mark_completed"><?php echo esc_html__('Mark as Completed', 'service-quote-builder'); ?></option>
                        <option value="delete"><?php echo esc_html__('Delete', 'service-quote-builder'); ?></option>
                    </select>
                    <button type="submit" class="button action"><?php echo esc_html__('Apply', 'service-quote-builder'); ?></button>
                </div>

                <table class="wp-list-table widefat fixed striped sqb-quotes-table">
                    <thead>
                        <tr>
                            <th class="sqb-col-check"><input type="checkbox" id="sqb-select-all"></th>
                            <th><?php echo esc_html__('ID', 'service-quote-builder'); ?></th>
                            <th><?php echo esc_html__('Customer', 'service-quote-builder'); ?></th>
                            <th><?php echo esc_html__('Vehicle', 'service-quote-builder'); ?></th>
                            <th><?php echo esc_html__('Total', 'service-quote-builder'); ?></th>
                            <th><?php echo esc_html__('Status', 'service-quote-builder'); ?></th>
                            <th><?php echo esc_html__('Date', 'service-quote-builder'); ?></th>
                            <th><?php echo esc_html__('Actions', 'service-quote-builder'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quotes)): ?>
                            <tr>
                                <td colspan="8" class="sqb-empty"><?php echo esc_html__('No quotes found.', 'service-quote-builder'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($quotes as $quote): ?>
                                <?php
                                $quote_data = json_decode($quote['quote_data'], true);
                                $total = 0;
                                if (is_array($quote_data) && isset($quote_data['items'])) {
                                    foreach ($quote_data['items'] as $item) {
                                        $total += floatval($item['price'] ?? 0);
                                    }
                                }

                                $total_display = $currency_position === 'before'
                                    ? $currency_symbol . number_format($total, 2)
                                    : number_format($total, 2) . $currency_symbol;

                                $status_class = 'pending' === $quote['status'] ? 'sqb-status-pending' : ('completed' === $quote['status'] ? 'sqb-status-completed' : 'sqb-status-cancelled');
                                ?>
                                <tr>
                                    <td><input type="checkbox" name="sqb_quotes[]" value="<?php echo esc_attr($quote['id']); ?>"></td>
                                    <td><strong>#<?php echo esc_html($quote['id']); ?></strong></td>
                                    <td>
                                        <strong><?php echo esc_html($quote['customer_name'] ?: 'N/A'); ?></strong><br>
                                        <a href="mailto:<?php echo esc_attr($quote['customer_email']); ?>"><?php echo esc_html($quote['customer_email']); ?></a><br>
                                        <small><?php echo esc_html($quote['customer_phone']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo esc_html($quote['vehicle_make'] . ' ' . $quote['vehicle_model']); ?>
                                    </td>
                                    <td><strong><?php echo esc_html($total_display); ?></strong></td>
                                    <td><span class="sqb-status <?php echo esc_attr($status_class); ?>"><?php echo esc_html(ucfirst($quote['status'])); ?></span></td>
                                    <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($quote['created_at']))); ?></td>
                                    <td>
                                        <a href="#" class="sqb-view-quote button button-small" data-id="<?php echo esc_attr($quote['id']); ?>">
                                            <?php echo esc_html__('View', 'service-quote-builder'); ?>
                                        </a>
                                        <?php if ('pending' === $quote['status']): ?>
                                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=service-quote-builder&action=complete&id=' . $quote['id']), 'sqb_complete_' . $quote['id']); ?>" class="button button-small">
                                                <?php echo esc_html__('Complete', 'service-quote-builder'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=service-quote-builder&action=delete&id=' . $quote['id']), 'sqb_delete_' . $quote['id']); ?>" class="button button-small sqb-delete" onclick="return confirm('<?php echo esc_js__('Are you sure you want to delete this quote?', 'service-quote-builder'); ?>')">
                                            <?php echo esc_html__('Delete', 'service-quote-builder'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                    <div class="sqb-pagination">
                        <?php
                        $page_links = paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => __('&laquo;', 'service-quote-builder'),
                            'next_text' => __('&raquo;', 'service-quote-builder'),
                            'total' => $total_pages,
                            'current' => $paged,
                            'type' => 'plain'
                        ));
                        echo $page_links;
                        ?>
                    </div>
                <?php endif; ?>
            </form>

        <?php elseif ($current_tab === 'settings'): ?>
            <!-- Settings Tab -->
            <form method="post" action="options.php">
                <?php
                settings_fields('sqb_settings');
                do_settings_sections('service-quote-builder');
                submit_button();
                ?>
            </form>

        <?php elseif ($current_tab === 'usage'): ?>
            <!-- Usage Tab -->
            <div class="sqb-usage-guide">
                <h2><?php echo esc_html__('How to Use the Service Quote Builder', 'service-quote-builder'); ?></h2>

                <h3><?php echo esc_html__('Installation', 'service-quote-builder'); ?></h3>
                <ol>
                    <li><?php echo esc_html__('Upload the plugin folder to /wp-content/plugins/', 'service-quote-builder'); ?></li>
                    <li><?php echo esc_html__('Activate the plugin through the "Plugins" menu in WordPress', 'service-quote-builder'); ?></li>
                    <li><?php echo esc_html__('Go to "Quote Builder" in the admin menu to configure settings', 'service-quote-builder'); ?></li>
                </ol>

                <h3><?php echo esc_html__('Shortcode Usage', 'service-quote-builder'); ?></h3>
                <p><?php echo esc_html__('Add the following shortcode to any page or post to display the quote builder:', 'service-quote-builder'); ?></p>
                <code>[service_quote_builder]</code>

                <p><?php echo esc_html__('You can also add a custom title:', 'service-quote-builder'); ?></p>
                <code>[service_quote_builder title="Custom Title"]</code>

                <h3><?php echo esc_html__('Features', 'service-quote-builder'); ?></h3>
                <ul>
                    <li><?php echo esc_html__('Multi-step quote builder with visual cards', 'service-quote-builder'); ?></li>
                    <li><?php echo esc_html__('Dynamic pricing calculations', 'service-quote-builder'); ?></li>
                    <li><?php echo esc_html__('Save cart functionality with share codes', 'service-quote-builder'); ?></li>
                    <li><?php echo esc_html__('Email notifications for new quotes', 'service-quote-builder'); ?></li>
                    <li><?php echo esc_html__('Admin dashboard to manage quotes', 'service-quote-builder'); ?></li>
                    <li><?php echo esc_html__('Responsive design for mobile devices', 'service-quote-builder'); ?></li>
                </ul>

                <h3><?php echo esc_html__('Customization', 'service-quote-builder'); ?></h3>
                <p><?php echo esc_html__('You can customize the following in the Settings tab:', 'service-quote-builder'); ?></p>
                <ul>
                    <li><?php echo esc_html__('Currency symbol and position', 'service-quote-builder'); ?></li>
                    <li><?php echo esc_html__('VAT rate', 'service-quote-builder'); ?></li>
                    <li><?php echo esc_html__('Notification email address', 'service-quote-builder'); ?></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quote Detail Modal -->
<div id="sqb-quote-modal" class="sqb-admin-modal" style="display: none;">
    <div class="sqb-modal-inner">
        <h2><?php echo esc_html__('Quote Details', 'service-quote-builder'); ?></h2>
        <div id="sqb-quote-content"></div>
        <button type="button" class="button sqb-modal-close"><?php echo esc_html__('Close', 'service-quote-builder'); ?></button>
    </div>
</div>

<style>
.sqb-admin {
    max-width: 1200px;
}
.sqb-admin h1 {
    margin-bottom: 20px;
}
.nav-tab-wrapper {
    margin-bottom: 20px;
}
.sqb-admin-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.sqb-toolbar {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.sqb-toolbar-top {
    margin-bottom: 0;
    padding-bottom: 15px;
    border-bottom: 1px solid #ddd;
}
.sqb-count {
    color: #666;
}
.sqb-quotes-table {
    margin-top: 0;
}
.sqb-col-check {
    width: 40px;
}
.sqb-status {
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}
.sqb-status-pending {
    background: #fef3c7;
    color: #92400e;
}
.sqb-status-completed {
    background: #d1fae5;
    color: #065f46;
}
.sqb-status-cancelled {
    background: #fee2e2;
    color: #991b1b;
}
.sqb-pagination {
    margin-top: 20px;
}
.sqb-pagination .page-numbers {
    padding: 5px 10px;
    background: #f0f0f0;
    border-radius: 4px;
    text-decoration: none;
}
.sqb-pagination .page-numbers.current {
    background: #0073aa;
    color: white;
}
.sqb-empty {
    text-align: center;
    padding: 40px !important;
    color: #666;
}
.sqb-usage-guide {
    max-width: 800px;
}
.sqb-usage-guide h2 {
    margin-top: 0;
}
.sqb-usage-guide h3 {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}
.sqb-usage-guide ol, .sqb-usage-guide ul {
    line-height: 1.8;
}
.sqb-usage-guide code {
    background: #f0f0f0;
    padding: 2px 8px;
    border-radius: 4px;
}
.sqb-admin-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.sqb-modal-inner {
    background: white;
    padding: 30px;
    border-radius: 8px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}
.sqb-modal-close {
    margin-top: 20px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Select all checkbox
    $('#sqb-select-all').on('change', function() {
        $('input[name="sqb_quotes[]"]').prop('checked', $(this).prop('checked'));
    });

    // View quote
    $('.sqb-view-quote').on('click', function(e) {
        e.preventDefault();
        var quoteId = $(this).data('id');

        // Fetch quote details via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'sqb_get_quote',
                nonce: '<?php echo wp_create_nonce('sqb_admin_nonce'); ?>',
                quote_id: quoteId
            },
            success: function(response) {
                if (response.success) {
                    $('#sqb-quote-content').html(response.data.html);
                    $('#sqb-quote-modal').show();
                }
            }
        });
    });

    // Close modal
    $('.sqb-modal-close').on('click', function() {
        $('#sqb-quote-modal').hide();
    });

    // Close on outside click
    $('#sqb-quote-modal').on('click', function(e) {
        if ($(e.target).hasClass('sqb-admin-modal')) {
            $(this).hide();
        }
    });
});
</script>