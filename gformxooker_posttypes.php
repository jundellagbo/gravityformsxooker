<?php

function gformxooker_stripe_account_posttype() {
  
  // stripe accounts post types
  register_post_type( 'gfs_accs',
    array(
      'labels' => array(
          'name' => __( 'Stripe Accounts' ),
          'singular_name' => __( 'Stripe Account' ),
          'add_new_item' => __( 'Add Account' )
      ),
      'public' => false,
      'show_ui' => true,
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


  // stripe products post types
  $posts = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'gfs_accs'
  ));

  foreach($posts as $post) {
    $productPostType = 'gfs_prods_' . $post->ID;
    register_post_type( $productPostType,
      array(
        'labels' => array(
            'name' => __( $post->post_title . ' Products' ),
            'add_new_item' => __( 'Add Product' )
        ),
        'public' => false,
        'show_ui' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => $productPostType),
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


  // pricing grids post types
  register_post_type( 'gfs_price_grid',
    array(
      'labels' => array(
          'name' => __( 'Pricing Grids' ),
          'singular_name' => __( 'Pricing Grids' ),
          'add_new_item' => __( 'Add Grid' )
      ),
      'public' => false,
      'show_ui' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'gfs_price_grid'),
      'show_in_rest' => false,
      'exclude_from_search' => true,
      'map_meta_cap' => true,
      'menu_icon' => 'dashicons-database',
      'supports' => array(
        'custom-fields',
        'title',
        'editor'
      )
    )
  );
  

  // pricing grids taxonomy
  register_taxonomy(
    'gfs_price_gridcat',
    'gfs_price_grid',
    array(
      'public' => false,
      'show_ui' => true,
      'hierarchical' => true,
      'label' => 'Categories',
      'query_var' => true,
      'show_in_rest' => true,
      'rewrite' => array(
        'slug' => 'price-grid-category',
        'with_front' => false
      )
    )
  );
  register_taxonomy_for_object_type( 'gfs_price_gridcat', 'gfs_price_grid' );
}

// Hooking up our function to theme setup
add_action( 'init', 'gformxooker_stripe_account_posttype' );


add_action( 'add_meta_boxes', 'gformxooker_stripe_acc_meta_box' );
function gformxooker_stripe_acc_meta_box() {
  
  // stripe account metabox
  add_meta_box(
    "gfs_accs_metadata",
    "Stripe Key",
    "gformxooker_stripe_acc_box",
    "gfs_accs",
    "normal",
    "high"
  );


  // stripe products meta box
  $posts = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'gfs_accs'
  ));

  foreach($posts as $post) {
    $productPostType = 'gfs_prods_' . $post->ID;
    add_meta_box(
      $productPostType . "_metadata",
      "Form Setup",
      "gformxooker_product_metabox",
      $productPostType,
      "normal",
      "high"
    );
  }


  // pricing grids meta box
  add_meta_box(
    "gfs_price_metadata",
    "Pricing Grid Settings",
    "gformxooker_pricing_grid_box",
    "gfs_price_grid",
    "normal",
    "high"
  );
}

function gformxooker_product_metabox() {
  ob_start();
  require_once plugin_dir_path( __FILE__ ) . 'cfields/products.php';
  echo ob_get_clean();
}


function gformxooker_stripe_acc_box() {
  ob_start();
  require_once plugin_dir_path( __FILE__ ) . 'cfields/stripe-account.php';
  echo ob_get_clean();
}

function gformxooker_pricing_grid_box() {
  ob_start();
  require_once plugin_dir_path( __FILE__ ) . 'cfields/price-grid.php';
  echo ob_get_clean();
}


add_filter( 'manage_posts_columns', 'gformxooker_product_list_columns', 10 , 2 );
function gformxooker_product_list_columns( $columns, $post_type ) {
  if(!str_contains( $post_type, "gfs_prods_" )) {
    return $columns;
  }
  $columns['gformxooker_stripe_product_id'] = 'Product ID ';
  $columns['gformxooker_stripe_price_id'] = 'Stripe Price ID';
  $columns['gformxooker_stripe_pricing'] = 'Pricing';
  $columns['gformxooker_stripe_value_used'] = 'Value Using';
  $columns['gformxooker_stripe_addons'] = 'Addon';
  unset($columns['date']);
  return $columns;
}


add_filter( 'manage_posts_columns', 'gformxooker_pricegrid_list_columns', 10 , 2 );
function gformxooker_pricegrid_list_columns( $columns, $post_type ) {
  if($post_type != "gfs_price_grid") {
    return $columns;
  }
  $columns['gformxooker_price_grid_cats'] = 'Categories';
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
        break;

        case 'gformxooker_stripe_addons':
          $stripeaddons = get_post_meta( $post_id, 'gformxooker_product_addons', true );
          $stripeaddons = explode(",", str_replace(" ", "", $stripeaddons));
          foreach($stripeaddons as $addon) {
            if($addon) {
              $addon = get_post( (int) $addon );
              echo '<code style="margin:10px;display:block">' . $addon->post_title . '</code>';
            }
          }
        break;

        case 'gformxooker_stripe_value_used':
          $gformvalueused = get_post_meta( $post_id, 'gformxooker_product_value', true );
          if($gformvalueused) {
            echo '<span class="e-button" style="user-select: all;">' . $gformvalueused . '</span>';
          }
        break;


        case 'gformxooker_price_grid_cats':
          $taxms = get_the_terms( $post_id, 'gfs_price_gridcat' );
          foreach( $taxms as $tax) {
            echo '<span class="gformxooker_badge">' . $tax->name . '</span>';
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