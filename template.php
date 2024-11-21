<?php

function gformxooker_ezsocial_plan_shortcode_v2($atts) {
  $default = array();
  $a = shortcode_atts($default, $atts);
ob_start();
?>

<h3 style="text-align: center">Choose your plan</h3>

<div class="gform_plan_toggle modulev2">
  <div class="gform_plan_toggle_container">
    <div class="tabs">
      <input type="radio" id="radio-1" name="tabs" value="monthly" class="gform_xooker_plan_type_selector">
      <label class="tab" for="radio-1">Monthly</label>
      <input type="radio" id="radio-2" name="tabs" value="annual" class="gform_xooker_plan_type_selector">
      <label class="tab" for="radio-2">Annual</label>
      <span class="glider"></span>
    </div>
  </div>
</div>

<!-- managed starts here -->
<div class="gform_xooker_self_managed">
  <div class="gform_xooker_self_managed_item">
    <p class="gform_xooker_self_managed_item_text_big">Self-Managed</p>
  </div>	
  <div  class="gform_xooker_self_managed_item self_managed_2">
    <div>
      <p class="gform_xooker_self_managed_item_text_big">EZsocial Managed Plans</p>
      <p class="gform_xooker_self_managed_item_text_mid">We do all the work!&#9;&#9;&#9;&#9;2 posts/week per channel</p>
    </div>
  </div>
</div>
<!-- end of managed -->


<!-- start here the grid -->
<div class="gform_xooler_plans_wrapper">
  <!-- MONTHLY STARTS HERE -->
  <div class="gform_xooker_plans modulev2 monthly">
    <!-- MONTHLY DIY-->
    <div class="gform_xooker_item gfxooker-plan-grid" data-plan="DIY Monthly Plan|99">
      <div class="gform_xooker_item_radio">
        <svg class="gform_xooker_plan_indicator" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg> 
      </div>
      <div class="gform_xooker_item_details">
        <div>
          <span class="gform_xooker_item_name gformxooker_shadow">D.I.Y</span>
          <span class="gform_xooker_item_sub">Monthly Plan</span>
          <p class="gform_xooker_price">
            <span class="gform_xooker_price_cur">$</span>
            99/
            <span class="gform_xooker_recurring">month</span>
          </p>
          <p class="gform_xooker_desc">Billed monthly for $99</p>
          <ul class="gform_xooker_features">
            <li>Client Supplied Video Content</li>
            <li>Client Supplied Photos</li>
          </ul>
        </div>
        <div>
          <p class="gform_xooker_highlight">
            <span class="gform_xooker_bigtext">Unlimited Posting</span><br> to all of your<br> <span class="gform_xooker_bigtext">Social Media Channels</span>
          </p>
          
          <div class="gform_xooker_highlight_price gformxooker_shadow">
            <p class="gform_xooker_bigtext">Monthly Plan</p>
            <p class="gform_xooker_price">
              <span class="gform_xooker_price_cur">$</span>
              99/
              <span class="gform_xooker_recurring">month</span>
            </p>
            <ul class="gform_xooker_features">
              <li>$99 Billed monthly</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- END OF MONTHLY DIY -->


    <!-- MONTHLY BASIC-->
    <div class="gform_xooker_item gfxooker-plan-grid"  data-plan="Basic Monthly Plan|199">
      <div class="gform_xooker_item_radio">
        <svg class="gform_xooker_plan_indicator" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg> 
      </div>
      <div class="gform_xooker_item_details">
        <div>
          <span class="gform_xooker_item_name gformxooker_shadow">Basic</span>
          <span class="gform_xooker_item_sub">Monthly Plan</span>
          <p class="gform_xooker_price">
            <span class="gform_xooker_price_cur">$</span>
            199/
            <span class="gform_xooker_recurring">month</span>
          </p>
          <p class="gform_xooker_desc">Billed monthly for $199</p>
          <ul class="gform_xooker_features">
            <li>Client Supplied Video Content</li>
            <li>Client Supplied Photos</li>
            <li>Reputation Management ($99 Value*)</li>
            <li>16 Managed posts per month</li>
          </ul>
        </div>
        <div>
          <p class="gform_xooker_highlight">
            Social Media Creations &amp; Posting<br> <span class="gform_xooker_bigtext">2 Social Media Channels</span>
          </p>
          
          <div class="gform_xooker_highlight_price gformxooker_shadow">
            <p class="gform_xooker_bigtext">Monthly Plan</p>
            <p class="gform_xooker_price">
              <span class="gform_xooker_price_cur">$</span>
              199/
              <span class="gform_xooker_recurring">month</span>
            </p>
            <ul class="gform_xooker_features">
              <li>$199 Billed monthly</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- END OF MONTHLY BASIC -->


    <!-- MONTHLY PRIME-->
    <div class="gform_xooker_item gfxooker-plan-grid" data-plan="Prime Monthly Plan|299">
      <div class="gform_xooker_item_radio">
        <svg class="gform_xooker_plan_indicator" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg> 
      </div>
      <div class="gform_xooker_item_details">
        <div>
          <span class="gform_xooker_item_name gformxooker_shadow">Prime</span>
          <span class="gform_xooker_item_sub">Monthly Plan</span>
          <p class="gform_xooker_price">
            <span class="gform_xooker_price_cur">$</span>
            299/
            <span class="gform_xooker_recurring">month</span>
          </p>
          <p class="gform_xooker_desc">Billed monthly for $299</p>
          <ul class="gform_xooker_features">
            <li>Client Supplied Video Content</li>
            <li>Client Supplied Photos</li>
            <li>Reputation Management ($99 Value*)</li>
            <li>EZhire Service (79 Value*)</li>
            <li>40 Managed posts per month</li>
          </ul>
        </div>
        <div>
          <p class="gform_xooker_highlight">
            Social Media Creations &amp; Posting<br> <span class="gform_xooker_bigtext">5 Social Media Channels</span>
          </p>
          
          <div class="gform_xooker_highlight_price gformxooker_shadow">
            <p class="gform_xooker_bigtext">Monthly Plan</p>
            <p class="gform_xooker_price">
              <span class="gform_xooker_price_cur">$</span>
              299/
              <span class="gform_xooker_recurring">month</span>
            </p>
            <ul class="gform_xooker_features">
              <li>$299 Billed monthly</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- END OF MONTHLY PRIME -->
  </div>
  <!-- END OF MONTHLY -->




  <!-- ANNUAL STARTS HERE -->
  <div class="gform_xooker_plans modulev2 annual">
    <!-- ANNUAL DIY-->
    <div class="gform_xooker_item gfxooker-plan-grid" data-plan="DIY Annual Plan|207">
      <div class="gform_xooker_item_radio">
        <svg class="gform_xooker_plan_indicator" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg> 
      </div>
      <div class="gform_xooker_item_details">
        <div>
          <p class="gform_xooker_item_name gformxooker_shadow">D.I.Y</p>
          <p class="gform_xooker_item_sub">Annual Plan</p>
          <p class="gform_xooker_price">
            <span class="gform_xooker_price_cur">$</span>
            69/
            <span class="gform_xooker_recurring">month</span>
          </p>
          <p class="gform_xooker_less">Save $30/month</p>
          <p class="gform_xooker_desc">Billed quarterly for $207</p>
          <ul class="gform_xooker_features">
            <li>Client Supplied Video Content</li>
            <li>Client Supplied Photos</li>
          </ul>
        </div>
        <div>
          <p class="gform_xooker_highlight">
            <span class="gform_xooker_bigtext">Unlimited Posting</span><br> to all of your<br> <span class="gform_xooker_bigtext">Social Media Channels</span>
          </p>
          
          <div class="gform_xooker_highlight_price gformxooker_shadow">
            <p class="gform_xooker_bigtext">Annual Plan</p>
            <p class="gform_xooker_price">
              <span class="gform_xooker_price_cur">$</span>
              69/
              <span class="gform_xooker_recurring">month</span>
            </p>
            <ul class="gform_xooker_features">
              <li>$207 Billed quarterly</li>
              <li>Save $30 per month</li>
            </ul>
          </div>
          
        </div>
      </div>
    </div>
    <!-- END OF ANNUAL DIY -->


    <!-- ANNUAL BASIC-->
    <div class="gform_xooker_item gfxooker-plan-grid" data-plan="Basic Annual Plan|357">
      <div class="gform_xooker_item_radio">
        <svg class="gform_xooker_plan_indicator" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg> 
      </div>
      <div class="gform_xooker_item_details">
        <div>
          <p class="gform_xooker_item_name gformxooker_shadow">Basic</p>
          <p class="gform_xooker_item_sub">Annual Plan</p>
          <p class="gform_xooker_price">
            <span class="gform_xooker_price_cur">$</span>
            119/
            <span class="gform_xooker_recurring">month</span>
          </p>
          <p class="gform_xooker_less">Save $80/month</p>
          <p class="gform_xooker_desc">Billed quarterly for $357</p>
          <ul class="gform_xooker_features">
            <li>Client Supplied Video Content</li>
            <li>Client Supplied Photos</li>
            <li>Reputation Management ($99 Value*)</li>
            <li>16 Managed posts per month</li>
          </ul>
        </div>
        <div>
          <p class="gform_xooker_highlight">
            Social Media Creations &amp; Posting<br> <span class="gform_xooker_bigtext">2 Social Media Channels</span>
          </p>
          
          <div class="gform_xooker_highlight_price gformxooker_shadow">
            <p class="gform_xooker_bigtext">Annual Plan</p>
            <p class="gform_xooker_price">
              <span class="gform_xooker_price_cur">$</span>
              119/
              <span class="gform_xooker_recurring">month</span>
            </p>
            <ul class="gform_xooker_features">
              <li>$357 Billed quarterly</li>
              <li>Save $80 per month</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- END OF ANNUAL BASIC -->


    <!-- ANNUAL PRIME-->
    <div class="gform_xooker_item gfxooker-plan-grid" data-plan="Prime Annual Plan|597">
      <div class="gform_xooker_item_radio">
        <svg class="gform_xooker_plan_indicator" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg> 
      </div>
      <div class="gform_xooker_item_details">
        <div>
          <p class="gform_xooker_item_name gformxooker_shadow">Prime</p>
          <p class="gform_xooker_item_sub">Annual Plan</p>
          <p class="gform_xooker_price">
            <span class="gform_xooker_price_cur">$</span>
            199/
            <span class="gform_xooker_recurring">month</span>
          </p>
          <p class="gform_xooker_less">Save $100/month</p>
          <p class="gform_xooker_desc">Billed quarterly for $597</p>
          <ul class="gform_xooker_features">
            <li>Client Supplied Video Content</li>
            <li>Client Supplied Photos</li>
            <li>Reputation Management ($99 Value*)</li>
            <li>EZhire Service (79 Value*)</li>
            <li>40 Managed posts per month</li>
          </ul>
        </div>
        <div>
          <p class="gform_xooker_highlight">
            Social Media Creations &amp; Posting<br> <span class="gform_xooker_bigtext">5 Social Media Channels</span>
          </p>
          
          <div class="gform_xooker_highlight_price gformxooker_shadow">
            <p class="gform_xooker_bigtext">Annual Plan</p>
            <p class="gform_xooker_price">
              <span class="gform_xooker_price_cur">$</span>
              199/
              <span class="gform_xooker_recurring">month</span>
            </p>
            <ul class="gform_xooker_features">
              <li>$597 Billed quarterly</li>
              <li>Save $100 per month</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- END OF ANNUAL PRIME -->

  </div>
  <!-- END OF ANNUAL -->
</div>


<p style="text-align:center;font-size:20px;font-weight:bold;color:#ff3737;">Prices shown above are with <strong style="font-size: 30px;">20%</strong> promo code applied. You must enter your promo code at checkout.</p>

<?php
return ob_get_clean();
}
add_shortcode('ezsocial_plan_v2', 'gformxooker_ezsocial_plan_shortcode_v2');