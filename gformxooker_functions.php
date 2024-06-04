<?php

 // money getters from stripe
 function gformstripecustom_money_get( $money ) {
    return $money/100;
 }

 // money setter for stripe
 function gformstripecustom_money_set( $money ) {
    return ((float) $money) * 100;
 }

 function gformstripecustom_money_get_format( $money ) {
    return number_format(gformstripecustom_money_get($money) , 2, '.', '');
 }


 // check if there is transaction
 function gformstripecustom_has_transaction( $entry ) {
    if ( rgar( $entry, 'transaction_type' ) == 1 && !rgempty( 'transaction_id', $entry ) ) {
        return true;
    }
    return false;
 }

 // check if there is subscription.
 function gformstripecustom_has_subscription( $entry ) {
    if ( rgar( $entry, 'transaction_type' ) == 2 && !rgempty( 'subscription_id', $entry ) ) {
        return true;
    }
    return false;
 }

 function gformstripecustom_secret_api() {
    return gf_stripe()->get_secret_api_key();
 }

 function gformstripecustom_get_post_id_by_metakey_value( $metakey, $metavalue, $posttype="gform_stripe_product" ) {
    $postQuery = get_posts(
        array(
            'post_type' => $posttype,
            'meta_query' => array(
              array(
                'key' => $metakey,
                'value' => $metavalue
              )
            ),
        ) 
    );
    $postID = count($postQuery) ? $postQuery[0]->ID : null;
    return $postID;
 }


 function gformstripecustom_post_product_handler_setter( $post_id ) {
    $product = array();
    if(get_post_meta($post_id, 'gform_xooker_price_id', true )) {
        $product = array(
            'price' => get_post_meta($post_id, 'gform_xooker_price_id', true ),
            'quantity' => 1
        );
    } else {
        $interval = get_field( 'gform_addon_custom_recurring_interval', $post_id );
        $interval_count = get_field( 'gform_addon_custom_recurring_interval_count', $post_id );
        $amount = gformstripecustom_money_set(get_field( 'gform_addon_custom_price', $post_id ));
        $product = array(
            'price_data' => array(
                'currency' => strtolower(get_field( 'gform_addon_custom_currency', $post_id )),
                'unit_amount' => $amount,
                'product_data' => array(
                    'name' => get_field( 'gform_addon_custom_item', $post_id )
                )
            ),
            'quantity' => 1
        );
        if($interval && $interval_count) {
            $product['price_data']['recurring'] = array(
                'interval_count' => $interval_count,
                'interval' => $interval
            );
        }
    }
    return $product;
 }

function gformstripecustom_after_submit_getstarted( $entry, $form ) {
    // If the GForm stripe custom addon is empty then submit the form as a normal state which is return;.
    if(!function_exists('gf_stripe')) {
        return;
    }

    $gfstripe = new GFStripe();
    $gfstripe->include_stripe_api();

    // Getting secret API from GForm Stripe Add-On
    $isAllowedRedirect = rgar($form, 'gformstripcustom_direct_checkout');
    $productFieldId = rgar($form, 'gformstripcustom_product_field_ids');
    $paymentMethods = "card";
    $paymentMode = rgar($form, 'gformstripcustom_payment_mode');
    $cancelUrl = rgar($form, 'gformstripcustom_cancel_url');
    $successUrl = rgar($form, 'gformstripcustom_checkout_success_url');
    $redirectUrl = rgar($form, 'gformstripcustom_success_url');
    $customerEmail = rgar($form, 'gformstripcustom_customer_email_field');
    $subscriptionBehavior = rgar($form, 'gformstripcustom_trial_end_behavior');
    $subscriptionTrial = (int) rgar($form, 'gformstripcustom_trial_period_days');
    $isEnabledTaxAutomatic = rgar($form, 'gformstripcustom_collect_tax_automatically');
    $phoneCollection = rgar($form, 'gformstripcustom_collect_customer_phone_number');
    $allowPromocode = rgar($form, 'gformstripcustom_allow_promo_code');
    $taxIdCollection = rgar($form, 'gformstripcustom_tax_id_collection');

    if(!rgar($entry, $customerEmail)) {
        return;
    }

    if(
        !gformstripecustom_secret_api() ||
        !$isAllowedRedirect ||
        !$paymentMode ||
        !$cancelUrl ||
        !$successUrl ||
        !$customerEmail ||
        !$productFieldId ||
        !rgar($entry, $productFieldId)
    ) {
        return;
    }

    $theProduct = rgar($entry, $productFieldId);
    $theProductExtract = explode("|", $theProduct);
    array_pop($theProductExtract);
    $theProductValue = implode("|", $theProductExtract);

    $productsArray = array();
    $post_id = gformstripecustom_get_post_id_by_metakey_value( 'gform_acf_addon_product_value_form', $theProduct );
    if(!$post_id) { return; }
    $productsArray[] = gformstripecustom_post_product_handler_setter( $post_id );

    // addons handling
    $productAddons = get_field( 'gform_acf_addon_products', $post_id );
    $productAddons = $productAddons && count($productAddons) ? $productAddons : [];
    foreach($productAddons as $addon) {
        $productsArray[] = gformstripecustom_post_product_handler_setter( $addon->ID );
    }

    if(!count($productsArray)) {
        return;
    }

    // generate a token link to autofill from link.
    gformxooker_generate_backlink($entry);
    $stripeParams = array(
        'payment_method_types' => explode(",", $paymentMethods),
        'line_items' => $productsArray,
        'mode' => $paymentMode,
        'success_url' => gformxooker_url_assigner($entry, $successUrl),
        'cancel_url' => gformxooker_url_assigner($entry, $cancelUrl)
    );

    if($subscriptionTrial && $paymentMode=="subscription") {
        $subscriptionBehavior = $subscriptionBehavior ? $subscriptionBehavior : "pause";
        $stripeParams['subscription_data'] = array(
            'trial_settings' => array(
                'end_behavior' => array(
                    'missing_payment_method' => $subscriptionBehavior
                )
            ),
            'trial_period_days' => $subscriptionTrial
        );
    }

    if($isEnabledTaxAutomatic) {
        $stripeParams['automatic_tax']['enabled'] = (boolean) $isEnabledTaxAutomatic;
    }

    if($phoneCollection) {
        $stripeParams['phone_number_collection']['enabled'] = (boolean) $phoneCollection;
    }

    if($allowPromocode) {
        $stripeParams['allow_promotion_codes'] = (boolean) $allowPromocode;
    }

    if($taxIdCollection) {
        $stripeParams['tax_id_collection']['enabled'] = (boolean) $taxIdCollection;
    }

    // getting your customer id by your email address for unique transaction of customer
    $customerId = gformxooker_get_customer_id_by_email( rgar($entry, $customerEmail) );
    if(!$customerId) {
        $stripeParams['customer_email'] = rgar($entry, $customerEmail);
    } else {
        $stripeParams['customer'] = $customerId;
    }

    // Stripe Checkout Session starts here.
    \Stripe\Stripe::setApiKey( gformstripecustom_secret_api() );
    $res = \Stripe\Checkout\Session::create($stripeParams);
    gform_update_meta( $entry['id'], 'ezhire_entry_checkout_url', $res->url );
    $entrySuccessURL = gformxooker_url_assigner($entry, $redirectUrl);
    gform_update_meta( $entry['id'], 'ezhire_entry_success_url', $entrySuccessURL );
    $redirectProcess = $res->url;
    if($redirectUrl) {
        $redirectProcess = $entrySuccessURL;
    }

    gform_update_meta( $entry['id'], 'gformxooker_stripe_checkout_process', 'pending' );

    $notification_data = array(
        'gformxooker' => array(
            'checkouturl' => $res->url,
            'entry' => $entry,
            'form' => $form,
            'session' => $res
        )
    );

    GFAPI::send_notifications( $form, $entry, 'gform_xooker_checkout_process', $notification_data);
    wp_redirect($redirectProcess);
    exit;
}
add_action( 'gform_after_submission', 'gformstripecustom_after_submit_getstarted', 10, 2 );


function gformxooker_url_assigner( $entry, $string="" ) {
    $needToReplace = array(
        '{entryId}',
        '{formUrl}',
        '{homeUrl}'
    );
    $replaceTo = array(
        $entry['id'],
        urlencode(gform_get_meta( $entry['id'], 'gform_xooker_partial_token_url' )),
        home_url()
    );
    return str_replace($needToReplace, $replaceTo, $string);
}


add_filter( 'gform_notification_events', 'gform_xooker_notification_add_event' );
function gform_xooker_notification_add_event( $notification_events ) {
    $notification_events['gform_xooker_checkout_success'] = __( 'GForm Xooker: Success', 'gravityforms' );
    $notification_events['gform_xooker_checkout_canceled'] = __( 'GForm Xooker: Canceled', 'gravityforms' );
    $notification_events['gform_xooker_checkout_process'] = __( 'GForm Xooker: Process', 'gravityforms' );
    $notification_events['gform_xooker_abandoned_entry'] = __( 'GForm Xooker: Abandoned Entry', 'gravityforms' );
    return $notification_events;
}

function gformxooker_generate_backlink($entry) {
    $entry = GFAPI::get_entry($entry['id']);
    $uniqid = uniqid();
    $source_url            = $entry['source_url'];
    $source_url            = add_query_arg( array( 'gf_ref' => $uniqid ), $source_url );
    $resume_url            = esc_url_raw( $source_url );

    // generate a token link to autofill from link.
    if(!gform_get_meta( $entry['id'], 'gform_xooker_partial_token_url' )) {
        gform_update_meta( $entry['id'], 'gform_xooker_partial_token_url', $resume_url );
        gform_update_meta( $entry['id'], 'gform_xooker_partial_token', $uniqid );
    }
}

function gform_xooker_partial_entry_bind_event( $partial_entry ) {
    gformxooker_generate_backlink($partial_entry);
}
add_action( 'gform_partialentries_post_entry_saved', 'gform_xooker_partial_entry_bind_event' );
add_action( 'gform_partialentries_post_entry_updated', 'gform_xooker_partial_entry_bind_event' );


# notifications for abandoned entries
add_action( 'gformxooker_scheduled_partial_email', 'gformxooker_scheduled_partial_email_exec' );
function gformxooker_scheduled_partial_email_exec() {
    // abandoned partial entries notification will send to each user to remind them to finish the signup process.
    // possibly there is 5 minutes delay.
    // will notify those partial entries updated after 30 minutes.
    $start_date                    = date( 'Y-m-d', strtotime('-30 minutes'));
    $end_date                      = date( 'Y-m-d', time() );
    $search_criteria['start_date'] = $start_date;
    $search_criteria['end_date']   = $end_date;
    $search_criteria['field_filters'][] = array( 'key' => 'partial_entry_percent', 'operator' => '!=', 'value' => '' );
    $search_criteria['field_filters'][] = array( 'key' => 'gformxooker_partial_token_email_sent', 'operator' => '!=', 'value' => '1' );
    $results = GFAPI::get_entries( 0, $search_criteria );
    foreach( $results as $ent ) {
        $entry = GFAPI::get_entry($ent['id']);
        $formId = $entry['form_id'];
        $form = GFAPI::get_form($formId);

        if(!empty($entry['partial_entry_percent'])) {
            $tokenLink = gform_get_meta( $ent['id'], 'gform_xooker_partial_token_url' );
            $notification_data = array(
                'gformxooker' => array(
                    'link' => $tokenLink,
                    'entry' => $entry,
                    'form' => $form
                )
            );
            GFAPI::send_notifications( $form, $entry, 'gform_xooker_abandoned_entry', $notification_data);
            gform_update_meta( $ent['id'], 'gformxooker_partial_token_email_sent', 1 );
        }
    }

    return true;
}