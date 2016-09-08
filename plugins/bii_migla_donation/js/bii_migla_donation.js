jQuery(function ($) {
	if ($("#migla_donation_form").length) {
		var tableau = [];
		var datajax = {
			'action': 'bii_migla_ajax_userinfo',
		};
		$("body").css("cursor","progress");
		setTimeout(function () {
			$.ajax({
				url: ajaxurl,
				data: datajax,
				dataType: 'html',
				success: function (reponse) {
					tableau = JSON.parse(reponse);
					$("#miglad_firstname + input").val(tableau["prenom"]);
					$("#miglad_lastname + input").val(tableau["nom"]);
					$("#miglad_email + input").val(tableau["mail"]);
					
					$("#miglad_address + input").val(tableau["adresse"]);
					$("#miglad_postalcode + input").val(tableau["code_postal"]);
					$("#miglad_city + input").val(tableau["ville"]);					
				}
			});

			$("body").css("cursor","auto");
		}, 3000);

	}


	$(".migla_amount_choice, #miglaCustomAmount").on("click change", function (e) {
		bii_CL(e.type);

		var formgroup = $(this).parents(".form-group");
		bii_CL(formgroup);
		formgroup.find(".radio-inline").removeClass("selected");
		$(this).parents(".radio-inline").addClass("selected");
	});
});