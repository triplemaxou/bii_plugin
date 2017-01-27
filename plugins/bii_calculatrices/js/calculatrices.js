jQuery(function ($) {



	if ($(".bii-calculatrice").length) {
		bii_CL("bii-calculatrice");


		$(".cbx-data-change").on("click keyup", function () {
			var dc = $(this).attr('data-change');
			var valeur = 0;
			if ($(this).is(":checked")) {
				valeur = 1;
			}
			bii_CL(valeur);
			$('#' + dc).val(valeur);
		});

		$(".bii-calculer").on("click keyup", function (e) {
			e.preventDefault();			
			var form = $(this).parents(".bii-calc-content");
			var result = form.siblings(".bii-result");
			
			result.html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Loading...</span>');
			
			var classe = $(this).parents(".bii-calculatrice").attr("data-calc");
//			console.log(ser);
			var datajax = {
				'action': 'bii_ajax_calc',
				'nom_calculatrice': classe
			};
			form.find("input, textarea, select").each(function () {
				var name = $(this).attr("name");
				if (name) {
					if (-1 == name.indexOf('-cbx')) {
						var value = $(this).val();
						datajax[name] = value;
					}
				}
			});

			bii_CL(datajax);

			$.ajax({
				url: ajaxurl,
				data: datajax,
				dataType: 'html',
				success: function (reponse) {
					result.html(reponse);
				}
			});
		});
	}
});