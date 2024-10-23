<?php
/*
Plugin Name: Dealer Locator Popup
Description: This is a Custom Plugin to add Popup Function for Locate Dealer button.
Version: 1.0
Author: DigitCave - Kavindu N.
Plugin URI: https://digitcave.com
*/

// Function to add the "Locate a Dealer" button and popup HTML
function add_dealer_locator_popup() {
    if ( is_product() ) {
        // Button HTML
        echo '<a href="#" class="button popup-button" style="display:block;margin-top:10px;background-color:#0044B0;color:white;padding:10px;text-align:center;">Locate a Dealer</a>';
        
        // Popup HTML
        ?>
        <div id="popup-overlay" class="popup-overlay">
            <div class="popup-content">
                <span class="popup-close">&times;</span>
                <header class="dealer-header">
					<h1><b>CHOOSE A DEALERSHIP</b></h1>
                </header>
                <section class="dealerships">
                    <!-- Dealership 1 -->
                    <div class="dealership">
						<h3><b>Max Built Trailers  - Covington</b></h3>
                        <p>10945 US-278<br>Covington, GA 30015<br>678.350.1555</p>
                        <div class="button-container">
                            <button class="contact-form-btn">CONTACT FORM</button>
                            <button class="call-now-btn">CALL NOW</button>
                        </div>
                    </div>
                    <!-- Dealership 2 -->
                    <div class="dealership">
                        <h3><b>Max Built Trailers - Chatsworth</b></h3>
                        <p>1631 Leonard Bridge Rd.<br>Chatsworth, GA 30705<br>706.971.2233</p>
                        <div class="button-container">
                            <button class="contact-form-btn">CONTACT FORM</button>
                            <button class="call-now-btn">CALL NOW</button>
                        </div>
                    </div>
                    <!-- Dealership 3 -->
                    <div class="dealership">
						<h3><b>Max Built Trailers - Midland</b></h3>
                        <p>420 Hwy 24/27<br>Midland, NC<br>980.219.9157</p>
                        <div class="button-container">
                            <button class="contact-form-btn">CONTACT FORM</button>
                            <button class="call-now-btn">CALL NOW</button>
                        </div>
                    </div>
                    <!-- Dealership 4 -->
                    <div class="dealership">
						<h3><b>Max Built Trailers - Morristown</b></h3>
                        <p>4581 W. Andrew Johnson Hwy<br>Morristown, TN<br>423.353.4278</p>
                        <div class="button-container">
                            <button class="contact-form-btn">CONTACT FORM</button>
                            <button class="call-now-btn">CALL NOW</button>
                        </div>
                    </div>
                    <!-- Dealership 5 -->
                    <div class="dealership">
						<h3><b>Max Built Trailers - Chattanooga</b></h3>
                        <p>5721 Lee Highway</p>
						<p>
							Chattanooga, TN 37421
						</p>
                        <div class="button-container">
                            <button class="contact-form-btn">CONTACT FORM</button>
                            <button class="call-now-btn">CALL NOW</button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php
    }
}
add_action( 'woocommerce_single_product_summary', 'add_dealer_locator_popup', 21 );

// Enqueue CSS and JavaScript
function dealer_locator_popup_assets() {
    if ( is_product() ) {
        // Enqueue CSS
        wp_enqueue_style( 'dealer-locator-popup-css', plugin_dir_url( __FILE__ ) . 'style.css' );

        // Enqueue JavaScript
        wp_enqueue_script( 'dealer-locator-popup-js', plugin_dir_url( __FILE__ ) . 'popup.js', array('jquery'), null, true );
    }
}
add_action( 'wp_enqueue_scripts', 'dealer_locator_popup_assets' );

// Localize the script with AJAX URL and security nonce
function dealer_locator_popup_localize_script() {
    if ( is_product() ) {
        wp_localize_script('dealer-locator-popup-js', 'contactFormAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('contact_form_nonce'),
            'product_name' => get_the_title() // Pass product name to JS
        ));
    }
}
add_action('wp_enqueue_scripts', 'dealer_locator_popup_localize_script');



// Mail Handling
// Handle AJAX request to send form data
function send_contact_form_ajax_handler() {
    // Check security nonce
    check_ajax_referer('contact_form_nonce', 'security');

    // Get form data
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $phone = sanitize_text_field($_POST['phone']);
    $email = sanitize_email($_POST['email']);
    $address = sanitize_text_field($_POST['address']);
    $message = sanitize_textarea_field($_POST['message']);
    $dealer_location = sanitize_text_field($_POST['dealer_location']);
    $product_name = sanitize_text_field($_POST['product_name']); // Get product name from the form

    // Prepare email
    $to = 'Hirefoo@gmail.com'; // Replace with your email address
    $subject = 'New Contact Form Submission';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $body = "<h1>New Dealer Contact Request</h1>
             <p><strong>First Name:</strong> $first_name</p>
             <p><strong>Last Name:</strong> $last_name</p>
             <p><strong>Phone:</strong> $phone</p>
             <p><strong>Email:</strong> $email</p>
             <p><strong>Address:</strong> $address</p>
             <p><strong>Dealer Location:</strong> $dealer_location</p>
             <p><strong>Product Name:</strong> $product_name</p> <!-- Include product name -->
             <p><strong>Message:</strong> $message</p>";

    // Send the email
    $mail_sent = wp_mail($to, $subject, $body, $headers);

    if ($mail_sent) {
        wp_send_json_success('Email sent successfully.');
    } else {
        wp_send_json_error('Failed to send email.');
    }
}
add_action('wp_ajax_send_contact_form', 'send_contact_form_ajax_handler');
add_action('wp_ajax_nopriv_send_contact_form', 'send_contact_form_ajax_handler');
