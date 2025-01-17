<?php
/**
 * Plugin Name: Custom Product Fields for WooCommerce
 * Description: Adds custom fields to WooCommerce products and displays them on the product page.
 * Version: 1.0
 * Author: Jennifer Murrin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add custom fields to WooCommerce product edit screen.
function custom_product_fields_add_meta_box() {
    add_meta_box(
        'custom_product_fields',
        __( 'Custom Product Fields', 'textdomain' ),
        'custom_product_fields_callback',
        'product',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'custom_product_fields_add_meta_box' );

function custom_product_fields_callback( $post ) {
    // Retrieve existing data from the database.
    $custom_field_value = get_post_meta( $post->ID, '_custom_field', true );

    // Display the field.
    ?>
    <p>
        <label for="custom_field"><?php _e( 'Custom Field Label', 'textdomain' ); ?></label>
        <input type="text" id="custom_field" name="custom_field" value="<?php echo esc_attr( $custom_field_value ); ?>" class="widefat" />
    </p>
    <?php
}

// Save the custom field data.
function custom_product_fields_save_meta_box_data( $post_id ) {
    if ( isset( $_POST['custom_field'] ) ) {
        update_post_meta( $post_id, '_custom_field', sanitize_text_field( $_POST['custom_field'] ) );
    }
}
add_action( 'save_post', 'custom_product_fields_save_meta_box_data' );

// Display the custom field on the product page.
function custom_product_fields_display_on_product_page() {
    global $post;

    $custom_field_value = get_post_meta( $post->ID, '_custom_field', true );

    if ( ! empty( $custom_field_value ) ) {
        echo '<p class="custom-product-field"><strong>' . __( 'Custom Field:', 'textdomain' ) . '</strong> ' . esc_html( $custom_field_value ) . '</p>';
    }
}
add_action( 'woocommerce_single_product_summary', 'custom_product_fields_display_on_product_page', 25 );

// Add the custom field to the WooCommerce REST API response.
function custom_product_fields_add_to_rest_api( $response, $object, $request ) {
    $custom_field_value = get_post_meta( $object->get_id(), '_custom_field', true );

    if ( ! empty( $custom_field_value ) ) {
        $response->data['custom_field'] = $custom_field_value;
    }

    return $response;
}
add_filter( 'woocommerce_rest_prepare_product', 'custom_product_fields_add_to_rest_api', 10, 3 );
