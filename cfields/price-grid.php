<?php 
global $post, $current_screen;
$accounts = get_posts(array(
  'numberposts' => -1,
  'post_type' => 'gfs_accs'
));
?>

<div class="gformxooker_mb5">
  <p class="gformxooker_formlabel">Choose Stripe Account</p>
  <?php 
  $valAccount = get_post_meta($post->ID, "gformxooker_price_grid_stripe_account", true);
  ?>
  <select name="gformxooker_price_grid_stripe_account" value="<?php echo $valAccount; ?>">
    <option>Select product</option>
    <?php foreach($accounts as $acc):  ?>
      <?php $gformsaccChecked = $valAccount == $acc->ID ? "selected" : ""; ?>
      <option value="<?php echo $acc->ID ?>" <?php echo $gformsaccChecked; ?>><?php echo $acc->post_title; ?></option>
    <?php endforeach; ?>
  </select>
  <p><strong class="gformxooker_formlabel">Important!</strong> Please save this setting first, before making changes below</p>
</div>

<hr />

<?php 
$products = get_posts(array(
  'numberposts' => -1,
  'post_type' => gformxooker_account_post( $valAccount )
));

if(count($products)):
  $product_val = get_post_meta($post->ID, "gformxooker_price_grid_product", true);
?>
<div class="gformxooker_mb5">
  <p class="gformxooker_formlabel">Choose Product</p>
  <select name="gformxooker_price_grid_product" value="<?php echo $product_val; ?>">
    <option>Select product</option>
    <?php foreach($products as $prod):  ?>
      <?php $productChecked = $product_val == $prod->ID ? "selected" : ""; ?>
      <option value="<?php echo $prod->ID ?>" <?php echo $productChecked; ?>><?php echo $prod->post_title; ?></option>
    <?php endforeach; ?>
  </select>
</div>
<?php endif; ?>

<div class="gformxooker_mb5">
  <?php $plan_val = get_post_meta($post->ID, "gformxooker_price_grid_plan_title", true); ?>
  <p class="gformxooker_formlabel">Plan Title</p>
  <input type="text" class="gformxooker_wfull" value="<?php echo $plan_val; ?>" name="gformxooker_price_grid_plan_title" />
</div>

<div class="gformxooker_mb5">
  <?php $cat_val = get_post_meta($post->ID, "gformxooker_price_cat_val", true); ?>
  <p class="gformxooker_formlabel">Categorize Value</p>
  <input type="text" class="gformxooker_wfull" value="<?php echo $cat_val; ?>" name="gformxooker_price_cat_val" />
  <p>Can be use as split grid. Format (category|priority) ex: monthly|0</p>
</div>


<div class="gformxooker_mb5">
  <?php $subtitle_val = get_post_meta($post->ID, "gformxooker_price_grid_sub_title", true); ?>
  <p class="gformxooker_formlabel">Sub Title</p>
  <input type="text" class="gformxooker_wfull" value="<?php echo $subtitle_val; ?>" name="gformxooker_price_grid_sub_title" />
</div>


<div class="gformxooker_mb5">
  <?php $currency_val = get_post_meta($post->ID, "gformxooker_price_grid_currency", true); ?>
  <p class="gformxooker_formlabel">Currency</p>
  <input type="text" value="<?php echo $currency_val; ?>" name="gformxooker_price_grid_currency" />
</div>


<div class="gformxooker_mb5">
  <?php $price_val = get_post_meta($post->ID, "gformxooker_price_price", true); ?>
  <p class="gformxooker_formlabel">Price</p>
  <input type="text" value="<?php echo $price_val; ?>" name="gformxooker_price_price" />
</div>

<div class="gformxooker_mb5">
  <?php $rec_val = get_post_meta($post->ID, "gformxooker_price_recurring", true); ?>
  <p class="gformxooker_formlabel">Recurring</p>
  <input type="text" class="gformxooker_wfull" value="<?php echo $rec_val; ?>" name="gformxooker_price_recurring" />
</div>


<div class="gformxooker_mb5">
  <?php $key_feat = get_post_meta($post->ID, "gformxooker_price_keyfeat", true); ?>
  <p class="gformxooker_formlabel">Key Feature</p>
  <textarea class="gformxooker_wfull" rows="4" name="gformxooker_price_keyfeat"><?php echo $key_feat; ?></textarea>
</div>


<div class="gformxooker_mb5">
  <?php $sub_feat = get_post_meta($post->ID, "gformxooker_price_subfeat", true); ?>
  <p class="gformxooker_formlabel">Sub Feature</p>
  <textarea class="gformxooker_wfull" rows="4" name="gformxooker_price_subfeat"><?php echo $sub_feat; ?></textarea>
</div>