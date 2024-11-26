<?php

function gformxooker_ezplan_shortcode($atts) {
  $default = array(
    'category' => '',
    'top_content_id' => null,
    'bottom_content_id' => null,
    'class' => ""
  );
  $a = shortcode_atts($default, $atts);
ob_start();

$optionStyles = [];


if(!$a['category']) {
  return null;
}

$posts = get_posts(array(
  'post_type' => 'gfs_price_grid',
  'tax_query' => array(
    array(
      'taxonomy' => 'gfs_price_gridcat',
      'field' => 'slug',
      'terms' => $a['category']
    )
  )
));

$priceGrids = [];
foreach( $posts as $pst ) {
  $catVal = get_post_meta( $pst->ID, 'gformxooker_price_cat_val', true );
  if($catVal) {
    $priceGrids[strtolower($catVal)][] = array(
      'gformxooker_price_grid_product' => get_post_meta( $pst->ID, 'gformxooker_price_grid_product', true ),
      'gformxooker_price_grid_plan_title' => get_post_meta( $pst->ID, 'gformxooker_price_grid_plan_title', true ),
      'gformxooker_price_grid_sub_title' => get_post_meta( $pst->ID, 'gformxooker_price_grid_sub_title', true ),
      'gformxooker_price_grid_currency' => get_post_meta( $pst->ID, 'gformxooker_price_grid_currency', true ),
      'gformxooker_price_price' => get_post_meta( $pst->ID, 'gformxooker_price_price', true ),
      'gformxooker_price_recurring' => get_post_meta( $pst->ID, 'gformxooker_price_recurring', true ),
      'gformxooker_price_keyfeat' => get_post_meta( $pst->ID, 'gformxooker_price_keyfeat', true ),
      'gformxooker_price_subfeat' => get_post_meta( $pst->ID, 'gformxooker_price_subfeat', true ),
      'gformxooker_price_cat_val' => get_post_meta( $pst->ID, 'gformxooker_price_cat_val', true ),
      'cat_val' => $catVal,
      'description' => $pst->post_content
    );
  }
}
?>

<div class="ezplan_shortcode <?php echo $a['class']; ?>">

<h3 style="text-align: center">Choose your plan</h3>

<?php if( count( $priceGrids ) ): ?>
<div class="gform_plan_toggle modulev2">
  <div class="gform_plan_toggle_container">
    <div class="tabs">
      <?php $opti=0; foreach( $priceGrids as $key => $opt ): ?>
        <input type="radio" id="radio-<?php echo $opti+1; ?>" name="tabs" value="<?php echo $key; ?>" class="gform_xooker_plan_type_selector" <?php echo $opti==0 ? "checked" : ""; ?>>
        <label class="tab gformxooker_capitalize" for="radio-<?php echo $opti+1; ?>"><?php echo $key; ?></label>
      <?php $opti++; endforeach; ?>
      <span class="glider"></span>
    </div>
  </div>
</div>
<?php endif; ?>

<?php 
if( $a['top_content_id']) {
  $topContent = get_post((int) $a['top_content_id']);
  if($topContent) {
    echo $topContent->post_content;
  }
}
?>

<?php if( count($priceGrids)): ?>
<div class="gform_xooler_plans_wrapper">
  <?php $pgridIndex=0; foreach($priceGrids as $key => $opt): ?>
    <div class="gform_xooker_plans modulev2 <?php echo $key; ?> <?php echo $pgridIndex==0 ? "active" : ""; ?>">
      <?php foreach( $opt as $pgrid ): ?>

        <?php 
          $productValue = get_post_meta( (int) $pgrid['gformxooker_price_grid_product'], 'gformxooker_product_value', true );
        ?>
        <div class="gform_xooker_item gfxooker-plan-grid" data-plan="<?php echo $productValue; ?>">
          <div class="gform_xooker_item_radio">
            <svg class="gform_xooker_plan_indicator" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg> 
          </div>
          <div class="gform_xooker_item_details">
            <div>
              <span class="gform_xooker_item_name gformxooker_shadow"><?php echo $pgrid['gformxooker_price_grid_plan_title']; ?></span>
              <span class="gform_xooker_item_sub"><?php echo $pgrid['gformxooker_price_grid_sub_title']; ?></span>
              <p class="gform_xooker_price">
                <span class="gform_xooker_price_cur"><?php echo $pgrid['gformxooker_price_grid_currency']; ?></span>
                <?php echo $pgrid['gformxooker_price_price']; ?>/
                <span class="gform_xooker_recurring"><?php echo $pgrid['gformxooker_price_recurring']; ?></span>
              </p>
              <p class="gform_xooker_desc"><?php // echo $pgrid['gformxooker_price_subfeat']; ?></p>
              <div class="gform_xooker_features">
                <?php echo $pgrid['description']; ?>
              </div>
            </div>
            <div>
              <p class="gform_xooker_highlight">
                <?php echo $pgrid['gformxooker_price_keyfeat']; ?>
              </p>
              
              <div class="gform_xooker_highlight_price gformxooker_shadow">
                <!-- <p class="gform_xooker_bigtext"><?php echo $pgrid['gformxooker_price_grid_sub_title']; ?></p>
                <p class="gform_xooker_price">
                  <span class="gform_xooker_price_cur"><?php echo $pgrid['gformxooker_price_grid_currency']; ?></span>
                  <?php echo $pgrid['gformxooker_price_price']; ?>/
                  <span class="gform_xooker_recurring"><?php echo $pgrid['gformxooker_price_recurring']; ?></span>
                </p> -->

                <div class="gformxooker_subfeat">
                  <?php echo $pgrid['gformxooker_price_subfeat']; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php $pgridIndex++; endforeach; ?>
</div>
<?php endif; ?>

<?php 
if( $a['bottom_content_id']) {
  $topContent = get_post((int) $a['bottom_content_id']);
  if($topContent) {
    echo $topContent->post_content;
  }
}
?>
</div>

<?php
return ob_get_clean();
}
add_shortcode('ezplan_shortcode', 'gformxooker_ezplan_shortcode');