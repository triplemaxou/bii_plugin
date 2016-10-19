jQuery(function ($) {
	$(".bii-invisible").hide(0);
	$('.notice-info[data-group*="wpml-st-string-scan"], .settings-error').hide();
	
	
	$("body").on("click",".bii-make-this-visible",function(){
		var selector = $(this).attr("data-selector");
		$(selector).show();
		$(this).removeClass("bii-make-this-visible").addClass("bii-make-this-invisible");
		$(this).find(".fa-plus").removeClass("fa-plus").addClass("fa-minus");
	});
	$("body").on("click",".bii-make-this-invisible",function(){
		var selector = $(this).attr("data-selector");
		$(selector).hide();
		$(this).addClass("bii-make-this-visible").removeClass("bii-make-this-invisible");
		$(this).find(".fa-minus").addClass("fa-plus").removeClass("fa-minus");
	});
	
	$(".bii_action_ajax").on("click", function () {
		var action = $(this).attr("data-action");
		var success = $(this).attr("data-success");
		var $fa = $(this).find(".fa-refresh");
		var param = $(this).attr("data-param");
		if ($fa.length) {
			$fa.addClass("fa-spin");
		}
		

		if (action) {
			$.ajax({
				url: ajaxurl,
				data: {
					'action': action
				},
				dataType: 'html',
				success: function (reponse) {
					if ($fa.length) {
						$fa.removeClass("fa-spin");
					}
					dosuccess(success, reponse, param);
				},
				error: function () {
					bii_CL("error");
				}
			});
		}
	});


	function dosuccess(success, reponse, param) {
		if (success == "refresh") {
			location.reload();
		}
		if (success == "alert") {
			alert(reponse);
		}
		if (success == "alertparam") {
			alert(param);
		}
		if (success == "log") {
			bii_CL(reponse);
		}
		if (success == "logparam") {
			bii_CL(param);
		}
	}
});
