<?php
/*
Plugin Name: vCard Generator
Description: A plugin for uploading vCards and displaying information.
Version: 1.0
Author: Your Name
*/

// Activation hook
function activate_vcard_uploader() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dm_vcards';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
        logo_url VARCHAR(255),
        header_bg_url VARCHAR(255),
        qr_code_url VARCHAR(255),
        name VARCHAR(255),
        job_title VARCHAR(255),
        short_description TEXT,
        phone_number VARCHAR(20),
        email_address VARCHAR(255),
        website_url VARCHAR(255),
        location VARCHAR(255),
        facebook_link VARCHAR(255),
        twitter_link VARCHAR(255),
        instagram_link VARCHAR(255),
        linkedin_link VARCHAR(255),
        youtube_link VARCHAR(255),
        whatsapp_link VARCHAR(255),
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'activate_vcard_uploader');

// Enqueue scripts and styles
function vcard_uploader_admin_scripts() {
    if (isset($_GET['page']) && ($_GET['page'] == 'vcard-uploader' || $_GET['page'] == 'vcard-list')) {
        wp_enqueue_media();
        wp_enqueue_script('vcard-uploader', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
        
        // Enqueue your custom CSS file
        wp_enqueue_style('vcard-styles', plugin_dir_url(__FILE__) . 'css/style.min.css'); // Adjust the path to your CSS file
    }
}
add_action('admin_enqueue_scripts', 'vcard_uploader_admin_scripts');


function enqueue_custom_styles() {
    wp_enqueue_style('custom-style', plugins_url('/css/style.min.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_styles');

function enqueue_font_awesome() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
}

add_action('wp_enqueue_scripts', 'enqueue_font_awesome');

// Admin menu
function vcard_uploader_menu() {
    add_menu_page('Vcard Generator', 'Vcard Generator', 'manage_options', 'vcard-uploader', 'vcard_uploader_admin_page', 'dashicons-id');
}
add_action('admin_menu', 'vcard_uploader_menu');

// Admin page
function vcard_uploader_admin_page() {
    global $wpdb;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_vcard'])) {
        // Generate a random and unique user ID
        $user_id = generate_unique_user_id();

        // Process and save data
        $vcard_data = array(
            'user_id' => $user_id,
            'name' => sanitize_text_field($_POST['name']),
            'job_title' => sanitize_text_field($_POST['job_title']),
            'short_description' => sanitize_text_field($_POST['short_description']),
            'phone_number' => sanitize_text_field($_POST['phone_number']),
            'email_address' => sanitize_text_field($_POST['email_address']),
            'website_url' => esc_url($_POST['website_url']),
            'location' => sanitize_text_field($_POST['location']),
            'facebook_link' => esc_url($_POST['facebook_link']),
            'twitter_link' => esc_url($_POST['twitter_link']),
            'instagram_link' => esc_url($_POST['instagram_link']),
            'linkedin_link' => esc_url($_POST['linkedin_link']),
            'youtube_link' => esc_url($_POST['youtube_link']),
            'whatsapp_link' => esc_url($_POST['whatsapp_link']),
        );

        // Handle logo upload
        if (!empty($_POST['logo_id'])) {
            $logo_id = intval($_POST['logo_id']);
            $vcard_data['logo_url'] = wp_get_attachment_url($logo_id);
        }

        // Handle header background image upload
        if (!empty($_POST['header_bg_id'])) {
            $header_bg_id = intval($_POST['header_bg_id']);
            $vcard_data['header_bg_url'] = wp_get_attachment_url($header_bg_id);
        }

        // Handle QR code image upload
        if (!empty($_POST['qr_code_id'])) {
            $qr_code_id = intval($_POST['qr_code_id']);
            $vcard_data['qr_code_url'] = wp_get_attachment_url($qr_code_id);
        }

        $table_name = $wpdb->prefix . 'dm_vcards';

        // Insert data into the database
        $wpdb->insert($table_name, $vcard_data);
        echo 'vCard uploaded successfully!';
    }

    // Display the form
    echo '<div class="" id="dm_vcard_generator_area">';
    echo '<h2>Vcard Generator</h2>';
    
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<div id="dm_vcard_generator_upload_form">';

    echo '<div>';
    echo '<label for="name">Name:</label>';
    echo '<input type="text" name="name" id="name" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="job_title">Job Title:</label>';
    echo '<input type="text" name="job_title" id="job_title" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="short_description">Short Description:</label>';
    echo '<input type="text" name="short_description" id="short_description" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="phone_number">Phone Number:</label>';
    echo '<input type="text" name="phone_number" id="phone_number" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="email_address">Email Address:</label>';
    echo '<input type="text" name="email_address" id="email_address" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="website_url">Website URL:</label>';
    echo '<input type="text" name="website_url" id="website_url" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="location">Location:</label>';
    echo '<input type="text" name="location" id="location" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="facebook_link">Facebook:</label>';
    echo '<input type="text" name="facebook_link" id="facebook_link" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="twitter_link">Twitter:</label>';
    echo '<input type="text" name="twitter_link" id="twitter_link" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="instagram_link">Instagram:</label>';
    echo '<input type="text" name="instagram_link" id="instagram_link" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="linkedin_link">LinkedIn:</label>';
    echo '<input type="text" name="linkedin_link" id="linkedin_link" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="youtube_link">YouTube:</label>';
    echo '<input type="text" name="youtube_link" id="youtube_link" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="whatsapp_link">WhatsApp:</label>';
    echo '<input type="text" name="whatsapp_link" id="whatsapp_link" /><br>';
    echo '</div>';

    echo '<div>';
    echo '<label for="logo_id">Logo:</label>';
    echo '<input type="button" class="button" id="select-logo" value="Select Logo">';
    echo '<input type="hidden" name="logo_id" id="logo_id" />';
    echo '</div>';

    echo '<div>';
    // Header Background Image Upload
    echo '<label for="header_bg">Header Background:</label>';
    echo '<input type="button" class="button" id="select-header-bg" value="Select Image">';
    echo '<input type="hidden" name="header_bg_id" id="header_bg_id" />';
    echo '</div>';

    echo '<div>';
    // QR Code Image Upload
    echo '<label for="qr_code">QR Code:</label>';
    echo '<input type="button" class="button" id="select-qr-code" value="Select Image">';
    echo '<input type="hidden" name="qr_code_id" id="qr_code_id" />';
    echo '</div>';

    echo '</div>';

    echo '<input id="submit_btn" type="submit" name="upload_vcard" value="Upload vCard" />';
    echo '</form>';
    echo '</div>';
    echo '</div>';
}

// Generate a random and unique user ID with 6 characters
function generate_unique_user_id() {
    $user_id = mt_rand(100000, 999999); // Generate a random 6-digit number
    global $wpdb;
    $table_name = $wpdb->prefix . 'dm_vcards';

    // Check if the generated user ID already exists, if so, generate a new one
    while ($wpdb->get_var($wpdb->prepare("SELECT user_id FROM $table_name WHERE user_id = %d", $user_id))) {
        $user_id = mt_rand(100000, 999999);
    }

    return $user_id;
}

// Shortcode to display vCard information
function vcard_shortcode($atts) {
    global $wpdb;

    $user_id = $atts['user_id'];
    $table_name = $wpdb->prefix . 'dm_vcards';
    $vcard_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id), ARRAY_A);

    if ($vcard_data) {
        $output = '<div id="dm_vcard_box">';
        // header area start
        $output .= '<div class="dm_header_area" style="background-image: linear-gradient(45deg, #19244cb5, #017ca1c2), url(\'' . esc_url($vcard_data['header_bg_url']) . '\');">';
        $output .= '<div class="dm_logo_box">';
                // Display logo if available
                if (!empty($vcard_data['logo_url'])) {
                    $output .= '<img class="dm_vcard_logo" src="' . esc_url($vcard_data['logo_url']) . '" alt="Logo">';
                }
        
        $output .= '</div>';
        // header info box
        $output .= '<div class="dm_header_info_box">';
        $output .= '<h2 class="title">' . esc_html($vcard_data['name']) . '</h2>';
        $output .= '<p class="designation">' . esc_html($vcard_data['job_title']) . '</p>';
        $output .= '<p class="short_desc">' . esc_html($vcard_data['short_description']) . '</p>';
        $output .= '</div>';
        
        $output .= '</div>';
        // header area end
        
        $output .= '<div class="dm_contact_area">';
        $output .= '<ul>';
        // Display phone number if available
        $output .= '<li>';
        if (!empty($vcard_data['phone_number'])) {
            $output .= '<span>';
            $output .= '<i class="fas fa-phone">';
            $output .= '</i>';
            $output .= '</span>';
            $output .= '<div>Phone: <a href="tel:' . esc_html($vcard_data['phone_number']) . '">' . esc_html($vcard_data['phone_number']) . '</a></div>';
        }
        $output .= '</li>';
        // Display email address if available
        $output .= '<li>';
        if (!empty($vcard_data['email_address'])) {
            $output .= '<span>';
            $output .= '<i class="fas fa-envelope">';
            $output .= '</i>';
            $output .= '</span>';
            $output .= '<div>Email: <a href="mailto:' . sanitize_email($vcard_data['email_address']) . '">' . sanitize_email($vcard_data['email_address']) . '</a></div>';
        }
        $output .= '</li>';
        // Display website if available
        $output .= '<li>';
        if (!empty($vcard_data['website_url'])) {
            $output .= '<span>';
            $output .= '<i class="fas fa-globe">';
            $output .= '</i>';
            $output .= '</span>';
            $output .= '<div>Website: <a href="' . esc_url($vcard_data['website_url']) . '" target="_blank">' . esc_html($vcard_data['website_url']) . '</a></div>';
        }
        $output .= '</li>';
        // Display location if available
        $output .= '<li>';

        if (!empty($vcard_data['location'])) {
            $output .= '<span>';
            $output .= '<i class="fas fa-location-arrow">';
            $output .= '</i>';
            $output .= '</span>';
            $output .= '<div>Location: <p>' . esc_html($vcard_data['location']) . '</p></div>';
        }
        $output .= '</li>';
        $output .= '</ul>';

        $output .= '</div>';
        
        // Display location if available
        

        // Process and display social links with labels as hyperlinks and Font Awesome icons
        $social_links = array(
            'Facebook' => array(
                'url' => $vcard_data['facebook_link'],
                'icon' => 'fab fa-facebook-f',
            ),
            'Twitter' => array(
                'url' => $vcard_data['twitter_link'],
                'icon' => 'fab fa-twitter',
            ),
            'Instagram' => array(
                'url' => $vcard_data['instagram_link'],
                'icon' => 'fab fa-instagram',
            ),
            'LinkedIn' => array(
                'url' => $vcard_data['linkedin_link'],
                'icon' => 'fab fa-linkedin-in',
            ),
            'YouTube' => array(
                'url' => $vcard_data['youtube_link'],
                'icon' => 'fab fa-youtube',
            ),
            'WhatsApp' => array(
                'url' => $vcard_data['whatsapp_link'],
                'icon' => 'fab fa-whatsapp',
            ),
        );

        $output .= '<div class="dm_social_area"> <h4>Connect With Me</h4> ';
        $output .= '<ul>';
        foreach ($social_links as $label => $data) {
            $url = esc_url($data['url']);
            $icon = esc_attr($data['icon']);
            
            $output .='<li>';
            if (!empty($url)) {
                $output .= '<a href="' . $url . '" target="_blank"><i class="' . $icon . '"></i></a>';
            }
            $output .='</li>';
        }
        $output .='</ul>';
        $output .= '</div>';


        // Display QR code if available
        $output .='<div class="dm_qr_code_area">';
        if (!empty($vcard_data['qr_code_url'])) {
            $output .= '<p></p>';
            $output .= "<h4>Scan Me<h4>";
            $output .= '<img src="' . esc_url($vcard_data['qr_code_url']) . '" alt="QR Code">';
            $output .= "<p>Share and install my Card 👉<p>";
            
        }
        $output .='</div>';

        $output .= '</div>';
        
        return $output;
    } else {
        return 'vCard not found.';
    }
}

add_shortcode('dm_vcard', 'vcard_shortcode');


// Submenu for vCard List
function vcard_list_submenu() {
    add_submenu_page(
        'vcard-uploader', // Parent menu slug
        'Vcard List', // Page title
        'Vcard List', // Menu title
        'manage_options',
        'vcard-list',
        'vcard_list_page'
    );
        add_submenu_page(
            'vcard-uploader', // Parent menu slug
            'Edit vCard',     // Page title
            'Edit vCard',     // Menu title
            'manage_options',
            'vcard-edit',     // Submenu slug
            'vcard_edit_page' // Callback function for the edit page
        ); 
}
add_action('admin_menu', 'vcard_list_submenu');

// Callback function for the vCard List submenu page
function vcard_list_page() {
    global $wpdb;

    // Display the list of uploaded vCards in a table
    $vcard_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}dm_vcards", ARRAY_A);
    echo '<div class="wrap">';
    echo '<h2>Vcard User List:</h2>';
    echo '<table class="wp-list-table widefat fixed">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>List Number</th>';
    echo '<th>Logo</th>';
    echo '<th>Name</th>';
    echo '<th>Job Title</th>';
    echo '<th>Shortcode</th>';
    echo '<th>Actions</th>'; // Add this line
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    $list_number = 1;
    foreach ($vcard_data as $data) {
        echo '<tr>';
        echo '<td>' . $list_number . '</td>';
        echo '<td>';
        if (!empty($data['logo_url'])) {
            echo '<img src="' . esc_url($data['logo_url']) . '" alt="Logo" width="50" height="50">';
        }
        echo '</td>';
        echo '<td>' . esc_html($data['name']) . '</td>';
        echo '<td>' . esc_html($data['job_title']) . '</td>';
        echo '<td>[dm_vcard user_id="' . esc_attr($data['user_id']) . '"]</td>';
        echo '<td>';
        // Add an "Edit" link for each vCard
        $edit_url = add_query_arg(array('page' => 'vcard-edit', 'user_id' => $data['user_id']), admin_url('admin.php'));
        echo '<a class="button" href="' . esc_url($edit_url) . '">Edit</a>';
        echo '</td>';
        // You can add more actions here as needed
        echo '</td>';
        echo '</tr>';
        $list_number++;
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

// Modify the vCard Edit Page to Retrieve User ID and Data
function vcard_edit_page() {
    global $wpdb;

    if (isset($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);
        $vcard_data = get_vcard_data_by_user_id($user_id);

        if ($vcard_data) {
            // Display an edit form populated with vCard data
            echo '<div class="edit_box">';
            echo '<h2>Edit vCard</h2>';
            echo '<form  method="post">';
            echo '<input type="hidden" name="user_id" value="' . esc_attr($user_id) . '">';

            // Add form fields for editing vCard data
            echo '<div>';
            echo '<label for="name">Name:</label>';
            echo '<input type="text" name="name" id="name" value="' . esc_attr($vcard_data['name']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="job_title">Job Title:</label>';
            echo '<input type="text" name="job_title" id="job_title" value="' . esc_attr($vcard_data['job_title']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="short_description">Short Description:</label>';
            echo '<input type="text" name="short_description" id="short_description" value="' . esc_attr($vcard_data['short_description']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="phone_number">Phone Number:</label>';
            echo '<input type="text" name="phone_number" id="phone_number" value="' . esc_attr($vcard_data['phone_number']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="email_address">Email Address:</label>';
            echo '<input type="text" name="email_address" id="email_address" value="' . esc_attr($vcard_data['email_address']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="website_url">Website URL:</label>';
            echo '<input type="text" name="website_url" id="website_url" value="' . esc_attr($vcard_data['website_url']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="location">Location:</label>';
            echo '<input type="text" name="location" id="location" value="' . esc_attr($vcard_data['location']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="facebook_link">Facebook:</label>';
            echo '<input type="text" name="facebook_link" id="facebook_link" value="' . esc_attr($vcard_data['facebook_link']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="twitter_link">Twitter:</label>';
            echo '<input type="text" name="twitter_link" id="twitter_link" value="' . esc_attr($vcard_data['twitter_link']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="instagram_link">Instagram:</label>';
            echo '<input type="text" name="instagram_link" id="instagram_link" value="' . esc_attr($vcard_data['instagram_link']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="linkedin_link">LinkedIn:</label>';
            echo '<input type="text" name="linkedin_link" id="linkedin_link" value="' . esc_attr($vcard_data['linkedin_link']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="youtube_link">YouTube:</label>';
            echo '<input type="text" name="youtube_link" id="youtube_link" value="' . esc_attr($vcard_data['youtube_link']) . '">';
            echo '</div>';

            echo '<div>';
            echo '<label for="whatsapp_link">WhatsApp:</label>';
            echo '<input type="text" name="whatsapp_link" id="whatsapp_link" value="' . esc_attr($vcard_data['whatsapp_link']) . '">';
            echo '</div>';

            // You can continue adding more fields for other vCard data here

            echo '<input type="submit" name="update_vcard" value="Update vCard">';
            echo '</form>';
            echo '</div>';
        } else {
            echo 'vCard not found for editing.';
        }
    } else {
        echo 'Invalid vCard.';
    }
}
// Add this in your plugin file

// Action hook for form submission
add_action('admin_init', 'update_vcard_data');

// Function to update vCard data
function update_vcard_data() {
    if (isset($_POST['update_vcard'])) {
        $user_id = intval($_POST['user_id']);
        $vcard_data = get_vcard_data_by_user_id($user_id);

        // Check if the vCard data exists
        if ($vcard_data) {
            // Update the vCard data based on the submitted form values
            $vcard_data['name'] = sanitize_text_field($_POST['name']);
            $vcard_data['job_title'] = sanitize_text_field($_POST['job_title']);
            $vcard_data['short_description'] = sanitize_text_field($_POST['short_description']);
            
            // Update other fields
            $vcard_data['phone_number'] = sanitize_text_field($_POST['phone_number']);
            $vcard_data['email_address'] = sanitize_text_field($_POST['email_address']);
            $vcard_data['website_url'] = esc_url($_POST['website_url']);
            $vcard_data['location'] = sanitize_text_field($_POST['location']);
            $vcard_data['facebook_link'] = esc_url($_POST['facebook_link']);
            $vcard_data['twitter_link'] = esc_url($_POST['twitter_link']);
            $vcard_data['instagram_link'] = esc_url($_POST['instagram_link']);
            $vcard_data['linkedin_link'] = esc_url($_POST['linkedin_link']);
            $vcard_data['youtube_link'] = esc_url($_POST['youtube_link']);
            $vcard_data['whatsapp_link'] = esc_url($_POST['whatsapp_link']);
            
            // Add more fields here
            
            // Update the database record
            update_vcard_data_by_user_id($user_id, $vcard_data);
        
            // Redirect to the vCard list page or show a success message
            wp_redirect(admin_url('admin.php?page=vcard-list'));
            exit;
        }
        
    }
}

// Function to update vCard data by user ID
function update_vcard_data_by_user_id($user_id, $data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dm_vcards';

    $wpdb->update(
        $table_name,
        $data,
        array('user_id' => $user_id)
    );
}

function get_vcard_data_by_user_id($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dm_vcards';
    
    $vcard_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id), ARRAY_A);
    
    return $vcard_data;
}

