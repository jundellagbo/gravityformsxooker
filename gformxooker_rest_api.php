<?php

// Custom rest api for checkout process

// Custom Rest API
add_action( 'rest_api_init', function () {
  register_rest_route( 'gformxooker/v1', '/gform-entry-checkout', array(
    'methods' => 'GET',
    'callback' => 'gformxooker_get_entry_checkout_url',
  ));

  register_rest_route( 'gformxooker/v1', '/gform-entry-checkout-success', array(
    'methods' => 'POST',
    'callback' => 'gformxooker_success_purchase',
  ));

  register_rest_route( 'gformxooker/v1', '/gform-entry-checkout-canceled', array(
    'methods' => 'POST',
    'callback' => 'gformxooker_checkout_canceled',
  ));

  register_rest_route( 'gformxooker/v1', '/gform-products-to-posts', array(
    'methods' => 'GET',
    'callback' => 'gformxooker_products_to_posts',
  ));

  register_rest_route( 'gformxooker/v1', 'process-payment', array(
    'methods' => 'GET',
    'callback' => 'gformxooker_process_payment',
  ));

  register_rest_route( 'gformxooker/v1', 'reset-product-sync', array(
    'methods' => 'GET',
    'callback' => 'gformxooker_reset_sync',
  ));

  register_rest_route( 'gformxooker/v1', 'customer-portal', array(
    'methods' => 'GET',
    'callback' => 'gformxooker_customer_portal',
  ));
});

function gformxooker_get_entry_checkout_url( WP_REST_Request $request ) {

    $entry = GFAPI::get_entry($request->get_param( 'entryId' ));
    if(is_wp_error($entry)) {
        return false;
    }

    // check if this is already been checked out
    $isCheckedOutSuccess = gform_get_meta( $entry['id'], 'ezhire_entry_is_checkout_success' );
    if($isCheckedOutSuccess) {
        // remove the checkout url if the purchase has been already made.
        gform_delete_meta( $entry['id'], 'ezhire_entry_checkout_url' );
        return false;
    }

    return array(
        'entry' => $entry,
        'checkout' => gform_get_meta( $entry['id'], 'ezhire_entry_checkout_url' )
    );
}

function gformxooker_success_purchase( WP_REST_Request $request ) {

    try {

        $entryid = $request->get_param( 'entryId' );
        $sessionId = $request->get_param( 'sessionId' );

        // ignore bottom code if missing entry and sessionId from checkout
        if(!$entryid || !$sessionId) { return false; }

        $entry = GFAPI::get_entry($entryid);
        if(is_wp_error($entry)) {
            return false;
        }
        
        // nothing to do if there is transaction or subscription
        if(gform_update_meta( $entry['id'], 'ezhire_entry_is_checkout_success', true )) {
            return false;
        }

        if(!isset($entry['id'])) { return false; }
        $formId = $entry['form_id'];
        $form = GFAPI::get_form($formId);

        $stripe = gformxooker_stripe_entry( $entry['id'] );
        $res = $stripe->checkout->sessions->retrieve($sessionId, []);

        gform_update_meta( $entry['id'], 'payment_element_subscription_id', $res->subscription );
        gform_delete_meta( $entry['id'], 'ezhire_entry_checkout_url' );
        gform_update_meta( $entry['id'], 'ezhire_entry_is_checkout_success', 1 );
        gform_update_meta( $entry['id'], 'gformxooker_stripe_checkout_process', 'finish' );
        GFAPI::add_note( 
			$entry['id'], 
			0, 
			'Payment Status', 
			__( 'Payment has been made.', 'gformxooker' ), 
			'gformxooker_payment_status', 
			'success' 
		);
        
        $notification_data = array(
            'gformxooker' => array(
                'checkouturl' => $res->url,
                'entry' => $entry,
                'form' => $form,
                'session' => $res,
                'subscriptiondetails' => gformstripecustom_load_subscriptiondetail_template(array(
                    'form' => $form,
                    'entry' => $entry
                ))
            )
        );

        GFAPI::send_notifications( $form, $entry, 'gform_xooker_checkout_success', $notification_data);

        do_action('gform_xooker_stripe_success_payment', array(
            'entry' => $entryid,
            'session' => $sessionId
        ));

        return array(
            'transaction_id' => $res->payment_intent,
            'subscription_id' => $res->subscription,
            'entry' => $entry['id'],
            'sessionId' => $sessionId,
            'customer' => $res->customer
        );

    } catch(Exception $e) {
        return false;
    }
}

function gformxooker_checkout_canceled( WP_REST_Request $request ) {
    $entryid = $request->get_param( 'entryId' );
    $sessionId = $request->get_param( 'sessionId' );

    if(gform_update_meta( $entryid, 'ezhire_entry_is_checkout_canceled', true )) {
        return false;
    }

    $entry = GFAPI::get_entry($entryid);
    $formId = $entry['form_id'];
    $form = GFAPI::get_form($formId);

    $stripe = gformxooker_stripe_entry( $entryid );
    if(!$stripe) { return false; }
    $res = $stripe->checkout->sessions->retrieve( $sessionId, [] );
    
    $notification_data = array(
        'gformxooker' => array(
            'checkouturl' => $res->url,
            'entry' => $entry,
            'form' => $form,
            'session' => $res
        )
    );

    gform_update_meta( $entryid, 'ezhire_entry_is_checkout_canceled', 1 );
    GFAPI::send_notifications( $form, $entry, 'gform_xooker_checkout_canceled', $notification_data);
    GFAPI::add_note( 
        $entryid, 
        0, 
        'Payment Status', 
        __( 'Payment has been canceled.', 'gformxooker' ), 
        'gformxooker_payment_status', 
        'error' 
    );

    do_action('gform_xooker_stripe_cancel_payment', array(
        'entry' => $entryid,
        'session' => $sessionId
    ));

    return $res->url;
}


function gformxooker_products_to_posts( WP_REST_Request $request ) {

    $posttype = $request->get_param( 'post_type' );
    if(!$posttype || !str_contains( $posttype, 'gfs_prods_' )) {
        return null;
    }

    $stripe = gformxooker_get_stripe_api( $posttype );
    if(!$stripe) {
        return null;
    }
    $apiArgs = array(
        'limit' => 10,
        'expand' => ['data.default_price']
    );

    $nextOption = "gform_xooker_stripe_product_next_id_" . $posttype;
    $nextPage = get_option($nextOption);
    if($nextPage) {
        $apiArgs['starting_after'] = $nextPage;
    }

    $res = $stripe->products->all($apiArgs);

    foreach($res['data'] as $product) {
        $price = $product['default_price'];
        if($price) {
            $productName = $product['name'];
            $args = array(
                'post_title'   => $productName,
                'post_content' => $product['description'],
                'post_status'  => 'publish',
                'post_type' => $posttype,
                'meta_input' => array(
                    'gform_xooker_api_response' => json_encode($price),
                    'gform_xooker_price_id' => $price['id'],
                    'gform_xooker_product_id' => $product['id'],
                    'gform_xooker_price_recurring_interval' => $price['recurring']['interval'],
                    'gform_xooker_price_recurring_interval_count' => $price['recurring']['interval_count'],
                    'gform_xooker_price_trial_period' => $price['recurring']['trial_period_days'],
                    'gform_xooker_price_amount' => $price['unit_amount'],
                    'gform_xooker_price_currency' => $price['currency'],
                    'gform_xooker_product_active' => $product['active']
                )
            );
            // only store those active products.
            $postID = gformstripecustom_get_post_id_by_metakey_value( 'gform_xooker_product_id', $product['id'], $posttype );
            if($product['active'] && $price['active']) {
                if($postID) {
                    $args['ID'] = $postID;
                    wp_update_post( $args, true );
                } else {
                    wp_insert_post( $args, true );
                }
            }
        }
    }

    update_option($nextOption, !$res['has_more'] || !count($res['data']) ? "" : $res['data'][count($res['data'])-1]['id'], null);

    return $res;
}



function gformxooker_process_payment( WP_REST_Request $request ) {
    
    $entryid = $request->get_param( 'entryId' );
    $sessionId = $request->get_param( 'sessionId' );
    $isCanceled = $request->get_param( 'canceled' );
    $redirectUrl = home_url();
    if($request->get_param( 'redirect_uri' )) {
        $redirectUrl = $request->get_param( 'redirect_uri' );
    }

    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ),
        'body' => json_encode(array(
            'entryId' => $entryid,
            'sessionId' => $sessionId
        ))
    );

    if($isCanceled) {
        wp_remote_post(get_rest_url(null, '/gformxooker/v1/gform-entry-checkout-canceled'), $args);
    } else {
        wp_remote_post(get_rest_url(null, '/gformxooker/v1/gform-entry-checkout-success'), $args);
    }

    wp_redirect(urldecode($redirectUrl));
    exit();
}


function gformxooker_get_customer_id_by_email( $email, $entry ) {
    $stripe = gformxooker_stripe_entry( $entry['id'] );
    if(!$stripe) { return null; }
    

    $apiArgs = array(
        'limit' => 1,
        'email' => $email
    );
    $res = $stripe->customers->all($apiArgs);
    return count($res->data) ? $res->data[0]->id : null;
}


function gformxooker_reset_sync(  WP_REST_Request $request ) {
    $posttype = $request->get_param( 'post_type' );
    if(!$posttype || !str_contains( $posttype, 'gfs_prods_' )) {
        return null;
    }

    $nextOption = "gform_xooker_stripe_product_next_id_" . $posttype;
    update_option($nextOption, null);
    return true;
}

function gformxooker_customer_portal( WP_REST_Request $request ) {

    $customer = $request->get_param( 'customer' );
    $redirect = $request->get_param( 'redirect' );
    $entryId = $request->get_param( 'entryId' );

    $stripe = gformxooker_stripe_entry( $entryId );

    if(!$customer || !$redirect || !$redirect || !$stripe) {
        return null;
    }



    try {
        $customer_portal = $stripe->billingPortal->sessions->create([
            'customer' => $customer,
            'return_url' => $redirect,
        ]);
        
        wp_redirect( $customer_portal->url );
		exit();
    } catch(\Exception $e) {
        wp_redirect( home_url() );
		exit();
    }
}