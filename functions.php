<?php

require_once QS_DIR . '/settings/settings.php';

add_filter('woocommerce_checkout_fields', function ($fields) {

    $only_virtual = true;

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if (!$cart_item['data']->is_virtual())
            $only_virtual = false;
    }

    if ($only_virtual) {

         unset($fields['billing']['billing_first_name']);

         unset($fields['billing']['billing_last_name']);
        
        unset($fields['billing']['billing_email']);
        
        unset($fields['billing']['billing_company']);

        unset($fields['billing']['billing_address_1']);

        unset($fields['billing']['billing_address_2']);

        unset($fields['billing']['billing_city']);

        unset($fields['billing']['billing_postcode']);

         unset($fields['billing']['billing_country']);

        unset($fields['billing']['billing_state']);

        // unset($fields['billing']['billing_phone']);

        add_filter('woocommerce_enable_order_notes_field', '__return_false');

    }
    return $fields;

});



add_filter('woocommerce_billing_fields', function ($fields) {
    
    return $fields;
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('template_include', function ($template) {
    if (is_singular('product')) {
        $product_id = get_the_ID();

        $is_project = get_post_meta($product_id, 'is_project', true);
        $is_gift = get_post_meta($product_id, 'is_gift', true);

        if (!$is_gift && !$is_project) {
            $template = QS_DIR . '/single-product-template.php';
        } elseif ($is_gift) {
            $template = QS_DIR . '/single-gift-product-template.php';
            if (isset($_GET['tuning']) && (current_user_can('administrator') || current_user_can('editor'))) {
                $template = QS_DIR . '/tuning-gift-template.php';
            }
        } elseif ($is_project) {
            $template = QS_DIR . '/single-project-product-template.php';
        }

    }
    return $template;
}, 99);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('init', function () {
    if (!isset($_GET['test']))
        return;
    if (!extension_loaded('imagick')){
        echo 'imagick not installed';
    }else{
        echo 'imagick installed';
    }
    exit;
});

add_action('template_redirect', function () {
    if (!isset($_GET['addtocard']))
        return;
    $product_id = $_GET['product_id'];
    $price = $_GET['price'];
    $cart_item_data = array('custom_price' => $price);
    WC()->cart->add_to_cart($product_id, 1, 0, array(), $cart_item_data);
    echo 1;
    exit;
});

add_action('woocommerce_before_calculate_totals', function ($cart_object) {
    if (!WC()->session->__isset("reload_checkout")) {
        foreach ($cart_object->cart_contents as $key => $value) {
            if (isset($value["custom_price"])) {
                $value['data']->set_price($value["custom_price"]);
            }
        }
    }
}, 99);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('the_content', function ($content) {

    $post_id = get_the_id();
    $is_project = get_post_meta($post_id, 'is_project', true);

    if (!$is_project)
        return $content;

    $permalink = get_the_permalink();
    $title = get_the_title();
    $thumbnail_url = get_the_post_thumbnail_url();
    $type = get_post_type();
    $share_value = get_post_meta($post_id, 'share_value', true);
    $project_goal = get_post_meta($post_id, 'project_goal', true);
    $net_revenue = qs_get_product_net_revenue($post_id);
    $completed_donations = get_post_meta($post_id, 'completed_donations', true);

    if (!$share_value)
        $share_value = 20;
    if (!$init_value)
        $init_value = 0;
    if (!$project_goal)
        $project_goal = 1000;

    $precentage = (int) round(100 * $net_revenue / $project_goal);

    $completed = $completed_donations && $precentage >= 100;

    if ($completed)
        $donate_btn = '<p style="display: block;background-color: #037569;color: white;margin-top: 11px;border-radius: 5px;padding: 4px 0;">المشروع مكتمل</p>';
    else
        $donate_btn = '<div class="donate-btn"><a href="javascript:void(0)">تبرع الآن</a></div>';

    $c = '
        <div style="margin: 20px 10px; padding: 20px 30px; font-size: 16px; border-bottom: solid 1px #0000000d; border-top: solid 1px #0000000d;">هدف المشروع: <b style="color: #037569;">' . $project_goal . '</b> ريال</div>    
        <div class="projects-container p-container"><div class="post-card" data-id="' . $post_id . '" data-share_value="' . $share_value . '">
            <div class="card-content">
                <div class="input_spinner" style="' . ($completed ? 'display:none;' : '') . '" data-share="' . $share_value . '">
                    <button class="spinner-btn btn btn-primary btn-sm" data-dir="down">-</button>
                    <input type="number" name="shares" maxlength="3" value="1" placeholder="عدد الأسهم" min="1" max="200">
                    <button class="spinner-btn btn btn-primary btn-sm" data-dir="up">+</button>
                </div>
                
                <div class="progress_bar" style="overflow: unset">
                    <div class="projcet-progress" style="width: ' . $precentage . '%"></div>
                    <div>تم التبرع بـ <span>' . $net_revenue . '</span> ريال من <span>' . $project_goal . '</span></div>
                    <div style="position: absolute; left: 0; top: -22px;">' . $precentage . ' %</div>
                </div>
                <div class="amount"><span>مبلغ التبرع</span><i>ر.س.</i><input class="' . ($completed ? 'disable-input' : '') . '" type="number" name="amount" maxlength="6" placeholder="مبلغ التبرع" min="1" max="250000" value="' . $share_value . '"></div>
                ' . $donate_btn . '
            </div>
            </div></div>';

    return $content . $c;
}, 6);

add_action('wp_head', function () {
    if (is_single()) {
        $post_id = get_the_id();
        $is_project = get_post_meta($post_id, 'is_project', true);
        if ($is_project) {
            ?>
            <style>
                p.price,
                form.cart,
                .yith-wcwl-add-to-wishlist,
                .product_meta {
                    display: none;
                }

                .post-card {
                    margin: 5px 0;
                    width: 100%;
                    max-width: 500px;
                }

                .projects-container,
                .gifts-container {
                    justify-content: center;
                }

                body.woocommerce #content-area div.product .woocommerce-tabs .panel,
                body.woocommerce div.product .woocommerce-tabs .panel {
                    padding: 30px 15px;
                }
            </style>
            <?php
        }
    }
}, 99);


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zad_pagination()
{
    global $wp_query;
    $total = $wp_query->max_num_pages;
    if ($total > 1) {
        if (!$current_page = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1)) {
            $current_page = 1;
        }
        echo '<div class="pagination"><div class="pagination-inner">' . paginate_links(
            array(
                'base' => @add_query_arg('page', '%#%'),
                'total' => $wp_query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
                'format' => '?page=%#%',
                'show_all' => false,
                'type' => 'plain',
                'end_size' => 2,
                'mid_size' => 1,
                'prev_next' => true,
                'prev_text' => sprintf('%1$s', __('«', 'text-domain')),
                'next_text' => sprintf('%1$s', __('»', 'text-domain')),
                'add_args' => false,
                'add_fragment' => '',
            )
        ) . '</div></div>';
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function qs_get_product_net_revenue($product_id)
{
    global $wpdb;

    $value = (float) $wpdb->get_var($wpdb->prepare("
            SELECT SUM(o.product_net_revenue) 
            FROM {$wpdb->prefix}wc_order_product_lookup o 
            INNER JOIN {$wpdb->prefix}wc_orders o2
                ON o.order_id = o2.id
            WHERE o2.status = 'wc-completed'
                AND o.product_id = %d
        ", $product_id));

        
    $init_value = get_post_meta($product_id, 'init_value', true);
    if(!$init_value || !is_numeric($init_value))$init_value = 0;

    return $value + $init_value;
}

function qs_get_product_gross_revenue($product_id)
{
    global $wpdb;

    return (float) $wpdb->get_var($wpdb->prepare("
            SELECT SUM(product_gross_revenue)
            FROM {$wpdb->prefix}wc_order_product_lookup
            WHERE product_id = %d
        ", $product_id));
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('init', function () {
    $labels = array(
        'name' => qs::_('Projects'),
        'singular_name' => qs::_('Project'),
        'menu_name' => qs::_('Projects'),
        'all_items' => qs::_('All Projects'),
        'parent_item' => qs::_('Parent Project'),
        'parent_item_colon' => qs::_('Parent Project') . ':',
        'new_item_name' => qs::_('New Project Name'),
        'add_new_item' => qs::_('Add New Project'),
        'edit_item' => qs::_('Edit Project'),
        'update_item' => qs::_('Update Project'),
        'separate_items_with_commas' => qs::_('Separate Projects with commas'),
        'search_items' => qs::_('Search Projects'),
        'add_or_remove_items' => qs::_('Add or remove Projects'),
        'choose_from_most_used' => qs::_('Choose from the most used Projects'),
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
    );
    register_taxonomy('project', 'product', $args);
    register_taxonomy_for_object_type('project', 'product');
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('woocommerce_after_cart_item_name', function ($cart_item, $cart_item_key) {
    $product_id = isset($cart_item['product_id']) ? $cart_item['product_id'] : 0;
    $notes = isset($cart_item['notes']) ? $cart_item['notes'] : '';
    $is_gift = isset($cart_item['is_gift']) ? $cart_item['is_gift'] : false;

    if ($is_gift) {
        $sendtoname = isset($cart_item['sendtoname']) ? $cart_item['sendtoname'] : '';
        $sendtophone = isset($cart_item['sendtophone']) ? $cart_item['sendtophone'] : '';
        $sendername = isset($cart_item['sendername']) ? $cart_item['sendername'] : '';
        echo '<style>
            .g-table{
                display: block;
                border: solid 1px #84848442;
                border-radius: 5px;
                margin: 20px 0;
                box-shadow: 0 0 5px #00000017;
                background-color: #81b199;
                color: white;
            }
            .g-tr{
                display: flex;
                width: 100%;
                border-bottom: solid 1px #ffffff36;
                padding: 10px;
                box-sizing: border-box;
            }
            .g-th{
                width: 100px;
                text-align: right;
            }
            </style>
            <div>
            <div class="g-table">
                <div class="g-tr"><div class="g-th">المهدى إليه</div><div class="g-td">' . $sendtoname . '</div></div>
                <div class="g-tr"><div class="g-th">رقم الجوال</div><div class="g-td">' . $sendtophone . '</div></div>
                <div class="g-tr"><div class="g-th">إسم المهدي</div><div class="g-td">' . $sendername . '</div></div>
            </div>
            </div>';
    }
}, 10, 2);


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('init', function () {
    if (!isset($_GET['checkproducts']))
        return;
    $product_ids = $_GET['checkproducts'];
    $product_ids = explode(',', trim($product_ids, ','));
    foreach ($product_ids as $key => $product_id) {
        if (!is_numeric($product_id)) {
            unset($product_ids[$key]);
            continue;
        }
        $val = 'Amin[' . $product_id . ']';
        $has_custom_donate = get_post_meta($product_id, 'has_custom_donate', true);
        $custom_donate = get_post_meta($product_id, 'custom_donate', true);
        $price = 0;
        if (function_exists('wc_get_product')) {
            $product = wc_get_product($product_id);
            $product->get_regular_price();
            $product->get_sale_price();
            $price = $product->get_price();
        }

        if (!$custom_donate) {
            $custom_donate = json_encode(array());
        }
        $custom_donate = json_decode($custom_donate, true);
        $products[] = array(
            'product_id' => $product_id,
            'val' => $val,
            'has_custom_donate' => $has_custom_donate ? 1 : 0,
            'custom_donate' => $custom_donate,
            'price' => $price,
        );
    }

    print_r(json_encode($products));
    exit;
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('init', function () {
    // echo 'bb';exit;
    if (!isset($_GET['sendgift']))
        return;
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $sendtoname = isset($_POST['sendtoname']) ? $_POST['sendtoname'] : '';
    $sendtophone = isset($_POST['sendtophone']) ? $_POST['sendtophone'] : '';
    $sendername = isset($_POST['sendername']) ? $_POST['sendername'] : '';
    $err = '';
    if (!$product_id)
        $err .= ' - حدث خطأ في رقم المنتج' . "\n";
    if (!$price)
        $err .= ' - يجب إدخال سعر للهدية' . "\n";
    if (!$sendtoname)
        $err .= ' - يرجى إدخال إسم المهدى إليه' . "\n";
    if (!$sendtophone)
        $err .= ' - يرجى إدخال رقم جوال المهدى إليه' . "\n";
    if (!$sendername)
        $err .= ' - يرجى إدخال إسمك' . "\n";

    if ($err) {
        print_r($err);
        exit;
    }
    // echo 'b';exit;

    $cart_item_data = array(
        'custom_price' => $price,
        'is_gift' => 1,
        'sendtoname' => $sendtoname,
        'sendtophone' => $sendtophone,
        'sendername' => $sendername,
    );

    WC()->cart->add_to_cart($product_id, 1, 0, array(), $cart_item_data);
    echo 1;
    exit;
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('init', function () {
    if (!isset($_GET['tuning_gift']) || !$_GET['tuning_gift'])
        return;

    if(!(current_user_can('administrator') || current_user_can('editor')))
        return;

    $thumbnail_dir = $_GET['image_dir'] ?? false;
    if (!$thumbnail_dir || !file_exists($thumbnail_dir)) {
        echo 'The thumbnail not found';
        exit;
    }

    preview_text_on_image();
    exit;
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('init', function () {
    if (!isset($_GET['save_tuning']))
        return;

    if(!(current_user_can('administrator') || current_user_can('editor')))
        return;

    $product_id = $_POST['product_id'] ?? 1;

    if (!$product_id || !get_post_type($product_id)) {
        print_r(json_encode(['ok' => false]));
        exit;
    }

    $tuning_params = array_map('trim', explode(',', '
            product_id,
            image_dir,
            name_1,
            name_1_color,
            name_1_font_size,
            name_1_right,
            name_1_top,
            name_1_font,
            name_2,
            name_2_color,
            name_2_font_size,
            name_2_right,
            name_2_top,
            name_2_font
        '));

    $gift_tuning = array();
    foreach ($tuning_params as $key) {
        $gift_tuning[$key] = $_POST[$key] ?? '';
    }
    update_post_meta($product_id, 'gift_tuning', $gift_tuning);

    // $data = array($product_id);
    print_r(json_encode(['ok' => true]));
    exit;
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    if (isset($values['sendtophone'])) {
        $order->update_meta_data('has_gift', '1');
        $order->update_meta_data('gift', array(
            'to_name' => $values['sendtoname'],
            'phone' => $values['sendtophone'],
            'from_name' => $values['sendername'],
        ));
        $order->save();
    }
}, 10, 4);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('woocommerce_order_status_changed', 'my_custom_function', 10, 3);
function my_custom_function($order_id, $old_status, $new_status) {
    // Get the order object
    $order = wc_get_order($order_id);
    
    $has_gift = $order->get_meta('has_gift');
    $gift = $order->get_meta('gift');

    if(!$has_gift)return;

    // Check if the order is completed for the first time
    if ($new_status === 'completed' && (!$order->get_date_completed() || true)) {
        // This will be executed when the order status changes to completed for the first time

        // Loop through order items
        foreach ($order->get_items() as $item_id => $item) {
            // Get the product ID for the current item
            $product_id = $item->get_product_id();
            $image_filename = $order_id . '_' . $item_id. '.jpg';
            save_gift($product_id, $gift['from_name'], $gift['to_name'], $image_filename);
        }

    }
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('woocommerce_order_details_before_order_table', 'my_custom_thankyou_message');

function my_custom_thankyou_message($order)
{
    // Get the order object
    $order_id = $order->get_id();
    
    $has_gift = $order->get_meta('has_gift');
    $gift = $order->get_meta('gift');

    if(!$has_gift)return;

    // Get the order status
    $status = $order->get_status();

    // Check if the order is completed
    if ('completed' === $status) {

        // Initialize the table
        $table = '<table style="border-collapse: collapse; width: 100%;">
                      <thead>
                          <tr>
                              <th style="border: 1px solid #ccc; padding: 10px;">إسم المنتج</th>
                              <th style="border: 1px solid #ccc; padding: 10px;">رابط التحميل</th>
                          </tr>
                      </thead>
                      <tbody>';


        // Loop through order items
        foreach ($order->get_items() as $item_id => $item) {
            // Get the product ID for the current item
            $product_id = $item->get_product_id();

            // Get the quantity for the current item
            $quantity = $item->get_quantity();

            // Get the item total for the current item
            $total = $item->get_total();

            // Generate the custom link
            $link = home_url() . '/gifts/' . $order_id . '_' . $item_id . '.jpg';

            // Get the product name for the current item
            $product_name = $item->get_name();

            // Add a new row to the table
            $table .= '<tr>
                          <td style="border: 1px solid #ccc; padding: 10px;">' . $product_name . (($quantity > 1) ? ' (×' . $quantity . ')' : '') . '</td>
                          <td style="border: 1px solid #ccc; padding: 10px;"><a target="_blank" href="' . $link . '" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; border-radius: 4px; font-size: 16px;">تحميل الآن</a></td>
                      </tr>';
        }

        // Close the table
        $table .= '</tbody>
                   </table>';

        // Display the table
        echo '<div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
                  <p style="font-size: 20px;">نشكرك على الطلب!</p>
                  <p style="font-size: 16px;">تم إرسال الهدية لرقم الهاتف ('.($gift['phone'] ?? '').').. وهنا أيضا رابط التحميل:</p>
                  ' . $table . '
              </div>';
    }else{
        echo '<div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
                  <p style="font-size: 20px;">شكرا لك على الطلب!</p>
                  <p style="font-size: 16px;">سيتم إرسال الهدية وسيظهر رابط التحميل هنا فور التحقق من الطلب في أقرب وقت</p>
              </div>';
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('init', function () {
    if (!isset($_GET['p']))
        return;
    if (!$_GET['p'] || !is_numeric($_GET['p']))
        return;
    header('location: ' . get_permalink($_GET['p']));
    exit;
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_head', function () {
    require_once QS_DIR . '/html/loader-style.html';
    require_once QS_DIR . '/html/style.html';
    require_once QS_DIR . '/html/loader-html.html';
    $script = file_get_contents(QS_DIR . '/html/script.html');
    $script = str_replace('[rand]', rand(), $script);
    echo $script;

    $cart_url = '';
    if (function_exists('wc_get_cart_url'))
        $cart_url = wc_get_cart_url();

    ?>
    <input type="hidden" class="home_url" value="<?= home_url() ?>">
    <input type="hidden" class="cart_url" value="<?= $cart_url ?>">
    <?php
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// send sms message after payment complete

add_action('woocommerce_payment_complete', '_wc_payment_complete_sendsms', 10, 3);
add_action('woocommerce_order_status_changed', function ($order_id, $old_status, $new_status) {
    if ($new_status == 'completed') {
        _wc_payment_complete_sendsms($order_id);
    }
}, 10, 3);
function _wc_payment_complete_sendsms($order_id)
{

    $order = wc_get_order($order_id);
    // Allow code execution only once 
    if (!get_post_meta($order_id, '_wc_payment_complete_sendsms', true)) {

        $msg = 'شكرا لكم تم قبول تبرعكم في ' . get_bloginfo('name') . ' ، رقم الطلب الخاص بك هو:' . $order_id;
        sendMessage($msg, $order->billing_phone);

        $order->update_meta_data('_wc_payment_complete_sendsms', true);
    }

}



add_action('woocommerce_order_status_failed', function ($order_id, $order) {
    $order = wc_get_order($order_id);
    // Allow code execution only once 
    if (!get_post_meta($order_id, '_wc_payment_failed_sendsms', true)) {

        $msg = 'نأسف، فشل التبرع الخاص بكم في ' . get_bloginfo('name') . '.. نرجو إعادة المحاولة';
        sendMessage($msg, $order->billing_phone);

        $order->update_meta_data('_wc_payment_failed_sendsms', true);
    }
}, 15, 2);


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// send sms message

function sendMessage($msg, $number)
{

    $sms_settings = get_option('qs_sms_settings');
    $sms_provider = $sms_settings['sms_provider'] ?? 'vip1sms';

    if ($sms_provider == 'oursms') {
        $result = sendMessage_Oursms($msg, $number);
    } else {
        $result = sendMessage_vip1sms($msg, $number);
    }
    return $result;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Oursms API

function sendMessage_Oursms($messageText, $phoneNumbers)
{
    // Set API endpoint URL
    $url = 'https://api.oursms.com/msgs/sms';

    // Set headers
    $headers = array(
        'Content-Type: application/json'
    );

    $sms_settings = get_option('qs_sms_settings');

    $username = $sms_settings['oursms_username'] ?? '';
    $apiToken = $sms_settings['oursms_api_token'] ?? '';
    $sender = $sms_settings['oursms_sender'] ?? '';

    // If phone numbers is a string, convert to array with a single value
    if (is_string($phoneNumbers)) {
        $phoneNumbers = array($phoneNumbers);
    }

    // Format phone numbers to required format
    $formattedPhoneNumbers = array();
    foreach ($phoneNumbers as $phoneNumber) {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber); // Remove non-numeric characters
        if (strlen($phoneNumber) == 10) { // If the phone number has 10 digits, assume it's a Saudi Arabia number
            $phoneNumber = '966' . $phoneNumber; // Add country code
        }
        $formattedPhoneNumbers[] = preg_replace('/96605/', '9665', $phoneNumber, 1);
    }

    $formattedPhoneNumbers = implode(',', $formattedPhoneNumbers);

    $url = 'https://api.oursms.com/api-a/msgs/?username=' . urlencode($username) . '&token=' . urlencode($apiToken) . '&src=' . urlencode($sender) . '&dests=' . $formattedPhoneNumbers . '&body=' . urlencode($messageText) . '&priority:0&delay=0&validity=0&maxParts=0&dlr=0&prevDups=0';
    return file_get_contents($url);
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// vip1sms API

function sendMessage_vip1sms($msg, $number)
{

    $number = str_replace(' ', '', $number);
    $number = str_replace('-', '', $number);
    $number = str_replace('+', '', $number);
    $number = ltrim(trim($number), '0');

    if (strpos($number, '96605') === 0) {
        $number = '9665' . ltrim($number, '96605');
    }

    $sms_settings = get_option('qs_sms_settings');

    $username = $sms_settings['vip1sms_username'] ?? '';
    $password = $sms_settings['vip1sms_password'] ?? '';
    $sender = $sms_settings['vip1sms_sender'] ?? '';

    // random number parameter used to avoid cashing if the same message sent tiwce
    $url = "http://www.vip1sms.com/smartsms/api/sendsms.php?username=$username&password=$password&sender=$sender&message=" . urlencode($msg) . "&numbers=" . urlencode($number) . "&unicode=u&return=full&rand_number=" . rand(100000, 9999999);

    $result = file_get_contents($url);
    return $result;
}

add_action('init', function () {
    if (!isset($_GET['testsend']))
        return;
    $r = sendMessage($_GET['msg'], $_GET['to']);
    print_r($r);
    exit;
});


add_action('wp_footer', 'callback_wp_footer');
function callback_wp_footer()
{
    ?>
    <script type="text/javascript">
        (function ($) {
            $(document.body).on('updated_checkout', function (data) {
                var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>",
                    country_code = $('#billing_country').val();

                var ajax_data = {
                    action: 'append_country_prefix_in_billing_phone',
                    country_code: $('#billing_country').val()
                };

                $.post(ajax_url, ajax_data, function (response) {
                    $('#billing_phone').val(response);
                });
            });
        })(jQuery);
    </script>
    <?php
}

add_action('wp_ajax_nopriv_append_country_prefix_in_billing_phone', 'country_prefix_in_billing_phone');
add_action('wp_ajax_append_country_prefix_in_billing_phone', 'country_prefix_in_billing_phone');
function country_prefix_in_billing_phone()
{
    $calling_code = '';
    $country_code = isset($_POST['country_code']) ? $_POST['country_code'] : '';
    if ($country_code) {
        $calling_code = WC()->countries->get_country_calling_code($country_code);
        $calling_code = is_array($calling_code) ? $calling_code[0] : $calling_code;

    }
    echo $calling_code;
    die();
}

add_action('woocommerce_after_checkout_form', function () {
    echo '<style>#billing_phone_field input#billing_phone {direction: ltr;}</style>';
}, 10);

add_filter( 'woocommerce_add_to_cart_redirect', 'redirect_to_checkout_skip_cart' );
function redirect_to_checkout_skip_cart() {
    return wc_get_checkout_url();
}

