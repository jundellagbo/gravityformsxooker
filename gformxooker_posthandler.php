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


function gformxooker_stripe_key_is_valid( $secret_key ) {
  \Stripe\Stripe::setApiKey($secret_key);
  try {
      $account = \Stripe\Account::retrieve();
      return $account;
  } catch (\Stripe\Exception\AuthenticationException $e) {
      return false;
  } catch (\Exception $e) {
      return false;
  }
}


function gformxooker_post_saved_notice() {
    $user_id = get_current_user_id();
    if (get_transient('gformxooker_stripe_key_error' . $user_id)) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p>Stripe account key is invalid!</p>';
        echo '</div>';

        // Delete the transient so notice shows only once
        delete_transient('gformxooker_stripe_key_error' . $user_id);
    }

    $accountSuccess = get_transient('gformxooker_stripe_key_success' . $user_id);
    if ($accountSuccess) {
        $account = json_decode( $accountSuccess, true );
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>Stripe account key is valid!</p>';
        echo '<p>Business name: ' . $account['business_profile']['name'] . '</p>';
        echo '<p>Business phone: ' . $account['business_profile']['support_phone'] . '</p>';
        echo '<p>Business email: ' . $account['business_profile']['support_email'] . '</p>';
        echo '<p>Business url: ' . $account['business_profile']['url'] . '</p>';
        echo '</div>';

        // Delete the transient so notice shows only once
        delete_transient('gformxooker_stripe_key_success' . $user_id);
    }
}
add_action('admin_notices', 'gformxooker_post_saved_notice');


function gformxooker_stripe_acc_save_post() {
  global $post;
  if(!isset($post->post_type) || $post->post_type != "gfs_accs") {
    return;
  }
  if( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
    return;
  }

  $value = sanitize_text_field(  $_POST['_gformxooker_stripe_account_key'] );
  $account = gformxooker_stripe_key_is_valid( $value );
  if(!gformxooker_stripe_key_is_valid( $value )) {
    set_transient('gformxooker_stripe_key_error' . get_current_user_id(), true, 30);
  }

  update_post_meta( $post->ID, "_gformxooker_stripe_account_key", $value);
  set_transient('gformxooker_stripe_key_success' . get_current_user_id(), json_encode( $account ), 30);
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