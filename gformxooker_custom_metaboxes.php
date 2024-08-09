<?php

add_filter( 'gform_entry_detail_meta_boxes', 'gformstripecustom_custom_details_meta_box', 10, 3 );
function gformstripecustom_custom_details_meta_box( $meta_boxes, $entry, $form ) {

  $isEnabled = rgar($form, 'gformstripcustom_direct_checkout');
  if(!$isEnabled) {
    return $meta_boxes;
  }

  if ( ! isset( $meta_boxes['custom_meta_box_subscription'] ) ) {
      $meta_boxes['custom_meta_box_subscription'] = array(
          'title'         => $entry['transaction_type'] == 2 ? esc_html__( 'Subscription Details', 'gravityforms' ) : esc_html__( 'Payment Details', 'gravityforms' ),
          'callback'      => 'gformstripecustom_subscription_details_metaboxes',
          'context'       => 'normal',
      );
  }

  return $meta_boxes;
}


function gformstripecustom_subscription_details_metaboxes( $args ) {

  $form  = $args['form'];
  $entry = $args['entry'];
  
  try {
    ob_start();
    ?>
    <style>
      .entry-products {
        display: none;
      }
    </style>
    <script>
      jQuery('.entry-view-field-name:contains("Order")').hide();
    </script>
    <?php
    echo gformstripecustom_load_subscriptiondetail_template( $args);
    
    echo ob_get_clean();
  } catch(\Exception $e) {
    echo '<p style="text-align: center;">Subscription not found in Stripe.</p>';
  }
}


function gformstripecustom_load_subscriptiondetail_template( $args ) {
  $form  = $args['form'];
  $entry = $args['entry'];

  ob_start();
  require_once(plugin_dir_path( __FILE__ ) . 'gformxooker_subscriptiondetails.php');
  return ob_get_clean();
}