<?php
  $subscriptionId = gform_get_meta( $entry['id'], 'payment_element_subscription_id' );
  $gfstripe = new GFStripe();
  $gfstripe->include_stripe_api();
  \Stripe\Stripe::setApiKey(gf_stripe()->get_secret_api_key());
  $stripe = new \Stripe\StripeClient(gf_stripe()->get_secret_api_key());
  $subscription = $stripe->subscriptions->retrieve($subscriptionId, [
    'expand' => [
      'customer',
      'latest_invoice',
      'discount.promotion_code'
    ]
  ]);

  if(!$subscription) {
    echo '<p style="text-align: center;">Subscription not found in Stripe.</p>';
    return false;
  }

  $invoice = $subscription['latest_invoice'];
  $firstItem = $subscription['items']['data'][0]['price'];
  $promoCode = isset($subscription['discount']['promotion_code']['code']) ? $subscription['discount']['promotion_code']['code'] : "";
  $customerId = $subscription['customer']['id'];


  $upcomingInvoice = null;
  try {
    $upcomingInvoice = $stripe->invoices->upcoming([
      'subscription' => $subscriptionId,
      'expand' => ['application']
    ]);
  } catch(\Exception $e) { }

  ?>
  <style type="text/css">
    .txt-heading-custom {
      font-size: 20px;
      font-weight: bold;
      margin: 0;
    }
    .txt-heading-custom.th {
      font-size: 14px;
    }
    .tbl-custom-table-plan {
      border-collapse: collapse;
      width: 100%;
    }
    .tbl-custom-table-plan thead tr {
      background: #f1f1f1;
    }
    .tbl-custom-table-plan tr td {
      padding: 5px;
    }
    .tbl-custom-table-plan tr td:not(.first-col) {
      text-align: right;
    }

    .tbl-custom-table-plan tr td:not(.no-border) {
      border-bottom: 1px solid #f1f1f1;
    }

    .custom-metabox-wrap p {
      margin: 0;
    }
  </style>

  <?php 

  $items = $stripe->subscriptionItems->all([
    'subscription' => $subscription['id'],
    'expand' => [
      'data.plan.product'
    ]
  ]);

    $apiURL = rgar($form, 'gformstripcustom_promocode_api_url');
    $needToReplace = array(
        '{promoCodeValue}'
    );
    $replaceTo = array(
      $promoCode
    );
    $apiURL = str_replace($needToReplace, $replaceTo, $apiURL);
    $salesRep = null;
    try {
      $salesRepRequest = wp_remote_get($apiURL, array(
        'headers' => array(
          'Accept' => 'application/json',
          'Content-Type' => 'application/json'
        )
      ));
      $salesRepData = wp_remote_retrieve_body($salesRepRequest);
      $getSalesRepJson = json_decode($salesRepData);
      if(isset($getSalesRepJson[0]->result->data->json)) {
        $salesRep = $getSalesRepJson[0]->result->data->json;
      }
    } catch(\Exception $e) { }
  ?>

  <div class="custom-metabox-wrap">

  <table class="tbl-custom-table-plan">
    <thead>
      <tr>
        <td class="first-col"><h4 class="txt-heading-custom th">Subscription Plan</h4></td>
        <td><h4 class="txt-heading-custom th">QTY</h4></td>
        <td><h4 class="txt-heading-custom th">Price</h4></td>
      </tr>
    </thead>
    <tbody>
      
      <?php foreach($items['data'] as $item): ?>
      <tr>
        <td class="first-col">
          <p style="margin: 0;"><strong><?php echo $item['plan']['product']['name']; ?></strong></p>
        </td>
        <td>
          <p style="margin: 0;"><strong><?php echo $item['quantity']; ?></strong></p>
        </td>
        <td>
        <p style="margin-top: 0;"><?php echo GFCommon::to_money( gformstripecustom_money_get( $item['plan']['amount'] ), strtoupper($subscription['currency']) ) ?></p>
        </td>
      </tr>
      <?php endforeach; ?>

      <tr>
        <td colspan="2" class="no-border">Created</td>
        <td class="no-border">
          <p>
            <?php echo gformstripedate_format($subscription['created'], "M j, Y"); ?>
          </p>
        </td>
      </tr>

      <tr>
        <td colspan="2" class="no-border">Current Period</td>
        <td class="no-border">
          <p>
            <?php echo gformstripedate_format($subscription['current_period_start'], "M j, Y"); ?> - <?php echo gformstripedate_format($subscription['current_period_end'], "M j, Y"); ?>
          </p>
        </td>
      </tr>
      
      <tr>
        <td colspan="2" class="no-border">Billing Cycle</td>
        <td class="no-border">
          <p>Every <?php echo $firstItem['recurring']['interval_count'] > 1 ? $firstItem['recurring']['interval_count']." " : " " ?> <?php echo $firstItem['recurring']['interval_count'] > 1 ? "months" : "month" ?>
        </p>
      </td>
      </tr>

      <tr>
        <td colspan="2" class="no-border">Status</td>
        <td class="no-border"><p><?php echo strtoupper($subscription['status']); ?></p></td>
      </tr>
      
      <?php if( $promoCode ): ?>
      <tr>
        <td colspan="2" class="no-border">Promo Code</td>
        <td class="no-border">
          <p>
            <strong><?php echo $promoCode; ?></strong> - 
            <?php if(isset($invoice['discount'])): ?>
              <span style="color: #6f6f6f;"> &#40;<?php if( $invoice['discount']['coupon']['amount_off'] ): ?><span><?php echo GFCommon::to_money( gformstripecustom_money_get( $invoice['discount']['coupon']['amount_off'] ), strtoupper($invoice['currency']) ); ?></span>
                <?php else: ?>
                  <span><?php echo $invoice['discount']['coupon']['percent_off']; ?>%</span>
                <?php endif; ?>
                <?php echo $invoice['discount']['coupon']['duration'] ?> &#41;</span>
            <?php endif; ?>
          </p>
        </td>
      </tr>
      <?php endif; ?>
      
      <?php if($invoice['subtotal']): ?>
      <tr>
        <td colspan="2" class="no-border">Subtotal</td>
        <td class="no-border"><p><?php echo GFCommon::to_money( gformstripecustom_money_get( $invoice['subtotal'] ), strtoupper($invoice['currency']) ); ?></p></td>
      </tr>
      <?php endif; ?>

      <?php if($invoice['total_discount_amounts'][0]['amount']): ?>
      <tr>
        <td colspan="2" class="no-border">Discount</td>
        <td class="no-border"><p>-<?php echo GFCommon::to_money( gformstripecustom_money_get( $invoice['total_discount_amounts'][0]['amount'] ), strtoupper($invoice['currency']) ); ?></p></td>
      </tr>
      <?php endif; ?>

      <tr>
        <td colspan="2" class="no-border">Amount</td>
        <td class="no-border"><p><?php echo GFCommon::to_money( gformstripecustom_money_get( $invoice['amount_paid'] ), strtoupper($invoice['currency']) ); ?></p></td>
      </tr>

    </tbody>
  </table>


  <table style="margin-top: 10px;">
    <tr>
      <td>
        <a href="<?php echo get_rest_url(); ?>gformxooker/v1/customer-portal?customer=<?php echo $customerId; ?>&redirect=<?php echo home_url(); ?>" target="_blank" class="button" style="margin-right:5px;">Manage Subscription</a>
        <a href="<?php echo $invoice['hosted_invoice_url']; ?>" target="_blank" class="button">View Invoice</a>
      </td>
    </tr>
  </table>

  <?php if( $salesRep ): ?>
  <table style="margin-top: 20px;">
    <tr>
      <td>
        <h4 class="txt-heading-custom" style="margin-top: 20px;">Sales Representative</h4>
      </td>
    </tr>
    <tr>
      <td>
        <p>Name: <?php echo $salesRep->first_name; ?> <?php echo $salesRep->last_name; ?></p>
      </td>
    </tr>
    <tr>
      <td>
        <p>Promo Code: 
          <strong><?php echo $promoCode; ?></strong> - 
          <?php if(isset($subscription['discount'])): ?>
            <span style="color: #6f6f6f;"> &#40;<?php if( $subscription['discount']['coupon']['amount_off'] ): ?><span><?php echo GFCommon::to_money( gformstripecustom_money_get( $subscription['discount']['coupon']['amount_off'] ), strtoupper($subscription['currency']) ); ?></span>
              <?php else: ?>
                <span><?php echo $subscription['discount']['coupon']['percent_off']; ?>%</span>
              <?php endif; ?>
              <?php echo $subscription['discount']['coupon']['duration'] ?> &#41;</span>
          <?php endif; ?>
        </p>
      </td>
    </tr>
  </table>
  <?php endif; ?>


  <?php if( $upcomingInvoice ): 
    $upcomingPeriodEnd = strtotime('+' .  $firstItem['recurring']['interval_count'] . ' ' . $firstItem['recurring']['interval'], $upcomingInvoice['next_payment_attempt']);
    ?>
  <table>
    <tr>
      <td>
        <h4 class="txt-heading-custom" style="margin-top: 20px;">Upcoming invoice</h4>
        <p>This is a preview of the invoice that will be billed on <?php echo gformstripedate_format($upcomingInvoice['next_payment_attempt'], "M j"); ?>. It may change if the subscription is updated.</p>
      </td>
    </tr>
  </table>
  
  <table class="tbl-custom-table-plan">
    <thead>
      <tr>
        <td class="first-col"><h4 class="txt-heading-custom th">Description</h4></td>
        <td><h4 class="txt-heading-custom th">QTY</h4></td>
        <td><h4 class="txt-heading-custom th">Price</h4></td>
      </tr>
    </thead>
    <tbody>
      
      <?php foreach($items['data'] as $item): ?>
      <tr>
        <td class="first-col">
          <p style="margin: 0;"><strong><?php echo $item['plan']['product']['name']; ?></strong></p>
        </td>
        <td>
          <p style="margin: 0;"><strong><?php echo $item['quantity']; ?></strong></p>
        </td>
        <td>
        <p style="margin-top: 0;"><?php echo GFCommon::to_money( gformstripecustom_money_get( $item['plan']['amount'] ), strtoupper($subscription['currency']) ) ?></p>
        </td>
      </tr>
      <?php endforeach; ?>

      <tr>
        <td colspan="2" class="no-border">Next Period</td>
        <td class="no-border">
          <p>
            <?php echo gformstripedate_format($upcomingInvoice['next_payment_attempt'], "M j, Y"); ?> - <?php echo gformstripedate_format($upcomingPeriodEnd, "M j, Y"); ?>
          </p>
        </td>
      </tr>
      
      <tr>
        <td colspan="2" class="no-border">Billing Cycle</td>
        <td class="no-border">
          <p>Every <?php echo $firstItem['recurring']['interval_count'] > 1 ? $firstItem['recurring']['interval_count']." " : " " ?> <?php echo $firstItem['recurring']['interval_count'] > 1 ? "months" : "month" ?></p>
      </td>
      </tr>
      
      <?php if( $promoCode ): ?>
      <tr>
        <td colspan="2" class="no-border">Promo Code</td>
        <td class="no-border">
          <p>
            <strong><?php echo $promoCode; ?></strong> - 
            <?php if(isset($upcomingInvoice['discount'])): ?>
              <span style="color: #6f6f6f;"> &#40;<?php if( $upcomingInvoice['discount']['coupon']['amount_off'] ): ?><span><?php echo GFCommon::to_money( gformstripecustom_money_get( $upcomingInvoice['discount']['coupon']['amount_off'] ), strtoupper($upcomingInvoice['currency']) ); ?></span>
                <?php else: ?>
                  <span><?php echo $upcomingInvoice['discount']['coupon']['percent_off']; ?>%</span>
                <?php endif; ?>
                <?php echo $upcomingInvoice['discount']['coupon']['duration'] ?> &#41;</span>
            <?php endif; ?>
          </p>
        </td>
      </tr>
      <?php endif; ?>

      <tr>
        <td colspan="2" class="no-border">Subtotal</td>
        <td class="no-border"><p><?php echo GFCommon::to_money( gformstripecustom_money_get( $upcomingInvoice['subtotal'] ), strtoupper($upcomingInvoice['currency']) ); ?></p></td>
      </tr>

      <?php if($upcomingInvoice['total_discount_amounts'][0]['amount']): ?>
      <tr>
        <td colspan="2" class="no-border">Discount</td>
        <td class="no-border"><p>-<?php echo GFCommon::to_money( gformstripecustom_money_get( $upcomingInvoice['total_discount_amounts'][0]['amount'] ), strtoupper($upcomingInvoice['currency']) ); ?></p></td>
      </tr>
      <?php endif; ?>

      <tr>
        <td colspan="2" class="no-border">Amount due</td>
        <td class="no-border"><p><?php echo GFCommon::to_money( gformstripecustom_money_get( $upcomingInvoice['amount_due'] ), strtoupper($upcomingInvoice['currency']) ); ?></p></td>
      </tr>

    </tbody>
  </table>

  <?php endif; ?>


  </div>