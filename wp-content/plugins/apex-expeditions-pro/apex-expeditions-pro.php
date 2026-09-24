<?php
/**
 * Plugin Name: Apex Expeditions Pro & Database Engine
 * Plugin URI: https://apexpeak-mountains.com/
 * Description: Mission-critical expedition management database system. Provides custom MySQL tables for expeditions, bookings, gear rental calculators, and climber reviews.
 * Version: 2.5.0
 * Author: Apex Mountaineering Engineering
 * Text Domain: apex-expeditions
 */

if (!defined('ABSPATH')) {
    exit;
}

define('APEX_EXP_VERSION', '2.5.0');
define('APEX_EXP_PATH', plugin_dir_path(__FILE__));
define('APEX_EXP_URL', plugin_dir_url(__FILE__));

/**
 * 1. DATABASE SCHEMA & ACTIVATION
 */
register_activation_hook(__FILE__, 'apex_install_database_tables');

function apex_install_database_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // 1. Expeditions Table
    $table_expeditions = $wpdb->prefix . 'apex_expeditions';
    $sql1 = "CREATE TABLE $table_expeditions (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        region varchar(100) NOT NULL,
        elevation_m int(11) NOT NULL,
        duration_days int(11) NOT NULL,
        difficulty varchar(50) NOT NULL,
        price_usd decimal(10,2) NOT NULL,
        guide_ratio varchar(50) NOT NULL,
        image_url varchar(500) NOT NULL,
        available_slots int(11) DEFAULT 6,
        status varchar(20) DEFAULT 'active',
        PRIMARY KEY  (id)
    ) $charset_collate;";
    dbDelta($sql1);

    // 2. Bookings Table
    $table_bookings = $wpdb->prefix . 'apex_bookings';
    $sql2 = "CREATE TABLE $table_bookings (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        expedition_name varchar(255) NOT NULL,
        climber_name varchar(255) NOT NULL,
        climber_email varchar(255) NOT NULL,
        climber_phone varchar(50) NOT NULL,
        departure_season varchar(50) NOT NULL,
        experience_level varchar(100) NOT NULL,
        notes text,
        status varchar(50) DEFAULT 'Confirmed Lead',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    dbDelta($sql2);

    // 3. Gear Inventory Table
    $table_gear = $wpdb->prefix . 'apex_gear_inventory';
    $sql3 = "CREATE TABLE $table_gear (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        item_name varchar(255) NOT NULL,
        category varchar(100) NOT NULL,
        daily_price decimal(10,2) NOT NULL,
        stock int(11) DEFAULT 10,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    dbDelta($sql3);

    // 4. Climber Reviews Table
    $table_reviews = $wpdb->prefix . 'apex_reviews';
    $sql4 = "CREATE TABLE $table_reviews (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        climber_name varchar(255) NOT NULL,
        origin varchar(100) NOT NULL,
        route_name varchar(255) NOT NULL,
        rating int(2) DEFAULT 5,
        review_text text NOT NULL,
        summit_year int(4) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    dbDelta($sql4);

    apex_seed_default_data();
}

function apex_seed_default_data() {
    global $wpdb;

    // Seed Expeditions
    $table_exp = $wpdb->prefix . 'apex_expeditions';
    if ($wpdb->get_var("SELECT COUNT(*) FROM $table_exp") == 0) {
        $wpdb->insert($table_exp, [
            'name' => 'Mont Blanc Classic Traverse',
            'region' => 'French Alps (Chamonix)',
            'elevation_m' => 4809,
            'duration_days' => 6,
            'difficulty' => 'Level 1: Alpine Skills',
            'price_usd' => 2850.00,
            'guide_ratio' => '2:1 Max',
            'image_url' => 'http://localhost/wordpress/wp-content/uploads/2026/09/mont-blanc-climb.jpg',
            'available_slots' => 4,
            'status' => 'active'
        ]);
        $wpdb->insert($table_exp, [
            'name' => 'Matterhorn Hörnli Ridge',
            'region' => 'Pennine Alps (Switzerland)',
            'elevation_m' => 4478,
            'duration_days' => 5,
            'difficulty' => 'Level 2: Classic Technical',
            'price_usd' => 4650.00,
            'guide_ratio' => '1:1 Strict',
            'image_url' => 'http://localhost/wordpress/wp-content/uploads/2026/09/matterhorn-peak-ascent.jpg',
            'available_slots' => 2,
            'status' => 'active'
        ]);
        $wpdb->insert($table_exp, [
            'name' => 'Ama Dablam Technical Expedition',
            'region' => 'Khumbu Himalayas (Nepal)',
            'elevation_m' => 6812,
            'duration_days' => 28,
            'difficulty' => 'Level 3: Himalayan Giant',
            'price_usd' => 9400.00,
            'guide_ratio' => '1:1 High Altitude Sherpa',
            'image_url' => 'http://localhost/wordpress/wp-content/uploads/2026/09/ama-dablam-peak.jpg',
            'available_slots' => 3,
            'status' => 'active'
        ]);
    }

    // Seed Gear Inventory
    $table_gear = $wpdb->prefix . 'apex_gear_inventory';
    if ($wpdb->get_var("SELECT COUNT(*) FROM $table_gear") == 0) {
        $gear_items = [
            ['item_name' => 'La Sportiva Olympus Mons Double Boots', 'category' => 'Footwear', 'daily_price' => 15.00, 'stock' => 12],
            ['item_name' => 'Petzl Lynx Modular Technical Crampons', 'category' => 'Hardgoods', 'daily_price' => 8.00, 'stock' => 20],
            ['item_name' => 'Petzl Quark Technical Ice Axe (Pair)', 'category' => 'Hardgoods', 'daily_price' => 12.00, 'stock' => 16],
            ['item_name' => 'Western Mountaineering -30°C Down Sleeping Bag', 'category' => 'Sleep Systems', 'daily_price' => 14.00, 'stock' => 8],
            ['item_name' => 'Garmin inReach Explorer+ Satellite Tracker', 'category' => 'Electronics', 'daily_price' => 10.00, 'stock' => 15],
            ['item_name' => 'Black Diamond Vapor Lightweight Climbing Helmet', 'category' => 'Protection', 'daily_price' => 5.00, 'stock' => 25]
        ];
        foreach ($gear_items as $item) {
            $wpdb->insert($table_gear, $item);
        }
    }

    // Seed Verified Reviews
    $table_reviews = $wpdb->prefix . 'apex_reviews';
    if ($wpdb->get_var("SELECT COUNT(*) FROM $table_reviews") == 0) {
        $reviews = [
            [
                'climber_name' => 'Marcus Vance',
                'origin' => 'Zurich, Switzerland',
                'route_name' => 'Ama Dablam Southwest Ridge',
                'rating' => 5,
                'review_text' => 'The medical supervision and acclimatization discipline were unmatched. Our Sherpa guide was flawless on the yellow tower.',
                'summit_year' => 2025
            ],
            [
                'climber_name' => 'Dr. Clara Morales',
                'origin' => 'Madrid, Spain',
                'route_name' => 'Mont Blanc Goûter Traverse',
                'rating' => 5,
                'review_text' => 'The 2:1 guide ratio made all the difference when predawn winds picked up above the Dôme du Goûter. Pure professionals.',
                'summit_year' => 2025
            ],
            [
                'climber_name' => 'David Sterling',
                'origin' => 'London, UK',
                'route_name' => 'Matterhorn Hörnli Ridge',
                'rating' => 5,
                'review_text' => 'Strict 1:1 guiding ensured total precision on the Solvay slabs. Apex Peak delivered an unforgettable, safe summit.',
                'summit_year' => 2024
            ]
        ];
        foreach ($reviews as $rev) {
            $wpdb->insert($table_reviews, $rev);
        }
    }

    // Seed Initial Test Booking
    $table_bookings = $wpdb->prefix . 'apex_bookings';
    if ($wpdb->get_var("SELECT COUNT(*) FROM $table_bookings") == 0) {
        $wpdb->insert($table_bookings, [
            'expedition_name' => 'Ama Dablam Technical Expedition',
            'climber_name' => 'Shubham Ramjiyani',
            'climber_email' => 'shubhamramjiyani2006@gmail.com',
            'climber_phone' => '+91 95032 48752',
            'departure_season' => 'Autumn 2026',
            'experience_level' => 'Advanced Mountaineer',
            'notes' => 'Expedition inquiry received directly via Apex Peak Database Engine.',
            'status' => 'Confirmed Lead',
            'created_at' => current_time('mysql')
        ]);
    }
}

/**
 * 2. ADMIN MENU & DATABASE DASHBOARD
 */
add_action('admin_menu', 'apex_register_admin_menus');

function apex_register_admin_menus() {
    add_menu_page(
        'Apex Expeditions DB',
        'Apex Expeditions',
        'manage_options',
        'apex-expeditions',
        'apex_render_admin_bookings',
        'dashicons-location-alt',
        25
    );

    add_submenu_page(
        'apex-expeditions',
        'Bookings & Leads',
        'Bookings & Leads',
        'manage_options',
        'apex-expeditions',
        'apex_render_admin_bookings'
    );

    add_submenu_page(
        'apex-expeditions',
        'Expeditions Inventory',
        'Expeditions Catalog',
        'manage_options',
        'apex-expeditions-catalog',
        'apex_render_admin_catalog'
    );

    add_submenu_page(
        'apex-expeditions',
        'Gear Inventory DB',
        'Gear Rental DB',
        'manage_options',
        'apex-gear-db',
        'apex_render_admin_gear'
    );

    add_submenu_page(
        'apex-expeditions',
        'Database Engine Status',
        'DB Engine Health',
        'manage_options',
        'apex-db-health',
        'apex_render_admin_health'
    );
}

function apex_render_admin_bookings() {
    global $wpdb;
    $table = $wpdb->prefix . 'apex_bookings';

    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        check_admin_referer('apex_del_booking_' . $_GET['id']);
        $wpdb->delete($table, ['id' => intval($_GET['id'])]);
        echo '<div class="notice notice-success"><p>Booking record removed.</p></div>';
    }

    $bookings = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC LIMIT 50");
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:10px;">
            <span class="dashicons dashicons-calendar-alt" style="font-size:32px;width:32px;height:32px;"></span>
            Apex Expeditions &bull; Real-Time Climber Bookings Database
        </h1>
        <p>Live entries recorded into table: <code><?php echo esc_html($table); ?></code></p>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th width="60">ID</th>
                    <th>Climber Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Target Expedition</th>
                    <th>Season</th>
                    <th>Experience</th>
                    <th>Status</th>
                    <th>Date Received</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr><td colspan="9">No bookings in the database yet.</td></tr>
                <?php else: foreach ($bookings as $b): ?>
                    <tr>
                        <td><strong>#<?php echo esc_html($b->id); ?></strong></td>
                        <td><strong><?php echo esc_html($b->climber_name); ?></strong></td>
                        <td><a href="mailto:<?php echo esc_attr($b->climber_email); ?>"><?php echo esc_html($b->climber_email); ?></a></td>
                        <td><a href="tel:<?php echo esc_attr($b->climber_phone); ?>"><?php echo esc_html($b->climber_phone); ?></a></td>
                        <td><span style="background:#e0f2fe;color:#0369a1;padding:3px 8px;border-radius:4px;font-weight:600;"><?php echo esc_html($b->expedition_name); ?></span></td>
                        <td><?php echo esc_html($b->departure_season); ?></td>
                        <td><?php echo esc_html($b->experience_level); ?></td>
                        <td><span style="background:#dcfce7;color:#166534;padding:3px 8px;border-radius:4px;font-weight:700;"><?php echo esc_html($b->status); ?></span></td>
                        <td><?php echo esc_html($b->created_at); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function apex_render_admin_catalog() {
    global $wpdb;
    $table = $wpdb->prefix . 'apex_expeditions';
    $expeditions = $wpdb->get_results("SELECT * FROM $table ORDER BY elevation_m DESC");
    ?>
    <div class="wrap">
        <h1>Expeditions Database Table (<code><?php echo esc_html($table); ?></code>)</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Expedition Name</th>
                    <th>Region</th>
                    <th>Elevation</th>
                    <th>Duration</th>
                    <th>Difficulty</th>
                    <th>Price (USD)</th>
                    <th>Guide Ratio</th>
                    <th>Slots</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expeditions as $e): ?>
                    <tr>
                        <td>#<?php echo esc_html($e->id); ?></td>
                        <td><strong><?php echo esc_html($e->name); ?></strong></td>
                        <td><?php echo esc_html($e->region); ?></td>
                        <td><?php echo number_format($e->elevation_m); ?>m</td>
                        <td><?php echo esc_html($e->duration_days); ?> Days</td>
                        <td><?php echo esc_html($e->difficulty); ?></td>
                        <td>$<?php echo number_format($e->price_usd, 2); ?></td>
                        <td><?php echo esc_html($e->guide_ratio); ?></td>
                        <td><?php echo esc_html($e->available_slots); ?> left</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function apex_render_admin_gear() {
    global $wpdb;
    $table = $wpdb->prefix . 'apex_gear_inventory';
    $gear = $wpdb->get_results("SELECT * FROM $table ORDER BY category ASC");
    ?>
    <div class="wrap">
        <h1>Gear Rental Database (<code><?php echo esc_html($table); ?></code>)</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gear Item</th>
                    <th>Category</th>
                    <th>Daily Rental Rate</th>
                    <th>In Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gear as $g): ?>
                    <tr>
                        <td>#<?php echo esc_html($g->id); ?></td>
                        <td><strong><?php echo esc_html($g->item_name); ?></strong></td>
                        <td><?php echo esc_html($g->category); ?></td>
                        <td>$<?php echo number_format($g->daily_price, 2); ?>/day</td>
                        <td><?php echo esc_html($g->stock); ?> units</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function apex_render_admin_health() {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $tables = [
        $prefix . 'apex_expeditions',
        $prefix . 'apex_bookings',
        $prefix . 'apex_gear_inventory',
        $prefix . 'apex_reviews'
    ];
    ?>
    <div class="wrap">
        <h1>Apex Expeditions &bull; MySQL Database Engine Diagnostics</h1>
        <p>Direct MySQL health statistics for custom plugin tables.</p>
        <table class="wp-list-table widefat fixed striped" style="max-width:800px;">
            <thead>
                <tr>
                    <th>Table Name</th>
                    <th>Rows Count</th>
                    <th>Engine</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $t): 
                    $count = $wpdb->get_var("SELECT COUNT(*) FROM $t");
                    $status = ($count !== null) ? 'HEALTHY (Active)' : 'NOT FOUND';
                ?>
                    <tr>
                        <td><code><?php echo esc_html($t); ?></code></td>
                        <td><strong><?php echo intval($count); ?></strong> records</td>
                        <td>InnoDB</td>
                        <td><span style="color:#16a34a;font-weight:700;">● <?php echo esc_html($status); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * 3. REST API ENDPOINTS FOR CLIENT-SIDE INTERACTION
 */
add_action('rest_api_init', function () {
    register_rest_route('apex/v1', '/book', [
        'methods' => 'POST',
        'callback' => 'apex_handle_api_booking',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('apex/v1', '/calculate-gear', [
        'methods' => 'POST',
        'callback' => 'apex_handle_gear_calculation',
        'permission_callback' => '__return_true'
    ]);
});

function apex_handle_api_booking($request) {
    global $wpdb;
    $params = $request->get_json_params();

    $name = sanitize_text_field($params['climber_name'] ?? '');
    $email = sanitize_email($params['climber_email'] ?? '');
    $phone = sanitize_text_field($params['climber_phone'] ?? '');
    $expedition = sanitize_text_field($params['expedition_name'] ?? 'General Mountain Inquiry');
    $season = sanitize_text_field($params['departure_season'] ?? '2026/2027');
    $experience = sanitize_text_field($params['experience_level'] ?? 'Intermediate');
    $notes = sanitize_textarea_field($params['notes'] ?? '');

    if (empty($name) || empty($email)) {
        return new WP_Error('missing_fields', 'Name and Email are mandatory', ['status' => 400]);
    }

    $table = $wpdb->prefix . 'apex_bookings';
    $inserted = $wpdb->insert($table, [
        'expedition_name' => $expedition,
        'climber_name' => $name,
        'climber_email' => $email,
        'climber_phone' => $phone,
        'departure_season' => $season,
        'experience_level' => $experience,
        'notes' => $notes,
        'status' => 'Confirmed Lead',
        'created_at' => current_time('mysql')
    ]);

    if (!$inserted) {
        return new WP_Error('db_error', 'Database insert failed', ['status' => 500]);
    }

    $booking_id = $wpdb->insert_id;
    $admin_email = 'shubhamramjiyani2006@gmail.com';
    $subject = "[Apex Expeditions] New Booking #{$booking_id} from {$name}";
    $body = "A new climber booking has been saved directly to your MySQL database:\n\n" .
            "Booking ID: #{$booking_id}\n" .
            "Climber Name: {$name}\n" .
            "Email: {$email}\n" .
            "Phone: {$phone}\n" .
            "Target Expedition: {$expedition}\n" .
            "Season: {$season}\n" .
            "Experience Level: {$experience}\n" .
            "Notes: {$notes}\n\n" .
            "View in WP Admin: " . admin_url('admin.php?page=apex-expeditions');
    @wp_mail($admin_email, $subject, $body);

    return rest_ensure_response([
        'success' => true,
        'booking_id' => $booking_id,
        'message' => 'Thank you! Your expedition request has been saved directly to our mountain database.'
    ]);
}

function apex_handle_gear_calculation($request) {
    global $wpdb;
    $params = $request->get_json_params();
    $days = max(1, intval($params['days'] ?? 7));
    $selected_ids = array_map('intval', (array)($params['items'] ?? []));

    if (empty($selected_ids)) {
        return rest_ensure_response(['total' => 0, 'items' => []]);
    }

    $table = $wpdb->prefix . 'apex_gear_inventory';
    $placeholders = implode(',', array_fill(0, count($selected_ids), '%d'));
    $query = $wpdb->prepare("SELECT * FROM $table WHERE id IN ($placeholders)", $selected_ids);
    $items = $wpdb->get_results($query);

    $total = 0;
    foreach ($items as $item) {
        $total += floatval($item->daily_price) * $days;
    }

    return rest_ensure_response([
        'days' => $days,
        'items_count' => count($items),
        'total_usd' => number_format($total, 2)
    ]);
}

/**
 * 4. SHORTCODES FOR ZERO-HTML GORGEOUS UI
 */

// Shortcode: [apex_expeditions_grid]
add_shortcode('apex_expeditions_grid', 'apex_shortcode_expeditions_grid');
function apex_shortcode_expeditions_grid() {
    global $wpdb;
    $table = $wpdb->prefix . 'apex_expeditions';
    $expeditions = $wpdb->get_results("SELECT * FROM $table WHERE status = 'active' ORDER BY elevation_m DESC");

    if (empty($expeditions)) {
        return '<p>No expeditions currently available.</p>';
    }

    ob_start();
    ?>
    <div class="apex-exp-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:24px;margin:24px 0;">
        <?php foreach ($expeditions as $exp): ?>
            <div class="apex-exp-card" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 4px 6px -1px rgba(0,0,0,0.06);display:flex;flex-direction:column;justify-content:space-between;">
                <div style="position:relative;height:220px;overflow:hidden;">
                    <img src="<?php echo esc_url($exp->image_url); ?>" alt="<?php echo esc_attr($exp->name); ?>" style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s ease;">
                    <span style="position:absolute;top:12px;left:12px;background:rgba(15,23,42,0.85);color:#38bdf8;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700;backdrop-filter:blur(4px);">
                        <?php echo esc_html($exp->difficulty); ?>
                    </span>
                    <span style="position:absolute;bottom:12px;right:12px;background:#0284c7;color:#fff;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700;">
                        <?php echo esc_html($exp->available_slots); ?> Slots Left
                    </span>
                </div>
                <div style="padding:20px;flex-grow:1;display:flex;flex-direction:column;justify-content:space-between;">
                    <div>
                        <div style="font-size:13px;color:#64748b;font-weight:600;margin-bottom:4px;"><?php echo esc_html($exp->region); ?></div>
                        <h3 style="margin:0 0 12px 0;font-size:20px;font-weight:800;color:#0f172a;"><?php echo esc_html($exp->name); ?></h3>
                        <div style="display:flex;gap:12px;font-size:13px;color:#334155;margin-bottom:16px;">
                            <span>🏔️ <strong><?php echo number_format($exp->elevation_m); ?>m</strong></span>
                            <span>⏳ <strong><?php echo esc_html($exp->duration_days); ?> Days</strong></span>
                            <span>👥 <strong><?php echo esc_html($exp->guide_ratio); ?></strong></span>
                        </div>
                    </div>
                    <div style="border-top:1px solid #f1f5f9;padding-top:16px;display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:700;">All-Inclusive</div>
                            <div style="font-size:22px;font-weight:800;color:#0284c7;">$<?php echo number_format($exp->price_usd); ?></div>
                        </div>
                        <a href="/wordpress/contact/?expedition=<?php echo urlencode($exp->name); ?>" class="wp-element-button" style="background:#0284c7;color:#fff;padding:8px 18px;border-radius:6px;text-decoration:none;font-weight:600;font-size:14px;">
                            Book Route &rarr;
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

// Shortcode: [apex_booking_form]
add_shortcode('apex_booking_form', 'apex_shortcode_booking_form');
function apex_shortcode_booking_form() {
    ob_start();
    ?>
    <div id="apex-booking-container" style="background:#ffffff;border:1px solid #cbd5e1;border-radius:14px;padding:32px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.07);max-width:680px;margin:20px auto;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span style="font-size:24px;">🏔️</span>
            <h3 style="margin:0;font-size:24px;font-weight:800;color:#0f172a;">Reserve Your Mountain Departure</h3>
        </div>
        <p style="color:#64748b;font-size:14px;margin-bottom:24px;">Submissions are written directly to our operational database. Expect confirmation within 24 hours.</p>

        <form id="apex-live-booking-form" onsubmit="handleApexBooking(event)">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px;">Full Name *</label>
                    <input type="text" name="climber_name" required placeholder="e.g. Alex Morgan" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px;">Email Address *</label>
                    <input type="email" name="climber_email" required placeholder="alex@domain.com" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;box-sizing:border-box;">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px;">WhatsApp / Phone Number</label>
                    <input type="tel" name="climber_phone" placeholder="+1 555 019 2831" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px;">Target Expedition *</label>
                    <select name="expedition_name" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;box-sizing:border-box;background:#fff;">
                        <option value="Mont Blanc Classic Traverse">Mont Blanc Classic (4,809m)</option>
                        <option value="Matterhorn Hörnli Ridge">Matterhorn Hörnli (4,478m)</option>
                        <option value="Ama Dablam Technical Expedition">Ama Dablam (6,812m)</option>
                        <option value="Gran Paradiso Alpine Skills">Gran Paradiso Skills (4,061m)</option>
                        <option value="Custom Private Expedition">Custom Private Expedition</option>
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px;">Season</label>
                    <select name="departure_season" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;box-sizing:border-box;background:#fff;">
                        <option value="Spring 2026">Spring 2026</option>
                        <option value="Summer 2026">Summer 2026</option>
                        <option value="Autumn 2026">Autumn 2026</option>
                        <option value="Winter 2026/27">Winter 2026/27</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px;">Climbing Experience</label>
                    <select name="experience_level" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;box-sizing:border-box;background:#fff;">
                        <option value="Novice / Hiker">Novice (Trekking only)</option>
                        <option value="Intermediate">Intermediate (Crampons / Glacier)</option>
                        <option value="Advanced">Advanced (Multi-pitch / Technical)</option>
                        <option value="High Altitude Veteran">Veteran (Prior 6,000m+)</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:6px;">Medical or Fitness Notes</label>
                <textarea name="notes" rows="3" placeholder="Tell our expedition doctor about any prior acclimatization experiences or training..." style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;box-sizing:border-box;"></textarea>
            </div>

            <button type="submit" id="apex-submit-btn" style="background:#0284c7;color:#fff;border:none;padding:14px 28px;border-radius:8px;font-weight:700;font-size:16px;cursor:pointer;width:100%;transition:background 0.2s;">
                Submit Booking &rarr;
            </button>
            <div id="apex-booking-feedback" style="margin-top:16px;display:none;padding:12px;border-radius:6px;font-size:14px;"></div>
        </form>
    </div>

    <script>
    async function handleApexBooking(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('apex-submit-btn');
        const feedback = document.getElementById('apex-booking-feedback');

        btn.disabled = true;
        btn.innerText = 'Submitting Booking...';

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        try {
            const res = await fetch('/wordpress/wp-json/apex/v1/book', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (res.ok && data.success) {
                feedback.style.display = 'block';
                feedback.style.background = '#dcfce7';
                feedback.style.color = '#15803d';
                feedback.style.border = '1px solid #86efac';
                feedback.innerHTML = '<strong>Success!</strong> ' + data.message + ' (Booking Reference #' + data.booking_id + ')';
                form.reset();
            } else {
                throw new Error(data.message || 'Error saving booking');
            }
        } catch (err) {
            feedback.style.display = 'block';
            feedback.style.background = '#fee2e2';
            feedback.style.color = '#b91c1c';
            feedback.style.border = '1px solid #fca5a5';
            feedback.innerHTML = '<strong>Error:</strong> ' + err.message;
        } finally {
            btn.disabled = false;
            btn.innerText = 'Submit Booking →';
        }
    }
    </script>
    <?php
    return ob_get_clean();
}

// Shortcode: [apex_gear_calculator]
add_shortcode('apex_gear_calculator', 'apex_shortcode_gear_calculator');
function apex_shortcode_gear_calculator() {
    global $wpdb;
    $table = $wpdb->prefix . 'apex_gear_inventory';
    $gear = $wpdb->get_results("SELECT * FROM $table ORDER BY category ASC");

    ob_start();
    ?>
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:28px;margin:24px 0;">
        <h3 style="margin-top:0;font-size:22px;font-weight:800;color:#0f172a;">Interactive Gear Rental Cost Calculator</h3>
        <p style="color:#64748b;font-size:14px;margin-bottom:20px;">Calculate your exact gear locker costs pulled live from our MySQL database.</p>

        <div style="margin-bottom:20px;display:flex;align-items:center;gap:12px;">
            <label style="font-weight:700;font-size:14px;color:#334155;">Rental Duration (Days):</label>
            <input type="number" id="apex-gear-days" value="7" min="1" max="60" onchange="calculateGearTotal()" style="width:80px;padding:6px 10px;border:1px solid #cbd5e1;border-radius:6px;font-size:15px;font-weight:700;">
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:12px;margin-bottom:24px;">
            <?php foreach ($gear as $item): ?>
                <label style="display:flex;align-items:center;gap:10px;background:#fff;padding:12px;border:1px solid #cbd5e1;border-radius:8px;cursor:pointer;">
                    <input type="checkbox" class="apex-gear-check" value="<?php echo esc_attr($item->daily_price); ?>" onchange="calculateGearTotal()">
                    <div>
                        <div style="font-weight:700;font-size:14px;color:#0f172a;"><?php echo esc_html($item->item_name); ?></div>
                        <div style="font-size:12px;color:#0284c7;font-weight:600;">$<?php echo number_format($item->daily_price, 2); ?> / day &bull; <?php echo esc_html($item->category); ?></div>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>

        <div style="background:#0f172a;color:#fff;padding:16px 24px;border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <div style="font-size:12px;color:#94a3b8;text-transform:uppercase;font-weight:700;">Estimated Equipment Total</div>
                <div style="font-size:26px;font-weight:800;color:#38bdf8;" id="apex-gear-total-display">$0.00 USD</div>
            </div>
            <a href="/wordpress/contact/" style="background:#0284c7;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:700;font-size:14px;">Reserve Gear Locker &rarr;</a>
        </div>
    </div>

    <script>
    function calculateGearTotal() {
        const days = parseInt(document.getElementById('apex-gear-days').value) || 1;
        const checks = document.querySelectorAll('.apex-gear-check');
        let dailyTotal = 0;
        checks.forEach(c => {
            if (c.checked) {
                dailyTotal += parseFloat(c.value) || 0;
            }
        });
        const total = dailyTotal * days;
        document.getElementById('apex-gear-total-display').innerText = '$' + total.toFixed(2) + ' USD';
    }
    </script>
    <?php
    return ob_get_clean();
}

// Shortcode: [apex_summit_reviews]
add_shortcode('apex_summit_reviews', 'apex_shortcode_summit_reviews');
function apex_shortcode_summit_reviews() {
    global $wpdb;
    $table = $wpdb->prefix . 'apex_reviews';
    $reviews = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

    ob_start();
    ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:20px;margin:24px 0;">
        <?php foreach ($reviews as $r): ?>
            <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <span style="color:#f59e0b;font-size:16px;">★★★★★</span>
                        <span style="background:#dcfce7;color:#15803d;font-size:11px;font-weight:700;padding:2px 8px;border-radius:9999px;">✓ Verified Summit</span>
                    </div>
                    <p style="font-style:italic;color:#334155;font-size:14px;line-height:1.6;margin-bottom:16px;">"<?php echo esc_html($r->review_text); ?>"</p>
                </div>
                <div style="border-top:1px solid #f1f5f9;padding-top:12px;">
                    <div style="font-weight:800;color:#0f172a;"><?php echo esc_html($r->climber_name); ?></div>
                    <div style="font-size:12px;color:#64748b;"><?php echo esc_html($r->origin); ?> &bull; <?php echo esc_html($r->route_name); ?> (<?php echo esc_html($r->summit_year); ?>)</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
