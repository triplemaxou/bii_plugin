jQuery(function ($) {
	$(".synchro-photo").click(function () {
		var $fa = $(this).find(".fa-refresh");
		$(this).addClass("btn-info").removeClass("btn-default");
		$fa.addClass("fa-spin");
		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_synchronize_photos'
			},
			dataType: 'html',
			success: function (reponse) {
				$fa.removeClass("fa-spin");
				$(this).addClass("btn-default").removeClass("btn-info");
			}
		});
	});

	$("#chooseinstance").on("change", function () {
		var val = $(this).val();
		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_change_instance',
				'newinstance': val
			},
			dataType: 'html',
			success: function (reponse) {
//				alert("ok");
				location.reload();
			}
		});
	});

	$(".bii_upval").on("click", function (e) {
		e.preventDefault();
//		alert("ok");
		var val = $(this).attr("data-newval");
		var option = $(this).attr("data-option");
		var html = $(this).html();
		var fa = $(this).find(".fa");
		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_change_wp_option',
				'option': option,
				'newval': val
			},
			dataType: 'html',
			success: function (reponse) {
//				alert(reponse);
				location.reload();
			},
			error: function () {
				alert("erreur");
			}
		});
	});

	$(".publier").on("click", function (e) {
		e.preventDefault();
		$("#poststuff").submit();
	});

	$(".hide-relative").on("click", function () {
		$(".hide-relative").removeClass("active");
		$(".bii_option").addClass("hidden");
		$(this).addClass("active");
		var dr = $(this).attr("data-relative");
		$("." + dr).removeClass('hidden');
		if ($(this).hasClass("hide-publier")) {
			$(".publier").addClass("hidden");
		} else {
			$(".publier").removeClass("hidden");
		}
	});

	$(".update-nag ").addClass("hidden");

	$(".formlevels .add-level").on("click", function (e) {
		e.preventDefault();
		//bii_add_new_level
		var index = $("#product_level_count").val() * 1 + 1;
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {action: 'bii_add_new_level', index: index, post_id: $("#project_post_id").val()},
			success: function (newlevel) {
				$(".container-levels").prepend(newlevel);
				$(".container-levels .otherform:first-of-type").hide();
				$("#product_level_count").val(index);
				$(".remove-level").show();
				$(".container-levels .otherform:first-of-type").show(700);
			}
		});
	});
	$(".formlevels .remove-level").on("click", function (e) {
		e.preventDefault();
		var index = $("#product_level_count").val() * 1 - 1;
		$("#product_level_count").val(index);
		$(".container-levels .otherform:first-of-type").hide(500, function () {
			$(this).remove();
		});

		if (index == 1) {
			$(".remove-level").hide();
		}
	});
});