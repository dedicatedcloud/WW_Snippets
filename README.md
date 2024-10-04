# WordPress & WooCommerce Snippets

<!--
<details>
  <summary>Code</summary>

  ```
here
```
</details>
-->



[WooCommerce](#woocommerce)

[WordPress](wordpress)


1. [Show product variations chosen at checkout in the information table instead of all the variants](#show-product-variations-chosen-at-checkout-in-the-information-table-instead-of-all-the-variants).
2. [Store Information Settings Page](#store-information-settings-page)

## Woocommerce

##### Show product variations chosen at checkout in the information table instead of all the variants
<details>

 <summary> Show only the product variations chosen at checkout in the information table instead of all the variants that are attached to that product</summary>
 
```
add_action( 'woocommerce_before_add_to_cart_quantity', 'wwccs_dynamic_atts_variation' );
  
function wwccs_dynamic_atts_variation() {
   global $product;
   if ( ! $product->is_type( 'variable' ) ) return;
    wc_enqueue_js( " 
      $('input.variation_id').change(function(){
         if( $(this).val() && $(this).val() > 0  ) {   
            $('form.variations_form').find('.variations select').each( function( index, el ){
               var current_select_id = $(el).attr('id');
               var current_select_val = $(el).find('option:selected').text();
               $('.woocommerce-product-attributes-item--attribute_'+current_select_id+' td p').text(current_select_val);
            });
         } 
      });
   " );
}
```


</details>



</details>


##### Store Information Settings Page
<details>
  <summary>Creates a settings page to add information about a store on the WooCommerce Single product page.</summary>

  ```

// Add custom admin menu for store details
add_action('admin_menu', 'custom_store_details_menu');
function custom_store_details_menu() {
    add_menu_page(
        __('Store Details', 'woocommerce'), // Page Title
        __('Store Details', 'woocommerce'), // Menu Title
        'manage_options', // Capability
        'store-details', // Menu Slug
        'custom_store_details_page', // Function to display the page
        'dashicons-store', // Icon
        20 // Position
    );
}

// Register settings for store details
add_action('admin_init', 'custom_store_details_settings');
function custom_store_details_settings() {
    register_setting('store_details_group', 'store_address');
    register_setting('store_details_group', 'store_hours');
    register_setting('store_details_group', 'store_phone');
    register_setting('store_details_group', 'store_google_map');
}

// Store details settings page
function custom_store_details_page() { ?>
    <div class="wrap">
        <h1><?php _e('Store Details Settings', 'woocommerce'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('store_details_group'); ?>
            <?php do_settings_sections('store_details_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Store Address', 'woocommerce'); ?></th>
                    <td><input type="text" name="store_address" value="<?php echo esc_attr(get_option('store_address')); ?>" style="width: 50%;" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Store Opening Hours', 'woocommerce'); ?></th>
                    <td><input type="text" name="store_hours" value="<?php echo esc_attr(get_option('store_hours')); ?>" style="width: 100%;" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Store Phone Number', 'woocommerce'); ?></th>
                    <td><input type="text" name="store_phone" value="<?php echo esc_attr(get_option('store_phone')); ?>" style="width: 100%;" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Google Map (Iframe HTML Code)', 'woocommerce'); ?></th>
                    <td><textarea name="store_google_map" rows="5" style="width: 100%;"><?php echo esc_textarea(get_option('store_google_map')); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }


// Replace Add to Cart button with custom HTML for products in "Shop" category on shop pages
add_filter('woocommerce_loop_add_to_cart_link', 'replace_add_to_cart_button_shop', 10, 2);
function replace_add_to_cart_button_shop($button, $product) {
    if (has_term('Shop', 'product_cat', $product->get_id())) {
        // Fetch store details from the settings
        $store_address = get_option('store_address');
        $store_hours = get_option('store_hours');
        $store_phone = get_option('store_phone');
        $store_google_map = get_option('store_google_map');

        // Custom HTML to display on shop pages
        return '<div class="custom-html-message">
                    <h3>## Available In-Store Only</h3>
                    <p>## can only be purchased from our shop. Visit us for exclusive offers!</p>
                    <p><strong>Address:</strong> ' . esc_html($store_address) . '</p>
                    <p><strong>Opening Hours:</strong> ' . esc_html($store_hours) . '</p>
                    <p><strong>Phone:</strong> ' . esc_html($store_phone) . '</p>
                    <div class="google-map">' . $store_google_map . '</div>
                </div>';
    }
    return $button; // Return default Add to Cart button for other products
}

// Disable Add to Cart button and replace it with custom HTML on single product pages
add_action('woocommerce_single_product_summary', 'replace_add_to_cart_button_single', 1);
function replace_add_to_cart_button_single() {
    global $product;
    if (has_term('Shop', 'product_cat', $product->get_id())) {
        // Fetch store details from the settings
        $store_address = get_option('store_address');
        $store_hours = get_option('store_hours');
        $store_phone = get_option('store_phone');
        $store_google_map = get_option('store_google_map');

        // Remove the default Add to Cart button
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        
        // Custom HTML to display on single product pages
        echo '<div class="custom-html-message">
                <h3>Purchase ## In-Store</h3>
                <p>This item can only be purchased from our shop. We look forward to welcoming you!</p>
                <p><strong>Address:</strong><br> ' . esc_html($store_address) . '</p>
                <p><strong>Opening Hours:</strong> ' . esc_html($store_hours) . '</p>
                <p><strong>Phone:</strong> ' . esc_html($store_phone) . '</p>
                <div class="google-map">' . $store_google_map . '</div>
              </div>';
    }
}
```
</details>

# WordPress
<!--
<details>
  <summary>Store Information Settings Page</summary>

  ```
<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Add custom admin menu for store details
add_action('admin_menu', 'custom_store_details_menu');
function custom_store_details_menu() {
    add_menu_page(
        __('Store Details', 'woocommerce'), // Page Title
        __('Store Details', 'woocommerce'), // Menu Title
        'manage_options', // Capability
        'store-details', // Menu Slug
        'custom_store_details_page', // Function to display the page
        'dashicons-store', // Icon
        20 // Position
    );
}

// Register settings for store details
add_action('admin_init', 'custom_store_details_settings');
function custom_store_details_settings() {
    register_setting('store_details_group', 'store_address');
    register_setting('store_details_group', 'store_hours');
    register_setting('store_details_group', 'store_phone');
    register_setting('store_details_group', 'store_google_map');
}

// Store details settings page
function custom_store_details_page() { ?>
    <div class="wrap">
        <h1><?php _e('Store Details Settings', 'woocommerce'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('store_details_group'); ?>
            <?php do_settings_sections('store_details_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Store Address', 'woocommerce'); ?></th>
                    <td><input type="text" name="store_address" value="<?php echo esc_attr(get_option('store_address')); ?>" style="width: 50%;" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Store Opening Hours', 'woocommerce'); ?></th>
                    <td><input type="text" name="store_hours" value="<?php echo esc_attr(get_option('store_hours')); ?>" style="width: 100%;" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Store Phone Number', 'woocommerce'); ?></th>
                    <td><input type="text" name="store_phone" value="<?php echo esc_attr(get_option('store_phone')); ?>" style="width: 100%;" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Google Map (Iframe HTML Code)', 'woocommerce'); ?></th>
                    <td><textarea name="store_google_map" rows="5" style="width: 100%;"><?php echo esc_textarea(get_option('store_google_map')); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }


// Replace Add to Cart button with custom HTML for products in "Shop" category on shop pages
add_filter('woocommerce_loop_add_to_cart_link', 'replace_add_to_cart_button_shop', 10, 2);
function replace_add_to_cart_button_shop($button, $product) {
    if (has_term('Shop', 'product_cat', $product->get_id())) {
        // Fetch store details from the settings
        $store_address = get_option('store_address');
        $store_hours = get_option('store_hours');
        $store_phone = get_option('store_phone');
        $store_google_map = get_option('store_google_map');

        // Custom HTML to display on shop pages
        return '<div class="custom-html-message">
                    <h3>## Available In-Store Only</h3>
                    <p>## can only be purchased from our shop. Visit us for exclusive offers!</p>
                    <p><strong>Address:</strong> ' . esc_html($store_address) . '</p>
                    <p><strong>Opening Hours:</strong> ' . esc_html($store_hours) . '</p>
                    <p><strong>Phone:</strong> ' . esc_html($store_phone) . '</p>
                    <div class="google-map">' . $store_google_map . '</div>
                </div>';
    }
    return $button; // Return default Add to Cart button for other products
}

// Disable Add to Cart button and replace it with custom HTML on single product pages
add_action('woocommerce_single_product_summary', 'replace_add_to_cart_button_single', 1);
function replace_add_to_cart_button_single() {
    global $product;
    if (has_term('Shop', 'product_cat', $product->get_id())) {
        // Fetch store details from the settings
        $store_address = get_option('store_address');
        $store_hours = get_option('store_hours');
        $store_phone = get_option('store_phone');
        $store_google_map = get_option('store_google_map');

        // Remove the default Add to Cart button
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        
        // Custom HTML to display on single product pages
        echo '<div class="custom-html-message">
                <h3>Purchase ## In-Store</h3>
                <p>This item can only be purchased from our shop. We look forward to welcoming you!</p>
                <p><strong>Address:</strong><br> ' . esc_html($store_address) . '</p>
                <p><strong>Opening Hours:</strong> ' . esc_html($store_hours) . '</p>
                <p><strong>Phone:</strong> ' . esc_html($store_phone) . '</p>
                <div class="google-map">' . $store_google_map . '</div>
              </div>';
    }
}
```
</details>
-->

<!--
<details>
  <summary>Code</summary>

  ```
here
```
</details>
-->

<!--
<details>
  <summary>Code</summary>

  ```
here
```
</details>
-->

<!--
<details>
  <summary>Code</summary>

  ```
here
```
</details>
-->

<!--
<details>
  <summary>Code</summary>

  ```
here
```
</details>
-->

