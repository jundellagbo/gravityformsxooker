<?php

// Our custom post type function
function gformxooker_product_posttype() {
  
    register_post_type( 'gform_stripe_product',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Stripe Products' ),
                'singular_name' => __( 'Stripe Product' ),
                'add_new' => __( 'Add Product' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'gform_stripe_product'),
            'show_in_rest' => true,
            'exclude_from_search' => true,
            'map_meta_cap' => true,
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'gformxooker_product_posttype' );



add_filter('manage_gform_stripe_product_posts_columns','gform_post_table_custom_column');
function gform_post_table_custom_column( $columns ) {
    $columns['gformxooker_stripe_product_id'] = 'Product ID';
    $columns['gformxooker_stripe_price_id'] = 'Stripe Price ID';
    $columns['gformxooker_stripe_pricing'] = 'Pricing';
    $columns['gformxooker_stripe_value_used'] = 'Value Using';
    $columns['gformxooker_stripe_addons'] = 'Addons';
    unset($columns['date']);
    return $columns;
}

add_action( 'manage_posts_custom_column','action_custom_columns_content', 10, 2 );
function action_custom_columns_content ( $column_id, $post_id ) {
    //run a switch statement for all of the custom columns created
    switch( $column_id ) { 

        case 'gformxooker_stripe_product_id':
            echo $post_id;
        break;

        case 'gformxooker_stripe_price_id':

            echo '<p>' . get_field( 'gform_addon_custom_description', $post_id ) . '</p>';

            $price = get_post_meta($post_id, 'gform_xooker_price_id', true );
            if($price) {
                echo '<code>' . $price . '</code>';
            } else {
                echo '<code>Custom pricing</code>';
            }
        break;

        case 'gformxooker_stripe_pricing':
            if(!empty(get_post_meta($post_id, 'gform_xooker_price_id', true ))) {
                $interval = get_post_meta($post_id, 'gform_xooker_price_recurring_interval', true );
                $interval_count = get_post_meta($post_id, 'gform_xooker_price_recurring_interval_count', true );
                $amount = get_post_meta($post_id, 'gform_xooker_price_amount', true );

                $intervallabel = $interval_count < 2 ? 'per' : 'every ' . $interval_count;
                $intervalsuffix = str_contains($intervallabel, 'every') ? 's' : '';
                echo '<p>' . gformstripecustom_money_get_format($amount) . ' ' . strtoupper( (string) get_post_meta( $post_id, 'gform_xooker_price_currency', true ) ) . '</p>';
                if($interval) {
                    echo '<p>' . $intervallabel . ' ' . $interval . $intervalsuffix. '</p>';
                }
            }

            if(empty(get_post_meta($post_id, 'gform_xooker_price_id', true ))) {
                $interval = get_field( 'gform_addon_custom_recurring_interval', $post_id );
                $interval_count = get_field( 'gform_addon_custom_recurring_interval_count', $post_id );
                $amount = gformstripecustom_money_set(get_field( 'gform_addon_custom_price', $post_id ));
                $intervallabel = $interval_count < 2 ? 'per' : 'every ' . $interval_count;
                $intervalsuffix = str_contains($intervallabel, 'every') ? 's' : '';
                echo '<p>' . gformstripecustom_money_get_format($amount) . ' ' . strtoupper( (string) get_post_meta( $post_id, 'gform_addon_custom_currency', true ) ) . '</p>';
                if($interval) {
                    echo '<p>' . $intervallabel . ' ' . $interval . $intervalsuffix. '</p>';
                }
            }
        break;


        case 'gformxooker_stripe_addons':
            $stripeaddons = get_field( 'gform_acf_addon_products', $post_id );
            $stripeaddonsArr = $stripeaddons && count($stripeaddons) ? $stripeaddons : [];
            foreach($stripeaddonsArr as $addon) {
                echo '<code style="margin-right:5px;display:block">' . $addon->post_title . '</code>';
            }
        break;


        case 'gformxooker_stripe_value_used':
            $gformvalueused = get_field( 'gform_acf_addon_product_value_form', $post_id );
            if($gformvalueused) {
                echo '<span class="e-button" style="user-select: all;">' . $gformvalueused . '</span>';
            }
        break;
   }
}


add_action('admin_head-edit.php','gformxooker_custom_button_post');

function gformxooker_custom_button_post() {
    global $current_screen;
    if ('gform_stripe_product' != $current_screen->post_type) {
        return;
    }

    ?>
        <script type="text/javascript">
            async function gformSyncProductsStripe() {
                var gformSyncProductElem = jQuery('.gform-pull-stripe-products');
                gformSyncProductElem.text('Syncing products...');
                gformSyncProductElem.attr('disabled', true);
                
                const response = await fetch("<?php echo get_rest_url(null, "/gformxooker/v1/gform-products-to-posts"); ?>");
                const stripeproducts = await response.json();

                gformSyncProductElem.text(gformSyncProductElem.data('text'));
                gformSyncProductElem.attr('disabled', false);

                if(stripeproducts.has_more) {
                    alert('Products Batch has been synced, sync more for another batch.');
                } else {
                    alert('Products has been completely synced.');
                }

                window.location.reload();
            }
            
            jQuery(document).ready( function()
            {
                jQuery(jQuery("ul.subsubsub")[0]).append('<li class="sync"><button type="button" class="button gform-pull-stripe-products" data-text="Pull Missing Products from Stripe">Pull Missing Products from Stripe</button></li>');
                
                jQuery('.gform-pull-stripe-products').on('click', function() {
                    gformSyncProductsStripe();
                    return false;
                });
            });
        </script>
    <?php
}