jQuery(function ($) {
	$(".sidebar_left_home, .sidebar_right_home").addClass("vc_hidden-sm vc_hidden-xs");
//	if ($(".page-id-1207").length) {
//		$(".page-id-1207").addClass("bii-traduit-de-344");
//	}
	if ($(".bii-traduit-de-613").length) {
		bii_CL(".bii-traduit-de-613");
		$(window).on("resize", function () {
			bii_CL(".bii-traduit-de-613");
			$(".vc_grid-item.vc_col-sm-3").addClass("vc_col-lg-3 vc_col-md-4 vc_col-sm-6 vc_col-xs-12").removeClass("vc_col-sm-3");
		});

	}
	if ($(".page-id-1240").length) {
		$(".page-id-1240").addClass("bii-traduit-de-399");
	}
	if ($(".page-id-1242").length) {
		$(".page-id-1242").addClass("bii-traduit-de-399");
	}
	if ($(".bii-member-mosaic ").length) {
		$(window).on("load resize", function () {
			$(".bii-member-mosaic ").css("height", function () {
				return $(this).find(".bii-member-tile").height() * 3;
			});
		});
	}

	if ($("#bii-select-hashtag").length) {
		$("#bii-select-hashtag").on("change", function () {
			var val = $(this).val();
			if (val) {
				window.location.assign(val);
			}
		});
	}

	if ($(".bii-cut-and-paste").length) {
		$("#bii_cover option").each(function () {
			var val = $(this).val();
			var html = $(this).html();
			if (val.indexOf("img_top_WWW_home.jpg") != -1) {
				html = "Image par défaut";
			}
			if (val.indexOf("bg_slide_invit_WWW.jpg") != -1) {
				html = "Image 1";
			}
			if (val.indexOf("img_slide2_full_page_wonderwomenworld.jpg") != -1) {
				html = "Image 2";
			}
			$(this).html(html);
		});


		$(".um-field-bii_cover").addClass("bii-cut-and-paste");
		$(".um-field-bii_cover").attr("data-selector", "h1.page-title");
		bii_cut_and_paste();
		$(".main-title .container").addClass("bii-custom-overlay");
		$(".um-profile-body").append('<input id="bii_cover_hidden" type="hidden" value="' + $("#bii_cover").val() + '" name="bii_cover" />');
		$("#bii_cover").on('change', function (e) {
			var val = $(this).val();
			bii_CL(val);
			$(".main-title.main-center-title").css("background-image", 'url("' + val + '")');
			$("#bii_cover_hidden").val(val);
		});

	}

	

	if ($("#menu-menu_home_content, #menu-menu_home_content_en ").length) {
		bii_CL("ok");
		$("#menu-menu_home_content, #menu-menu_home_content_en").on("click", function (e) {
			bii_CL(e.target);
		});

		$("#menu-menu_home_content > .menu-item > a, #menu-menu_home_content_en > .menu-item > a").on("click", function (e) {
			e.preventDefault();
//			bii_CL("click");
			bii_CL("click a");

			$("#menu-menu_home_content .caret, #menu-menu_home_content_en .caret").removeClass("active");
			$(this).find(".caret").addClass("active");
			$("#menu-menu_home_content .menu-item, #menu-menu_home_content_en .menu-item ").removeClass("active");
			$(this).parents(".menu-item").addClass("active");
			$("#menu-menu_home_content .sub-menu, #menu-menu_home_content_en .sub-menu").hide(500);
			$(this).siblings(".sub-menu").slideToggle("slow");

		});

		$("#menu-menu_home_content > .menu-item:first-child a , #menu-menu_home_content_en > .menu-item:first-child a").trigger("click");
	}
	if ($(".bii-changecover").length) {
		$(".main-title.main-center-title").css("background-image", 'url("' + $(".bii-changecover").attr("data-cover") + '")');
	}





	$(".sidebar-3lr").addClass("hidden-xs hidden-sm");
	var widgetcontainermain = null, widgetcontainerextra = null;
	if ($(".sidebar-main").length) {
		widgetcontainermain = $(".sidebar-main .widgets-container");

	}
	if ($(".sidebar-extra").length) {
		widgetcontainerextra = $(".sidebar-extra .inner-content");

	}
	$(window).on("scroll load resize", function (event) {
		bii_fix_widget_top(widgetcontainermain, 310, event);
		bii_fix_widget_top(widgetcontainerextra, 310, event);
	});

	$(".um-hashtag").on("click", function (e) {
		e.preventDefault();
		if ($(this).hasClass("selected")) {
			$(this).removeClass("selected");
		} else {
			$(this).addClass("selected");
		}
	});

	jQuery(document).off('submit', '.um-activity-publish');
	jQuery(document).on('submit', '.um-activity-publish', function (e) {
		e.preventDefault();

		var this_form = jQuery(this);
		if (this_form.find('textarea').val().trim().length == 0 && this_form.find('#_post_img').val().trim().length == 0) {
			this_form.find('textarea').focus();
		} else {

			jQuery('.um-activity-post').addClass('um-disabled');
			formdata = this_form.serialize();
			var hashtags = "";
			var i;
			$(".um-hashtag.selected").each(function () {
				if (hashtags == "") {
					hashtags = "&_hashtags=";
					hashtags += $(this).text();
				} else {
					hashtags += "," + $(this).text();
				}

			});
			formdata += hashtags;

			bii_CL(formdata);
			// new post
			if (this_form.find('#_post_id').val() == 0) {

				var wall = this_form.parents('.um').find('.um-activity-wall');
				var clone = wall.find('.um-activity-clone:first');
				var clonel = clone.clone();
				clonel.prependTo(wall).addClass('unready').fadeIn().find('.um-activity-bodyinner-txt').html(this_form.find('textarea').val());
				if (this_form.find('#_post_img').val().trim().length > 0) {
					if (clonel.find('.um-activity-bodyinner-txt').html().trim().length == 0) {
						clonel.find('.um-activity-bodyinner-txt').hide();
					}
					clonel.prependTo(wall).find('.um-activity-bodyinner-photo').html('<a href="#" class="um-photo-modal" data-src="' + this_form.find('#_post_img').val() + '"><img src="' + this_form.find('#_post_img').val() + '" alt="" /></a>');
				}

				this_form.find('textarea').val('').height('auto');
				this_form.find('#_post_img').val('');

				this_form.find('.um-activity-preview').hide();

				jQuery('.um-activity-textarea-elem').attr('placeholder', jQuery('.um-activity-textarea-elem').attr('data-ph'));

			} else {

				this_form.css({opacity: 0.5});

			}

			jQuery.ajax({
				url: ultimatemember_ajax_url,
				type: 'post',
				dataType: 'json',
				data: formdata,
				success: function (data) {

					// new post
					if (this_form.find('#_post_id').val() == 0) {

						this_form.find('.um-activity-preview').find('img').attr('src', '');

						clonel.removeClass('unready').attr('id', 'postid-' + data.postid).removeClass('um-activity-clone');
						clonel.find('.um-activity-comment-textarea').show();
						if (data.orig_content) {
							clonel.find('.um-activity-bodyinner-edit textarea').val(data.orig_content);
						} else {
							clonel.find('.um-activity-bodyinner-edit textarea').val('');
						}
						if (data.content) {
							clonel.find('.um-activity-bodyinner-txt').html(data.content);
						} else {
							clonel.find('.um-activity-bodyinner-txt').empty().hide();
						}

						if (data.link) {
							if (clonel.find('.um-activity-bodyinner-txt').find('.post-meta').length) {
								clonel.find('.um-activity-bodyinner-txt').show().find('.post-meta').replaceWith(data.link);
							} else {
								clonel.find('.um-activity-bodyinner-txt').show().append(data.link);
							}
						}

						if (data.photo) {
							clonel.find('.um-activity-bodyinner-edit input#_photo_').val(data.photo);
							clonel.find('.um-activity-bodyinner-photo').find('a').attr('data-src', data.photo);
							clonel.find('.um-activity-bodyinner-photo').find('a').attr('href', data.photo);
							clonel.find('.um-activity-bodyinner-photo').find('img').attr('src', data.photo);
						} else {
							clonel.find('.um-activity-bodyinner-edit input#_photo_').val('');
						}
						if (data.video) {
							clonel.find('.um-activity-bodyinner-video').html(data.video);
						}

						clonel.find('.um-activity-metadata a').attr('href', data.permalink);

						autosize(clonel.find('.um-activity-comment-textarea'));

					} else {

						elem = this_form.parents('.um-activity-widget');
						elem.find('form').remove();
						if (data.orig_content) {
							elem.find('.um-activity-bodyinner-edit textarea').val(data.orig_content);
						} else {
							elem.find('.um-activity-bodyinner-edit textarea').val('');
						}

						if (data.content) {
							elem.find('.um-activity-bodyinner-txt').html(data.content);
							elem.find('.um-activity-bodyinner-txt').show();
						} else {
							elem.find('.um-activity-bodyinner-txt').empty().hide();
						}

						if (data.link) {
							if (elem.find('.um-activity-bodyinner-txt').find('.post-meta').length) {
								elem.find('.um-activity-bodyinner-txt').show().find('.post-meta').replaceWith(data.link);
							} else {
								elem.find('.um-activity-bodyinner-txt').show().append(data.link);
							}
						}

						if (data.photo) {
							elem.find('.um-activity-bodyinner-edit input#_photo_').val(data.photo);
							if (elem.find('.um-activity-bodyinner-photo').find('a').length == 0) {
								elem.find('.um-activity-bodyinner-photo').html('<a href="' + data.photo + '"><img src="' + data.photo + '" alt="" /></a>');
							} else {
								elem.find('.um-activity-bodyinner-photo').find('a').attr('href', data.photo);
								elem.find('.um-activity-bodyinner-photo').find('img').attr('src', data.photo);
							}
							elem.find('.um-activity-bodyinner-photo').show();
						} else {
							elem.find('.um-activity-bodyinner-edit input#_photo_').val('');
							elem.find('.um-activity-bodyinner-photo').empty().hide();
						}
						if (data.video) {
							elem.find('.um-activity-bodyinner-video').html(data.video);
							elem.find('.um-activity-bodyinner-video').show();
						} else {
							elem.find('.um-activity-bodyinner-video').empty().hide();
						}

					}

					UM_wall_autocomplete_start()

				}

			});

		}
		return false;
	});

	function bii_fix_widget_top($element, topy, event) {
		if ($element && $element.length) {
//			var top = $element.offset().top;
			var zone = zoneFenetre();
//			var direction = directionScroll();
			var size = getWindowSize();
			if (size == "lg" || size == "md") {
				if (event.type == "resize") {
					$element.attr("data-iwidth", null);
				}
				if (!$element.attr("data-iwidth")) {
					if (event.type == "resize") {
						$element.css("width", "auto");
					}
					$element.attr("data-iwidth", $element.width());
				}

				var width = $element.attr("data-iwidth");


				if (zone.ytop > topy) {
					$element.css("position", "fixed");
					$element.css("top", "50px");
					$element.css("width", width + "px");
				}
				if (zone.ytop < topy) {
					$element.css("position", "static");
					$element.css("top", "auto");
					$element.css("width", "auto");
				}

			}
		}
	}

});

