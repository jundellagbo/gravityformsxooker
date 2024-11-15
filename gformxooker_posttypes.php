<?php

function gformxooker_stripe_account_posttype() {
  
  register_post_type( 'gfs_accs',
    array(
      'labels' => array(
          'name' => __( 'Stripe Accounts' ),
          'singular_name' => __( 'Stripe Account' ),
          'add_new_item' => __( 'Add Account' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'gfs_accs'),
      'show_in_rest' => false,
      'exclude_from_search' => true,
      'map_meta_cap' => true,
      'menu_icon' => 'dashicons-database',
      'supports' => array(
        'custom-fields',
        'title'
      )
    )
  );


  $posts = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'gfs_accs'
  ));

  foreach($posts as $post) {
    register_post_type( 'gfs_prods_' . $post->ID,
      array(
        'labels' => array(
            'name' => __( $post->post_title . ' Products' ),
            'add_new_item' => __( 'Add Product' )
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'gfs_prods_' . $post->ID),
        'show_in_rest' => false,
        'exclude_from_search' => true,
        'map_meta_cap' => true,
        'menu_icon' => 'dashicons-database',
        'supports' => array(
          'custom-fields',
          'title'
        )
      )
    );
  }
}

// Hooking up our function to theme setup
add_action( 'init', 'gformxooker_stripe_account_posttype' );


add_action( 'add_meta_boxes', 'gformxooker_stripe_acc_meta_box' );
function gformxooker_stripe_acc_meta_box() {
  add_meta_box(
    "gfs_accs_metadata",
    "Stripe Key",
    "gformxooker_stripe_acc_box",
    "gfs_accs",
    "normal",
    "high"
  );
}


function gformxooker_stripe_acc_box() {
  global $post;
  $value = get_post_meta($post->ID, "_gformxooker_stripe_account_key", true);
  echo "<input type=\"password\" name=\"_gformxooker_stripe_account_key\" value=\"" . $value . "\" placeholder=\"Enter stripe account key\" style=\"width: 100%;\" />";
  echo "<p>Do not copy this, the system will encrypt the value.</p>";
}

function gformxooker_stripe_acc_save_post() {
  global $post;
  if($post->post_type !== "gfs_accs") {
    return;
  }
  if( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
    return;
  }
  $fds = new FSD_Data_Encryption();
  $value = $fds->encrypt( sanitize_text_field(  $_POST['_gformxooker_stripe_account_key'] ));
  update_post_meta( $post->ID, "_gformxooker_stripe_account_key", $value);
}
add_action( 'save_post', 'gformxooker_stripe_acc_save_post' );



add_filter( 'manage_posts_columns', 'gformxooker_product_list_columns', 10 , 2 );
function gformxooker_product_list_columns( $columns, $post_type ) {
  if(!str_contains( $post_type, "gfs_prods_" )) {
    return $columns;
  }
  $columns['gformxooker_stripe_product_id'] = 'Product ID ';
  $columns['gformxooker_stripe_price_id'] = 'Stripe Price ID';
  $columns['gformxooker_stripe_pricing'] = 'Pricing';
  $columns['gformxooker_stripe_value_used'] = 'Value Using';
  $columns['gformxooker_stripe_addons'] = 'Addons';
  unset($columns['date']);
  return $columns;
}


add_action( 'manage_posts_custom_column','gformxooker_action_custom_columns_content', 10, 2 );
function gformxooker_action_custom_columns_content ( $column_id, $post_id ) {
    //run a switch statement for all of the custom columns created
    switch( $column_id ) { 
        case 'gformxooker_stripe_product_id':
            echo $post_id;
        break;

        case 'gformxooker_stripe_price_id':
            $price = get_post_meta($post_id, 'gform_xooker_price_id', true );
            if($price) {
                echo '<p>' . get_post_field('post_content', $post_id) . '</p>';
                echo '<code>' . $price . '</code>';
            } else {
                echo '<p>' . get_field( 'gform_addon_custom_description', $post_id ) . '</p>';
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

    if(!str_contains( $current_screen->post_type, "gfs_prods_" )) {
      return;
    }

    ?>
        <script type="text/javascript">
            async function gformSyncProductsStripe(elem=null) {
                if(elem) {
                    elem.text('Loading...');
                    elem.attr('disabled', true);
                }
                
                const response = await fetch("<?php echo get_rest_url(null, "/gformxooker/v1/gform-products-to-posts?post_type=" . $current_screen->post_type); ?>");
                const stripeproducts = await response.json();

                if(elem) {
                    elem.text(elem.data('text'));
                    elem.attr('disabled', false);
                }

                if(stripeproducts.has_more) {
                    alert('Products Batch has been synced, sync more for another batch.');
                } else {
                    alert('Products has been completely synced.');
                }

                window.location.reload();
            }

            async function gformResetSyncProductsStripe(elem=null) {
                if(elem) {
                    elem.text('Loading...');
                    elem.attr('disabled', true);
                }

                const response = await fetch("<?php echo get_rest_url(null, "/gformxooker/v1/reset-product-sync?post_type=" . $current_screen->post_type); ?>");
                if(elem) {
                    elem.text(elem.data('text'));
                    elem.attr('disabled', false);
                }

                alert('Product Sync has been reset.');
            }
            
            jQuery(document).ready( function()
            {
                jQuery(jQuery("ul.subsubsub")[0]).append(`
                    <li class="sync">
                        <button type="button" class="button gform-pull-stripe-products" data-text="Pull Missing Products from Stripe">
                            Pull Missing Products from Stripe
                        </button> 
                        <button type="button" class="button gform-reset-sync" data-text="Reset Sync">
                            Reset Sync
                        </button>
                    </li>`);
                
                jQuery('.gform-pull-stripe-products').on('click', function() {
                    gformSyncProductsStripe(jQuery(this));
                    return false;
                });

                jQuery('.gform-reset-sync').on('click', function() {
                    gformResetSyncProductsStripe(jQuery(this));
                    return false;
                });
            });
        </script>
    <?php
}