jQuery(document).ready( function() {
	jQuery('.gfxooker-plan-grid').on('click', function() {
		jQuery('.gfxooker-plan-grid').removeClass('active');
		jQuery(this).addClass('active');
		
		jQuery('[name="input_24"]').attr('checked', false);
		jQuery('[name="input_24"][value="'+jQuery(this).data('plan')+'"]').attr('checked', true);
		
		var gformXookerPlanLink = jQuery('#gformxooker_plan_link');
		if(gformXookerPlanLink.length) {
 			var gformXookerPlanLinkHref = gformXookerPlanLink.attr('href');
			const gformXookerPlanLinkHrefUrl = new URL(gformXookerPlanLinkHref);
			gformXookerPlanLinkHrefUrl.searchParams.set("gformxooker_product", jQuery(this).data('plan'));
			gformXookerPlanLink.attr('href', decodeURIComponent(gformXookerPlanLinkHrefUrl.href));
		}
	});
	
	var gformXookerProductVal = jQuery('[name="input_24"]:checked').val();
	if(gformXookerProductVal && gformXookerProductVal.toLowerCase().includes("annual")) {
		jQuery('.gform_xooker_plan_type_selector[value="annual"]').prop('checked', true);
		jQuery('.gform_xooker_plans.annual').addClass('active');
	} else {
		jQuery('.gform_xooker_plan_type_selector[value="monthly"]').prop('checked', true);
		jQuery('.gform_xooker_plans.monthly').addClass('active');
	}
	
	// default select of plan
	if(gformXookerProductVal) {
		jQuery(`[data-plan="${gformXookerProductVal}"]`).addClass('active');
	}
	
	jQuery('.gform_plan_toggle_container input').change( function() {
		jQuery('.gform_xooker_plans').removeClass('active');
		jQuery(`.gform_xooker_plans.${jQuery(this).val()}`).addClass('active');
	});
});