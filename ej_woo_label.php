<?php
/*
Plugin Name: EJ WOO Label
Description: A powerful tool for create & print physical product labels with QR codes, barcodes, and product information in WooCommerce.
Plugin URI: https://elementorjet.ir/ej-woo-label-barcode-and-qr-generator-label-printing-plugin
Author: elementorjet.ir
Version: 1.1
Author URI: https://elementorjet.ir
*/

// Hook to add the menu item
add_action('admin_menu', 'ej_woo_label_menu');

function ej_woo_label_menu() {
    // Add a top-level menu item
    add_menu_page('ej_woo_label', 'ej_woo_label', 'manage_options', 'ej-woo-label', 'ej_woo_label_page');

    // Add a submenu item under the top-level menu
    add_submenu_page('ej-woo-label', 'Settings', 'Settings', 'manage_options', 'ej-woo-label-settings', 'ej_woo_label_settings_page');
}

// Function to display the form page
function ej_woo_label_page() {
    ?>
    <div class="wrap">
        <h2>ej_woo_label</h2>
        <form method="post" action="">

            <?php wp_nonce_field( 'ej_woo_label_nonce', 'ej_woo_label_nonce_field' ); ?>

            <div id="input-container">
                <label for="product_id_1">Product ID 1:</label>
                <input type="text" name="product_id_1" id="product_id_1" />
            </div>
            <button type="button" id="add-input">Add New Product ID</button>
            <input type="submit" name="submit_form" class="button button-primary" value="Submit">
        </form>

        <?php
        // Check if the form is submitted
        if ( isset( $_POST['submit_form'] ) && wp_verify_nonce( $_POST['ej_woo_label_nonce_field'], 'ej_woo_label_nonce' ) ) {

            // Print the input values with barcodes
            echo '<div id="printable-result" style="display: flex; flex-wrap: wrap;">';
            echo '<div class ="two_cl" style="margin: -8;">';

            // Counter for dynamically added inputs
            $counter = 1;
                    $options = get_option('ej_woo_label_settings');
                    $barcode_settings = isset($options['barcode_settings']) ? $options['barcode_settings'] : array();
                    
                    $barcode_format = isset($options['barcode_format']) ? $options['barcode_format'] : 'CODE128';
                    $barcode_height = isset($options['barcode_height']) ? $options['barcode_height'] : 30;
                    $barcode_width = isset($options['barcode_width']) ? $options['barcode_width'] : 0.8;

                    $barcode_margin_t = isset($options['barcode_margin_top']) ? $options['barcode_margin_top'] : 0;
                    $barcode_margin_b = isset($options['barcode_margin_bottom']) ? $options['barcode_margin_bottom'] : 0;
                    $barcode_margin_l = isset($options['barcode_margin_left']) ? $options['barcode_margin_left'] : 0;
                    $barcode_margin_r = isset($options['barcode_margin_right']) ? $options['barcode_margin_right'] : 0;
                    $barcode_margin = $barcode_margin_t.' px '.$barcode_margin_r.' px '.$barcode_margin_b.' px '.$barcode_margin_l.' px ';
                    
                    $barcode_background = isset($options['barcode_background']) ? $options['barcode_background'] : '#ffffff';
                    //$barcode_lineColor = isset($barcode_settings['barcode_line_color']) ? $barcode_settings['barcode_line_color'] : '#000000';
                    $barcode_lineColor = isset($options['barcode_line_color']) ? $options['barcode_line_color'] : '#000000';
                    $barcode_font = isset($options['barcode_font']) ? $options['barcode_font'] : 'monospace';
                    //$barcode_textAlign = isset($options['barcode_text_align']) ? $options['barcode_text_align'] : 'center';
                    $barcode_textPosition = isset($options['barcode_text_position']) ? $options['barcode_text_position'] : 'bottom';
                    $barcode_textMargin = isset($options['barcode_text_margin']) ? $options['barcode_text_margin'] : 2;
                    $barcode_fontSize = isset($options['barcode_font_size']) ? $options['barcode_font_size'] : 10;
                    $barcode_margin = isset($options['barcode_margin']) ? $options['barcode_margin'] : 10;
                    //$barcode_display_value = isset($options['barcode_display_value']) ? $options['barcode_display_value'] : 0;
                    $barcode_display_value = $options['barcode_display_value'];
                    if (!$barcode_display_value) $barcode_display_value = 0;
                    //$barcode_display_value = true;
                    
                    $qr_width = isset($options['qr_width']) ? $options['qr_width'] : 40; 
                    $qr_padding = isset($options['qr_padding']) ? $options['qr_padding'] : 5; 
                    $qr_ecc = isset($options['qr_ecc']) ? $options['qr_ecc'] : 'l'; 
                    $qr_color = isset($options['qr_color']) ? $options['qr_color'] : '#000000'; 
                    $qr_bgcolor = isset($options['qr_bgcolor']) ? $options['qr_bgcolor'] : '#ffffff'; 
            
                    $show_name = $options['show_name'];
                    if (!$show_name) $show_name = 0;
                    $padding = isset($options['padding']) ? $options['padding'] : 0;
                    $font_size = isset($options['font_size']) ? $options['font_size'] : 12;
                    $font_family = isset($options['font_family']) ? $options['font_family'] : 'auto';
                    $color = isset($options['color']) ? $options['color'] : '#000000';
                    $line_height = isset($options['line_height']) ? $options['line_height'] : 0;
                    $show_price = $options['show_price'];
                    if (!$show_price) $show_price = 0;

            while ( isset( $_POST['product_id_' . $counter] ) ) {
                $input_name = 'product_id_' . $counter;
                $product_id = isset( $_POST[$input_name] ) ? sanitize_text_field( $_POST[$input_name] ) : '';


                // Check if the product ID is found
                if ($product_id) {
                    // Get the product link
                    $product_link = get_permalink($product_id);
                    $product_ = wc_get_product($product_id);
                    $product_name = $product_->get_name(); 
                    $product_regular_price = $product_->get_regular_price(); 
                    $product_sale_price = $product_->get_sale_price();

                    echo '<div class="result-item" style="flex: 0 0 calc(33.33% - 20px); width: fit-content; margin-top: 5.9px;padding-left: 18px ;  box-sizing: border-box; display: flex; align-items: center; flex-direction: column;">';
                    echo '<div style="display: flex; align-items: center;">';
                    echo '<svg id="barcode_' . esc_html($counter) . '"></svg>';
                    echo '<script src="' . esc_html(plugin_dir_url(__FILE__)) . 'JsBarcode.all.min.js"></script>';
                    // Fetching settings for barcode generation

                    $qr_width = isset($options['qr_width']) ? $options['qr_width'] : 40; 
                    $qr_padding = isset($options['qr_padding']) ? $options['qr_padding'] : 5; 
                    $qr_ecc = isset($options['qr_ecc']) ? $options['qr_ecc'] : 'l'; // 
                    $qr_color = isset($options['qr_color']) ? $options['qr_color'] : '#000000'; // 
                    $qr_bgcolor = isset($options['qr_bgcolor']) ? $options['qr_bgcolor'] : '#ffffff'; // 

                    echo '<script>JsBarcode("#barcode_' . esc_html($counter) . '", "' . esc_html($product_id) . '", { format: "' . esc_html($barcode_format) . '", height: ' . esc_html($barcode_height) . ', width: ' . esc_html($barcode_width) . ', displayValue:'.esc_html($barcode_display_value)  . ', margin: ' . esc_html($barcode_margin) . ', background: "' . esc_html($barcode_background) . '", lineColor: "' . esc_html($barcode_lineColor) . '", font: "' . esc_html($barcode_font) . '", textAlign: "' . 'center' . '", textPosition: "' . esc_html($barcode_textPosition) . '", textMargin: ' . esc_html($barcode_textMargin) . ', fontSize: ' . esc_html($barcode_fontSize) . ', margin: ' . esc_html($barcode_margin) . '});</script>';

                    echo '<img style="width: ' . esc_html($qr_width) . 'px; background: ' . esc_html($qr_bgcolor) . '; padding: ' . esc_html($qr_padding) . 'px; " src="https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&color=' . esc_html(ltrim($qr_color, '#')) . '&bgcolor=' . esc_html(ltrim($qr_bgcolor, '#')) . '&margin=0&ecc=' . esc_html($qr_ecc) . '&format=svg&data=' . esc_html($product_link) . '" style="flex: 0;">';
                    echo '</div>';
                    

                    if($show_name){
                        echo '<div style="background: #ffffff; padding: ' . esc_html($padding) . 'px;">';
                        echo '<p style="display: block; text-align: center; font-size: ' . esc_html($font_size) . 'px; font-family: ' . esc_html($font_family) . '; color: ' . esc_html($color) . '; line-height: ' . esc_html($line_height) . ';">' . esc_html($product_name) . '</p>';
                        echo '</div>';
                    }

                    
                    if($show_price){
                        echo '<div style=" background: #ffffff; padding : ' . esc_html($padding) . 'px;">';
                        if (!empty($product_sale_price)) {
                            $prp = wc_price($product_regular_price);
                            $psp = wc_price($product_sale_price);
                            echo '<del style=" display: block; text-align: center; font-size: ' . esc_html($font_size) . 'px; font-family: ' . esc_html($font_family) . '; color: ' . esc_html($color) . '; line-height: ' . esc_html($line_height) . ';">' . wp_kses_post($prp) . '</del> ';
                    
                            echo '<span style=" display: block; text-align: center; font-size: ' . esc_html($font_size) . 'px; font-family: ' . esc_html($font_family) . '; color: ' . esc_html($color) . '; line-height: ' . esc_html($line_height) . ';">' . wp_kses_post($psp) . '</span>';
                        } else {
                            $prp = wc_price($product_regular_price);
                            echo '<span style=" display: block; text-align: center; font-size: ' . esc_html($font_size) . 'px; font-family: ' . esc_html($font_family) . '; color: ' . esc_html($color) . '; line-height: ' . esc_html($line_height) . ';">' .wp_kses_post($prp). '</span>'; 
                            

                        }

                        echo '</div>';
                    }

                    echo '</div>';
                } else {
                    echo "Product not found for Product ID". esc_html($product_id);
                }

                $counter++;
            }

            echo '</div>';
            echo '</div>';

            // Add a button to trigger printing
            echo '<button onclick="printLabels()">Print Labels</button>';
        }
        ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var inputContainer = document.getElementById('input-container');
                var addInputButton = document.getElementById('add-input');

                var counter = 2; // Start counter from 2 because the first input is already there

                addInputButton.addEventListener('click', function() {
                    var newInput = document.createElement('div');
                    newInput.innerHTML = '<label for="product_id_' + counter + '">Product ID ' + counter + ':</label>' +
                        '<input type="text" name="product_id_' + counter + '" id="product_id_' + counter + '" />';
                    inputContainer.appendChild(newInput);
                    counter++;
                });
            });

            function printLabels() {
                // Create a new window with only the printable area
                var printableArea = document.getElementById('printable-result').innerHTML;
                var printWindow = window.open('', '_blank');
                printWindow.document.open();
                printWindow.document.write('<html><head><title>Print Labels</title></head><body>' + printableArea + '</body></html>');
                printWindow.document.close();

                // Trigger the print functionality in the new window
                printWindow.print();
            }
        </script>
    </div>
    <?php
}

// Function to display the settings page
function ej_woo_label_settings_page() {
    ?>
    <div class="wrap">
        <h2>ej_woo_label Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('ej_woo_label_settings_group'); ?>
            <?php do_settings_sections('ej-woo-label-settings'); ?>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

// Register settings and fields
add_action('admin_init', 'ej_woo_label_register_settings');

function ej_woo_label_register_settings() {
    // Barcode settings section
    add_settings_section('ej_woo_label_barcode_settings_section', 'Barcode Settings', 'ej_woo_label_barcode_settings_section_callback', 'ej-woo-label-settings');

    // Barcode format field
    add_settings_field('barcode_format', 'Barcode Format', 'ej_woo_label_barcode_format_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    // Add other settings fields similarly
    add_settings_field('barcode_height', 'Barcode Height', 'ej_woo_label_barcode_height_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_width', 'Barcode Width', 'ej_woo_label_barcode_width_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_margin_top', 'Barcode Margin Top', 'ej_woo_label_barcode_margin_top_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_margin_bottom', 'Barcode Margin Bottom', 'ej_woo_label_barcode_margin_bottom_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_margin_left', 'Barcode Margin Left', 'ej_woo_label_barcode_margin_left_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_margin_right', 'Barcode Margin Right', 'ej_woo_label_barcode_margin_right_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_background', 'Barcode Background', 'ej_woo_label_barcode_background_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_line_color', 'Barcode Line Color', 'ej_woo_label_barcode_line_color_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_display_value', 'Display Value', 'ej_woo_label_barcode_display_value_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');

    add_settings_field('barcode_font', 'Barcode Font', 'ej_woo_label_barcode_font_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    //add_settings_field('barcode_text_align', 'Barcode Text Align', 'ej_woo_label_barcode_text_align_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_text_position', 'Barcode Text Position', 'ej_woo_label_barcode_text_position_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_text_margin', 'Barcode Text Margin', 'ej_woo_label_barcode_text_margin_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_font_size', 'Barcode Font Size', 'ej_woo_label_barcode_font_size_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');
    add_settings_field('barcode_margin', 'Barcode Margin', 'ej_woo_label_barcode_margin_field_callback', 'ej-woo-label-settings', 'ej_woo_label_barcode_settings_section');

    // QR settings section
    add_settings_section('ej_woo_label_qr_settings_section', 'QR Code Settings', 'ej_woo_label_qr_settings_section_callback', 'ej-woo-label-settings');
    add_settings_field('qr_width', 'QR Code Width', 'ej_woo_label_qr_width_field_callback', 'ej-woo-label-settings', 'ej_woo_label_qr_settings_section');
    add_settings_field('qr_padding_top', 'QR Code Padding Top', 'ej_woo_label_qr_padding_top_field_callback', 'ej-woo-label-settings', 'ej_woo_label_qr_settings_section');
    add_settings_field('qr_padding_bottom', 'QR Code Padding Bottom', 'ej_woo_label_qr_padding_bottom_field_callback', 'ej-woo-label-settings', 'ej_woo_label_qr_settings_section');
    add_settings_field('qr_padding_left', 'QR Code Padding Left', 'ej_woo_label_qr_padding_left_field_callback', 'ej-woo-label-settings', 'ej_woo_label_qr_settings_section');
    add_settings_field('qr_padding_right', 'QR Code Padding Right', 'ej_woo_label_qr_padding_right_field_callback', 'ej-woo-label-settings', 'ej_woo_label_qr_settings_section');
    add_settings_field('qr_ecc', 'QR Code Error Correction Level', 'ej_woo_label_qr_ecc_field_callback', 'ej-woo-label-settings', 'ej_woo_label_qr_settings_section');
    add_settings_field('qr_color', 'QR Code Color', 'ej_woo_label_qr_color_field_callback', 'ej-woo-label-settings', 'ej_woo_label_qr_settings_section');
    add_settings_field('qr_bgcolor', 'QR Code Background Color', 'ej_woo_label_qr_bgcolor_field_callback', 'ej-woo-label-settings', 'ej_woo_label_qr_settings_section');
    add_settings_field('qr_margin', 'QR Code Margin', 'ej_woo_label_qr_margin_field_callback', 'ej-woo-label-settings', 'ej_woo_label_qr_settings_section');
    
    // Register QR code settings
    //register_setting('ej_woo_label_settings_group', 'ej_woo_label_settings');
    
    // Register settings
    register_setting('ej_woo_label_settings_group', 'ej_woo_label_settings');

    // Register a new setting named "ej_woo_label_settings" in the WordPress database
    register_setting('ej_woo_label_settings_group', 'ej_woo_label_settings');

    // Set default values
    $defaults = array(
        'show_name' => true,
        'padding' => 0,
        'font_size' => 12,
        'font_family' => 'auto',
        'color' => '#000000',
        'line_height' => 0
    );
    add_option('ej_woo_label_settings', $defaults);

    // Add a new section to the settings page
    add_settings_section(
        'ej_woo_label_settings_section',
        'Product Label Settings',
        'ej_woo_label_settings_section_callback',
        'ej-woo-label-settings'
    );

    // Add the "show_name" field
    add_settings_field(
        'show_name',
        'Display Product Name',
        'ej_woo_label_show_name_callback',
        'ej-woo-label-settings',
        'ej_woo_label_settings_section'
    );

    // Add other fields
    add_settings_field(
        'padding',
        'Padding',
        'ej_woo_label_padding_callback',
        'ej-woo-label-settings',
        'ej_woo_label_settings_section'
    );

    add_settings_field(
        'font_size',
        'Font Size',
        'ej_woo_label_font_size_callback',
        'ej-woo-label-settings',
        'ej_woo_label_settings_section'
    );

    add_settings_field(
        'font_family',
        'Font Family',
        'ej_woo_label_font_family_callback',
        'ej-woo-label-settings',
        'ej_woo_label_settings_section'
    );

    add_settings_field(
        'color',
        'Color',
        'ej_woo_label_color_callback',
        'ej-woo-label-settings',
        'ej_woo_label_settings_section'
    );

    add_settings_field(
        'line_height',
        'Line Height',
        'ej_woo_label_line_height_callback',
        'ej-woo-label-settings',
        'ej_woo_label_settings_section'
    );

    add_settings_section("ej_woo_label_price_section", "Product Price", null, "ej-woo-label-settings");
    add_settings_field("show_price_field", "Display Product Price", "show_price_callback", "ej-woo-label-settings", "ej_woo_label_price_section");
    register_setting("ej-woo-label-settings", "ej_woo_label_settings");
 
}

// Callback function for barcode settings section
function ej_woo_label_barcode_settings_section_callback() {
    echo '<p>Configure barcode settings here.</p>';
}

// Callback function for barcode format field
function ej_woo_label_barcode_format_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_format = isset($options['barcode_format']) ? $options['barcode_format'] : 'CODE128';
    echo '<select name="ej_woo_label_settings[barcode_format]">';
    $formats = array(
        'CODE128' => 'CODE128',
        'CODE128A' => 'CODE128A',
        'CODE128B' => 'CODE128B',
        'CODE128C' => 'CODE128C',
        'EAN13' => 'EAN13',
        'EAN8' => 'EAN8',
        'UPC' => 'UPC',
        'CODE39' => 'CODE39',
        'ITF14' => 'ITF14',
        'ITF' => 'ITF',
        'MSI' => 'MSI',
        'MSI10' => 'MSI10',
        'MSI11' => 'MSI11',
        'MSI1010' => 'MSI1010',
        'MSI1110' => 'MSI1110',
        'Pharmacode' => 'Pharmacode'
    );
    foreach ($formats as $value => $label) {
        echo '<option value="' . esc_html($value) . '" ' . selected($barcode_format, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}

// Callback function for barcode height field
function ej_woo_label_barcode_height_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_height = isset($options['barcode_height']) ? $options['barcode_height'] : 30;
    echo '<input type="number" min="10" max="100" name="ej_woo_label_settings[barcode_height]" value="' . esc_attr($barcode_height) . '" />';
}


// Callback function for barcode display value field
function ej_woo_label_barcode_display_value_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_display_value = isset($options['barcode_display_value']) ? $options['barcode_display_value'] : false;
    echo '<input type="checkbox" name="ej_woo_label_settings[barcode_display_value]" value="1" ' . checked(1, $barcode_display_value, false) . ' />';
}


// Callback function for barcode width field
function ej_woo_label_barcode_width_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_width = isset($options['barcode_width']) ? $options['barcode_width'] : 0.8;
    echo '<input type="number" min="0.8" max="4" step="0.1" name="ej_woo_label_settings[barcode_width]" value="' . esc_attr($barcode_width) . '" />';
}

// Callback function for barcode margin top field
function ej_woo_label_barcode_margin_top_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_margin_top = isset($options['barcode_margin_top']) ? $options['barcode_margin_top'] : 0;
    echo '<input type="number" min="0" max="50" name="ej_woo_label_settings[barcode_margin_top]" value="' . esc_attr($barcode_margin_top) . '" />';
}

// Callback function for barcode margin bottom field
function ej_woo_label_barcode_margin_bottom_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_margin_bottom = isset($options['barcode_margin_bottom']) ? $options['barcode_margin_bottom'] : 0;
    echo '<input type="number" min="0" max="50" name="ej_woo_label_settings[barcode_margin_bottom]" value="' . esc_attr($barcode_margin_bottom) . '" />';
}

// Callback function for barcode margin left field
function ej_woo_label_barcode_margin_left_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_margin_left = isset($options['barcode_margin_left']) ? $options['barcode_margin_left'] : 0;
    echo '<input type="number" min="0" max="50" name="ej_woo_label_settings[barcode_margin_left]" value="' . esc_attr($barcode_margin_left) . '" />';
}

// Callback function for barcode margin right field
function ej_woo_label_barcode_margin_right_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_margin_right = isset($options['barcode_margin_right']) ? $options['barcode_margin_right'] : 0;
    echo '<input type="number" min="0" max="50" name="ej_woo_label_settings[barcode_margin_right]" value="' . esc_attr($barcode_margin_right) . '" />';
}

// Callback function for barcode background field
function ej_woo_label_barcode_background_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_background = isset($options['barcode_background']) ? $options['barcode_background'] : '#ffffff';
    echo '<input type="color" name="ej_woo_label_settings[barcode_background]" value="' . esc_attr($barcode_background) . '" />';
}

// Callback function for barcode line color field
function ej_woo_label_barcode_line_color_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_line_color = isset($options['barcode_line_color']) ? $options['barcode_line_color'] : '#000000';
    echo '<input type="color" name="ej_woo_label_settings[barcode_line_color]" value="' . esc_attr($barcode_line_color) . '" />';
}



// Callback function for barcode font field
function ej_woo_label_barcode_font_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_font = isset($options['barcode_font']) ? $options['barcode_font'] : 'monospace';
    echo '<input type="text" name="ej_woo_label_settings[barcode_font]" value="' . esc_attr($barcode_font) . '" />';
}

// Callback function for barcode text align field
function ej_woo_label_barcode_text_align_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_text_align = isset($options['barcode_text_align']) ? $options['barcode_text_align'] : 'center';
    echo '<select name="ej_woo_label_settings[barcode_text_align]">';
    $align_options = array(
        'left' => 'Left',
        'center' => 'Center',
        'right' => 'Right'
    );
    foreach ($align_options as $value => $label) {
        echo '<option value="' . esc_html($value) . '" ' . selected($barcode_text_align, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}

// Callback function for barcode text position field
function ej_woo_label_barcode_text_position_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_text_position = isset($options['barcode_text_position']) ? $options['barcode_text_position'] : 'bottom';
    echo '<select name="ej_woo_label_settings[barcode_text_position]">';
    $position_options = array(
        'top' => 'Top',
        'bottom' => 'Bottom'
    );
    foreach ($position_options as $value => $label) {
        echo '<option value="' . esc_html($value) . '" ' . selected($barcode_text_position, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}

// Callback function for barcode text margin field
function ej_woo_label_barcode_text_margin_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_text_margin = isset($options['barcode_text_margin']) ? $options['barcode_text_margin'] : 0;
    echo '<input type="number" min="0" max="10" name="ej_woo_label_settings[barcode_text_margin]" value="' . esc_attr($barcode_text_margin) . '" />';
}

// Callback function for barcode font size field
function ej_woo_label_barcode_font_size_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_font_size = isset($options['barcode_font_size']) ? $options['barcode_font_size'] : 10;
    echo '<input type="number" min="0" max="50" name="ej_woo_label_settings[barcode_font_size]" value="' . esc_attr($barcode_font_size) . '" />';
}

// Callback function for barcode margin field
function ej_woo_label_barcode_margin_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $barcode_margin = isset($options['barcode_margin']) ? $options['barcode_margin'] : 0;
    echo '<input type="number" min="0" max="50" name="ej_woo_label_settings[barcode_margin]" value="' . esc_attr($barcode_margin) . '" />';
}
// Callback function for QR code settings section
function ej_woo_label_qr_settings_section_callback() {
    echo '<p>Configure QR code settings here.</p>';
}

// Callback function for QR code width field
function ej_woo_label_qr_width_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $qr_width = isset($options['qr_width']) ? $options['qr_width'] : 40;
    echo '<input type="number" min="10" max="200" name="ej_woo_label_settings[qr_width]" value="' . esc_attr($qr_width) . '" />';
}

// Callback function for QR code padding top field
function ej_woo_label_qr_padding_top_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $qr_padding_top = isset($options['qr_padding_top']) ? $options['qr_padding_top'] : 5;
    echo '<input type="number" min="0" max="20" name="ej_woo_label_settings[qr_padding_top]" value="' . esc_attr($qr_padding_top) . '" />';
}

// Callback function for QR code padding right field
function ej_woo_label_qr_padding_right_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $qr_padding_right = isset($options['qr_padding_right']) ? $options['qr_padding_right'] : 5;
    echo '<input type="number" min="0" max="20" name="ej_woo_label_settings[qr_padding_right]" value="' . esc_attr($qr_padding_right) . '" />';
}

// Callback function for QR code padding bottom field
function ej_woo_label_qr_padding_bottom_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $qr_padding_bottom = isset($options['qr_padding_bottom']) ? $options['qr_padding_bottom'] : 5;
    echo '<input type="number" min="0" max="20" name="ej_woo_label_settings[qr_padding_bottom]" value="' . esc_attr($qr_padding_bottom) . '" />';
}

// Callback function for QR code padding left field
function ej_woo_label_qr_padding_left_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $qr_padding_left = isset($options['qr_padding_left']) ? $options['qr_padding_left'] : 5;
    echo '<input type="number" min="0" max="20" name="ej_woo_label_settings[qr_padding_left]" value="' . esc_attr($qr_padding_left) . '" />';
}

// Callback function for QR code error correction level field
function ej_woo_label_qr_ecc_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $qr_ecc = isset($options['qr_ecc']) ? $options['qr_ecc'] : 'L';
    echo '<select name="ej_woo_label_settings[qr_ecc]">';
    $ecc_options = array(
        'L' => 'L',
        'M' => 'M',
        'Q' => 'Q',
        'H' => 'H'
    );
    foreach ($ecc_options as $value => $label) {
        echo '<option value="' . esc_html($value) . '" ' . selected($qr_ecc, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}

// Callback function for QR code color field
function ej_woo_label_qr_color_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $qr_color = isset($options['qr_color']) ? $options['qr_color'] : '#000000';
    echo '<input type="color" name="ej_woo_label_settings[qr_color]" value="' . esc_attr($qr_color) . '" />';
}

// Callback function for QR code margin field
function ej_woo_label_qr_margin_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $qr_margin = isset($options['qr_margin']) ? $options['qr_margin'] : 0;
    echo '<input type="number" min="0" max="20" name="ej_woo_label_settings[qr_margin]" value="' . esc_attr($qr_margin) . '" />';
}
// Callback function for QR code background color field
function ej_woo_label_qr_bgcolor_field_callback() {
    $options = get_option('ej_woo_label_settings');
    $qr_bgcolor = isset($options['qr_bgcolor']) ? $options['qr_bgcolor'] : '#ffffff';
    echo '<input type="color" name="ej_woo_label_settings[qr_bgcolor]" value="' . esc_attr($qr_bgcolor) . '" />';
}


// Callback function for the settings section
function ej_woo_label_settings_section_callback() {
    echo '<p>You can change the product label settings here.</p>';
}

// Callback function for the "show_name" field
function ej_woo_label_show_name_callback() {
    $options = get_option('ej_woo_label_settings');
    $show_name = isset($options['show_name']) ? $options['show_name'] : '';

    echo '<input type="checkbox" id="show_name" name="ej_woo_label_settings[show_name]" value="1" ' . checked(1, $show_name, false) . ' />';
    echo '<label for="show_name">Display Product Name</label>';
}

// Callback functions for displaying each setting field
function ej_woo_label_padding_callback() {
    $options = get_option('ej_woo_label_settings');
    $value = isset($options['padding']) ? $options['padding'] : 0;
    echo '<input type="number" min="0" max="50" name="ej_woo_label_settings[padding]" value="' . esc_html($value) . '" />';
}

function ej_woo_label_font_size_callback() {
    $options = get_option('ej_woo_label_settings');
    $value = isset($options['font_size']) ? $options['font_size'] : 12;
    echo '<input type="number" min="2" max="50" name="ej_woo_label_settings[font_size]" value="' . esc_html($value) . '" />';
}

function ej_woo_label_font_family_callback() {
    $options = get_option('ej_woo_label_settings');
    $value = isset($options['font_family']) ? $options['font_family'] : 'auto';
    echo '<input type="text" name="ej_woo_label_settings[font_family]" value="' . esc_html($value) . '" />';
}

function ej_woo_label_color_callback() {
    $options = get_option('ej_woo_label_settings');
    $value = isset($options['color']) ? $options['color'] : '#000000';
    echo '<input type="color" name="ej_woo_label_settings[color]" value="' . esc_html($value) . '" />';
}

function ej_woo_label_line_height_callback() {
    $options = get_option('ej_woo_label_settings');
    $value = isset($options['line_height']) ? $options['line_height'] : 0;
    echo '<input type="number" min="0" max="10" name="ej_woo_label_settings[line_height]" value="' . esc_html($value) . '" />';
}

function show_price_callback() {
    $options = get_option('ej_woo_label_settings');
    $show_price = isset($options['show_price']) ? $options['show_price'] : false;
    ?>
    <input type="checkbox" name="ej_woo_label_settings[show_price]" value="1" <?php checked(1, $show_price); ?> />
    <?php
}
