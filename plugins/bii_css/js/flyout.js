jQuery(function ($) {
	if ($(".bii-flyout").length) {
		$(".bii-onglet").on("click", function () {
			var parent = $(this).parents(".bii-flyout");
			if (parent.hasClass("deroule")) {
				parent.animate({
					left: "-283px"
				}, 1000, "easeOutBounce");
				parent.removeClass("deroule");
			} else {
				parent.animate({
					left: "18px"
				}, 900, "easeOutBounce");
				parent.addClass("deroule");
			}
		});
	}
});