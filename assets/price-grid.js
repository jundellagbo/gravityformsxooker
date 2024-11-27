jQuery(document).ready( function() {
	jQuery('.gfxooker-plan-grid').on('click', function() {
		jQuery('.gfxooker-plan-grid').removeClass('active');
		jQuery(this).addClass('active');
		
		jQuery('.gformproduct_dynamic input').attr('checked', false);
		jQuery('.gformproduct_dynamic input[value="'+jQuery(this).data('plan')+'"]').attr('checked', true);
		
		var gformXookerPlanLink = jQuery('#gformxooker_plan_link');
		if(gformXookerPlanLink.length) {
 			var gformXookerPlanLinkHref = gformXookerPlanLink.attr('href');
			const gformXookerPlanLinkHrefUrl = new URL(gformXookerPlanLinkHref);
			gformXookerPlanLinkHrefUrl.searchParams.set("gformxooker_product", jQuery(this).data('plan'));

			if(jQuery('.gform_plan_toggle_container input:checked').val()) {
				gformXookerPlanLinkHrefUrl.searchParams.set("gformxooker_product_option", jQuery('.gform_plan_toggle_container input:checked').val());
			}

			gformXookerPlanLink.attr('href', decodeURIComponent(gformXookerPlanLinkHrefUrl.href));
		}
	});
	
	var gformXookerProductVal = jQuery('.gformproduct_dynamic input:checked').val();

	// default select of plan
	if(gformXookerProductVal) {
		jQuery(`[data-plan="${gformXookerProductVal}"]`).addClass('active');
	}
	
	jQuery('.gform_plan_toggle_container input').change( function() {
		jQuery('.gform_xooker_plans').removeClass('active');
		jQuery(`.gform_xooker_plans.${jQuery(this).val()}`).addClass('active');
	});
	
	var gformXookerPlanUrl = new URLSearchParams(window.location.search);
	var gformXookerSelectedPlanParam = gformXookerPlanUrl.get('gformxooker_product_option');
	if(gformXookerSelectedPlanParam) {
		jQuery('.gform_plan_toggle_container input[value="' + gformXookerSelectedPlanParam + '"]').attr('checked', true)
		jQuery(`.gform_xooker_plans`).removeClass('active')
		jQuery(`.gform_xooker_plans.${gformXookerSelectedPlanParam}`).addClass('active')
	}
});