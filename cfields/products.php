<?php
global $post, $current_screen;
$addons = get_posts(array(
  'numberposts' => -1,
  'post_type' => $current_screen->post_type
));
$addonvalue = get_post_meta($post->ID, "gformxooker_product_addons", true);
?>
<div class="gformxooker_mb5">
  <p class="gformxooker_formlabel">Product ADDON</p>
  <select name="gformxooker_product_addons" value="<?php echo $addonvalue; ?>">
    <option>Select product</option>
    <?php foreach($addons as $addn):  ?>
      <?php $gformsaccChecked = $addonvalue == $addn->ID ? "selected" : ""; ?>
      <option value="<?php echo $addn->ID ?>" <?php echo $gformsaccChecked; ?>><?php echo $addn->post_title; ?></option>
    <?php endforeach; ?>
  </select>
</div>

<div class="gformxooker_mb5">
  <p class="gformxooker_formlabel">ProductF Form value</p>
  <?php $value = get_post_meta($post->ID, "gformxooker_product_value", true); ?>
  <input type="text" name="gformxooker_product_value" value="<?php echo $value; ?>" style="width: 100%;" />
</div>