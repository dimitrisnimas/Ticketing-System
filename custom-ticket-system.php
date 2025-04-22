<?php
/**
 * Plugin Name: Custom Ticket System
 * Description: Simple member-based ticket submission and history system.
 * Version: 1.0
 * Author: Dimitris Nimas (DimitrisNimas.gr)
 */

// Create ticket table on plugin activation
function cts_create_ticket_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'support_tickets';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        subject TEXT NOT NULL,
        message LONGTEXT NOT NULL,
        status VARCHAR(20) DEFAULT 'open',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'cts_create_ticket_table');

// Ticket form shortcode
function cts_ticket_form() {
    if (!is_user_logged_in()) return '<p>Please log in to submit a ticket.</p>';

    if (isset($_POST['cts_submit_ticket'])) {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'support_tickets', [
            'user_id' => get_current_user_id(),
            'subject' => sanitize_text_field($_POST['subject']),
            'message' => sanitize_textarea_field($_POST['message']),
        ]);
        echo '<p style="color:green;">Ticket submitted successfully!</p>';
    }

    ob_start(); ?>
    <form method="post">
        <p><input type="text" name="subject" placeholder="Subject" required style="width: 100%;"></p>
        <p><textarea name="message" placeholder="Your message" required style="width: 100%; height: 100px;"></textarea></p>
        <p><button type="submit" name="cts_submit_ticket">Submit Ticket</button></p>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_ticket_form', 'cts_ticket_form');

// Ticket history shortcode
function cts_ticket_history() {
    if (!is_user_logged_in()) return '';

    global $wpdb;
    $user_id = get_current_user_id();
    $table = $wpdb->prefix . 'support_tickets';
    $tickets = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC",
        $user_id
    ));

    ob_start();
    echo '<h3>My Tickets</h3>';
    if ($tickets) {
        foreach ($tickets as $ticket) {
            echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px;'>
                <strong>{$ticket->subject}</strong><br>
                <small>Status: {$ticket->status} | {$ticket->created_at}</small><br><br>
                {$ticket->message}
            </div>";
        }
    } else {
        echo '<p>No tickets found.</p>';
    }

    return ob_get_clean();
}
add_shortcode('custom_ticket_history', 'cts_ticket_history');


// Add admin menu for ticket management
function cts_register_admin_menu() {
    add_menu_page(
        'Support Tickets',
        'Support Tickets',
        'manage_options',
        'cts_ticket_admin',
        'cts_render_admin_page',
        'dashicons-tickets-alt',
        25
    );
}
add_action('admin_menu', 'cts_register_admin_menu');

// Render the admin page
function cts_render_admin_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'support_tickets';

    // Update ticket status
    if (isset($_POST['cts_update_status'])) {
        $ticket_id = intval($_POST['ticket_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        $wpdb->update($table, ['status' => $new_status], ['id' => $ticket_id]);
        echo '<div class="notice notice-success is-dismissible"><p>Status updated!</p></div>';
    }

    // Fetch all tickets
    $tickets = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

    echo '<div class="wrap"><h1>Support Tickets</h1>';
    if ($tickets) {
        echo '<table class="widefat fixed striped"><thead>
            <tr><th>ID</th><th>User</th><th>Subject</th><th>Message</th><th>Status</th><th>Date</th><th>Actions</th></tr>
        </thead><tbody>';
        foreach ($tickets as $ticket) {
            $user = get_userdata($ticket->user_id);
            echo "<tr>
                <td>{$ticket->id}</td>
                <td>{$user->user_login}</td>
                <td>{$ticket->subject}</td>
                <td>{$ticket->message}</td>
                <td>{$ticket->status}</td>
                <td>{$ticket->created_at}</td>
                <td>
                    <form method='post' style='display:inline-block;'>
                        <input type='hidden' name='ticket_id' value='{$ticket->id}'>
                        <select name='new_status'>
                            <option value='open'" . selected($ticket->status, 'open', false) . ">Open</option>
                            <option value='closed'" . selected($ticket->status, 'closed', false) . ">Closed</option>
                        </select>
                        <button type='submit' name='cts_update_status' class='button'>Update</button>
                    </form>
                </td>
            </tr>";
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No tickets found.</p>';
    }
    echo '</div>';
}

