<?php
global $post;
$value = get_post_meta($post->ID, "_gformxooker_stripe_account_key", true);
?>

<input type="password" name="_gformxooker_stripe_account_key" value="<?php echo esc_attr($value); ?>" placeholder="Enter stripe account key" style="width: 100%;" />
<p>Do not copy this, the system will encrypt the value.</p>