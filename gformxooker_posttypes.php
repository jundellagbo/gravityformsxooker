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



