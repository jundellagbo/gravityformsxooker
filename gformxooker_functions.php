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

 function gformstripecustom_get_post_id_by_metakey_value( $metakey, $metavalue ) {
    global $wpdb;
    $tbl = $wpdb->prefix.'postmeta';
    $prepare_guery = $wpdb->prepare( "SELECT post_id FROM $tbl where meta_key=%s and meta_value=%s", array( $metakey, $metavalue ));
    $get_values = $wpdb->get_col( $prepare_guery );
    $postID = count($get_values) ? $get_values[0] : null;
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

    if(!isset($entry[(int) $customerEmail])) {
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
        !isset($entry[(int) $productFieldId])
    ) {
        return;
    }

    $theProduct = $entry[(int) $productFieldId];
    $theProductExtract = explode("|", $theProduct);
    array_pop($theProductExtract);
    $theProductValue = implode("|", $theProductExtract);

    $productsArray = array();
    $post_id = gformstripecustom_get_post_id_by_metakey_value( 'gform_acf_addon_product_value_form', $theProductValue );
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

    $stripeParams = array(
        'customer_email' => $entry[(int) $customerEmail],
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
    wp_redirect($redirectProcess);
    exit;
}
add_action( 'gform_after_submission', 'gformstripecustom_after_submit_getstarted', 10, 2 );


function gformxooker_url_assigner( $entry, $string="" ) {
    $needToReplace = array(
        '{entryId}'
    );

    $replaceTo = array(
        $entry['id']
    );

    return str_replace($needToReplace, $replaceTo, $string);
}


add_filter( 'gform_notification_events', 'gform_xooker_notification_add_event' );
function gform_xooker_notification_add_event( $notification_events ) {
    $notification_events['gform_xooker_checkout_success'] = __( 'Stripe Checkout Success', 'gravityforms' );
    $notification_events['gform_xooker_checkout_canceled'] = __( 'Stripe Checkout Canceled', 'gravityforms' );
    return $notification_events;
}