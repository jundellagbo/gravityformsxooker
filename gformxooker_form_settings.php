<?php


// Gravity form extended settings

add_filter( 'gform_form_settings', 'gformstripecustom_form_setting', 10, 2 );
function gformstripecustom_form_setting( $settings, $form ) {

    $isEnabled = rgar($form, 'gformstripcustom_direct_checkout');
    $isEnabled = $isEnabled == 1 ? "checked" : "";

    $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_direct_checkout'] = '
        <tr>
           <td>
                <label class="gform-settings-label" for="gformstripcustom_direct_checkout">
                    <input value="1" name="gformstripcustom_direct_checkout" type="checkbox" '.$isEnabled.'>
                    Enable Stripe Hosted Checkout
                </label>
            </td>
        </tr>';


    $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_customer_email_field'] = '
        <tr>
           <td>
                <div class="gform-settings-field__header">
                    <label class="gform-settings-label gform-settings-field__header" for="gformstripcustom_customer_email_field">
                        Customer Email Field ID
                    </label>
                </div>
                <input value="' . rgar($form, 'gformstripcustom_customer_email_field') . '" name="gformstripcustom_customer_email_field" type="number">
            </td>
        </tr>';



    $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_product_field_ids'] = '
        <tr>
           <td>
                <div class="gform-settings-field__header">
                    <label class="gform-settings-label gform-settings-field__header" for="gformstripcustom_product_field_ids">
                        Product Field ID
                    </label>
                </div>
                <input value="' . rgar($form, 'gformstripcustom_product_field_ids') . '" name="gformstripcustom_product_field_ids" type="number">
            </td>
        </tr>';

        $paymentModes = array(
            array(
                "label" => "Payment",
                "value" => "payment"
            ),
            array(
                "label" => "Subscription",
                "value" => "subscription"
            )
        );

        $paymentModesOptions = '';
        foreach($paymentModes as $pmode) {
            $pmodeChecked = rgar($form, 'gformstripcustom_payment_mode') === $pmode['value'] ? "selected" : "";
            $paymentModesOptions .= '<option value="' . $pmode['value'] . '" ' . $pmodeChecked . '>' . $pmode['label'] . '</option>';
        }


        
    $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_payment_mode'] = '
        <tr>
           <td>
                <div class="gform-settings-field__header">
                    <label class="gform-settings-label gform-settings-field__header" for="gformstripcustom_payment_mode">
                        Payment mode
                    </label>
                </div>
                <select name="gformstripcustom_payment_mode" value="'.rgar($form, 'gformstripcustom_payment_mode').'">' . $paymentModesOptions . '</select>
            </td>
        </tr>';


    $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_trial_period_days'] = '
        <tr>
           <td>
                <div class="gform-settings-field__header">
                    <label class="gform-settings-label gform-settings-field__header" for="gformstripcustom_trial_period_days">
                        Free trial period days (This will be ignored if the payment method is not subscription)
                    </label>
                </div>
                <input value="' . rgar($form, 'gformstripcustom_trial_period_days') . '" name="gformstripcustom_trial_period_days" type="number">
            </td>
        </tr>';


        $trialEndBehaviors = array(
            array(
                "label" => "Cancel Subscripion",
                "value" => "cancel"
            ),
            array(
                "label" => "Create Invoice",
                "value" => "create_invoice"
            ),
            array(
                "label" => "Pause",
                "value" => "pause"
            ),
        );


        $trialEndBehaviorOptions = '';
        foreach($trialEndBehaviors as $pmode) {
            $tendchecked = rgar($form, 'gformstripcustom_trial_end_behavior') === $pmode['value'] ? "selected" : "";
            $trialEndBehaviorOptions .= '<option value="' . $pmode['value'] . '" ' . $tendchecked . '>' . $pmode['label'] . '</option>';
        }
    
    $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_trial_end_behavior'] = '
        <tr>
           <td>
                <div class="gform-settings-field__header">
                    <label class="gform-settings-label gform-settings-field__header" for="gformstripcustom_trial_end_behavior">
                        Free trial end behavior
                    </label>
                </div>
                <select name="gformstripcustom_trial_end_behavior" value="'.rgar($form, 'gformstripcustom_trial_end_behavior').'">' . $trialEndBehaviorOptions . '</select>
            </td>
        </tr>';


        $isEnabledTaxAutomatic = rgar($form, 'gformstripcustom_collect_tax_automatically');
        $isEnabledTaxAutomatic = $isEnabledTaxAutomatic == 1 ? "checked" : "";

        $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_collect_tax_automatically'] = '
            <tr>
            <td>
                    <label class="gform-settings-label" for="gformstripcustom_collect_tax_automatically">
                        <input value="1" name="gformstripcustom_collect_tax_automatically" type="checkbox" '.$isEnabledTaxAutomatic.'>
                        Collect tax automatically
                    </label>
                </td>
            </tr>';


        $phoneCollection = rgar($form, 'gformstripcustom_collect_customer_phone_number');
        $phoneCollection = $phoneCollection == 1 ? "checked" : "";

        $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_collect_customer_phone_number'] = '
            <tr>
            <td>
                    <label class="gform-settings-label" for="gformstripcustom_collect_customer_phone_number">
                        <input value="1" name="gformstripcustom_collect_customer_phone_number" type="checkbox" '.$phoneCollection.'>
                        Require custom to provide phone number
                    </label>
                </td>
            </tr>';


        $allowPromocode = rgar($form, 'gformstripcustom_allow_promo_code');
        $allowPromocode = $allowPromocode == 1 ? "checked" : "";

        $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_allow_promo_code'] = '
            <tr>
            <td>
                    <label class="gform-settings-label" for="gformstripcustom_allow_promo_code">
                        <input value="1" name="gformstripcustom_allow_promo_code" type="checkbox" '.$allowPromocode.'>
                        Allow promotion codes
                    </label>
                </td>
            </tr>';


        $taxIdCollection = rgar($form, 'gformstripcustom_tax_id_collection');
        $taxIdCollection = $taxIdCollection == 1 ? "checked" : "";

        $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_tax_id_collection'] = '
            <tr>
            <td>
                    <label class="gform-settings-label" for="gformstripcustom_tax_id_collection">
                        <input value="1" name="gformstripcustom_tax_id_collection" type="checkbox" '.$taxIdCollection.'>
                        Allow business customers to provide tax IDs
                    </label>
                </td>
            </tr>';


    $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_cancel_url'] = '
        <tr>
           <td>
                <div class="gform-settings-field__header">
                    <p>For URL\'s you can use these shortcodes to replace <code>{entryId}</code> <code>{CHECKOUT_SESSION_ID}</code> <code>{formUrl}</code></p>
                    <label class="gform-settings-label gform-settings-field__header" for="gformstripcustom_cancel_url">
                        Cancel Page URL
                    </label>
                </div>
                <input value="' . rgar($form, 'gformstripcustom_cancel_url') . '" name="gformstripcustom_cancel_url" type="text">
            </td>
        </tr>';


    $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_success_url'] = '
        <tr>
           <td>
                <div class="gform-settings-field__header">
                    <label class="gform-settings-label gform-settings-field__header" for="gformstripcustom_success_url">
                        Success Page URL
                    </label>
                </div>
                <input value="' . rgar($form, 'gformstripcustom_success_url') . '" name="gformstripcustom_success_url" type="text">
            </td>
        </tr>';


    $settings[ __( 'Stripe Settings', 'gravityforms' ) ]['gformstripcustom_checkout_success_url'] = '
        <tr>
           <td>
                <div class="gform-settings-field__header">
                    <label class="gform-settings-label gform-settings-field__header" for="gformstripcustom_checkout_success_url">
                        Checkout Success Page URL
                    </label>
                </div>
                <input value="' . rgar($form, 'gformstripcustom_checkout_success_url') . '" name="gformstripcustom_checkout_success_url" type="text">
            </td>
        </tr>';
 
    return $settings;
}
 
// // save your custom form setting
add_filter( 'gform_pre_form_settings_save', 'gformstripecustom_form_setting_save' );
function gformstripecustom_form_setting_save($form) {
    $formsettings = array(
        'gformstripcustom_direct_checkout',
        'gformstripcustom_product_field_ids',
        'gformstripcustom_payment_mode',
        'gformstripcustom_cancel_url',
        'gformstripcustom_success_url',
        'gformstripcustom_checkout_success_url',
        'gformstripcustom_trial_period_days',
        'gformstripcustom_trial_end_behavior',
        'gformstripcustom_customer_email_field',
        'gformstripcustom_collect_tax_automatically',
        'gformstripcustom_collect_customer_phone_number',
        'gformstripcustom_allow_promo_code',
        'gformstripcustom_tax_id_collection',
        //'gformstripcustom_product_dynamic_ids'
    );

    foreach($formsettings as $fs) {
        $form[$fs] = rgpost($fs);    
    }

    return $form;
}