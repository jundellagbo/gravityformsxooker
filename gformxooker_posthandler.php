<?php

function gformxooker_post_save_handle( $keys = [] ) {
  global $post;
  foreach( $keys as $key ) {
    update_post_meta( $post->ID, $key,  isset($_POST[$key]) ? $_POST[$key] : "");
  }
}


function gformxooker_products_save_post() {
  global $post;
  if(!isset($post->post_type) || !str_contains( $post->post_type, 'gfs_prods_' )) {
    return;
  }
  if( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
    return;
  }

  gformxooker_post_save_handle(array(
    'gformxooker_product_addons',
    'gformxooker_product_value'
  ));
}
add_action( 'save_post', 'gformxooker_products_save_post' );


function gformxooker_stripe_acc_save_post() {
  global $post;
  if(!isset($post->post_type) || $post->post_type != "gfs_accs") {
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



function gformxooker_price_grid_save_post() {
  global $post;
  if(!isset($post->post_type) || $post->post_type != "gfs_price_grid") {
    return;
  }
  if( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
    return;
  }

  gformxooker_post_save_handle(array(
    'gformxooker_price_grid_stripe_account',
    'gformxooker_price_grid_product',
    'gformxooker_price_grid_plan_title',
    'gformxooker_price_grid_sub_title',
    'gformxooker_price_grid_currency',
    'gformxooker_price_price',
    'gformxooker_price_recurring',
    'gformxooker_price_keyfeat',
    'gformxooker_price_subfeat',
    'gformxooker_price_cat_val'
  ));
}
add_action( 'save_post', 'gformxooker_price_grid_save_post' );