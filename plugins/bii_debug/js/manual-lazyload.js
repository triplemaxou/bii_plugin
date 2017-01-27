/* Events Lazyload */
var zonebottomplus = 10;
jQuery(function () {
	//events
	try {		
		jQuery(".lazyquick-lg").lazyload({
			event: "loadquick-lg"
		});
		jQuery(".lazyquick-md").lazyload({
			event: "loadquick-md"
		});
		jQuery(".lazyquick-sm").lazyload({
			event: "loadquick-sm"
		});
		jQuery(".lazyquick-xs").lazyload({
			event: "loadquick-xs"
		});
		jQuery(".lazyquick-xxs").lazyload({
			event: "loadquick-xxs"
		});
		jQuery(".lazyquick").lazyload({
			event: "loadquick"
		});
	} catch (e) {
		console.log("lazyload n'existe pas");
		jQuery("img[data-original]").each(function () {
			var dor = jQuery(this).attr("data-original");
			var datasrcset = jQuery(this).attr("data-srcset");
			jQuery(this).attr("src", dor);
			if(datasrcset){
				jQuery(this).attr("data-srcset", datasrcset);
				jQuery(this).attr("data-srcset","");
			}
		});
	}
	loadLazyquick();
	jQuery(window).on("scroll resize",function () {		
		loadLazyquick();
	});

});

function loadLazyquick() {
	var size = getWindowSize();
	
	
	var zone = zoneFenetre();
	var ytop = zone.ytop;
	var ybottom = zone.ybottom;
	if((".bii-screenwidth-listener").length){
		var bii_size = "bii_"+size;
		var sizes = ["bii_xxs","bii_xs","bii_sm","bii_md","bii_lg"];
		var index = sizes.indexOf(bii_size);
		delete sizes[index];
		sizes = sizes.join(" ");
//		bii_CL(sizes);
		jQuery(".bii-screenwidth-listener").removeClass(sizes).addClass(bii_size);
	}
	
	
	jQuery(".lazyquick:not(.loaded)").each(function () {
		loadquick(jQuery(this), size, ytop, ybottom);
	});
	jQuery(".lazyquick-" + size + ":not(.loaded)").each(function () {
		loadquick(jQuery(this), size, ytop, ybottom);
	});
	jQuery(".lazyquick-" + size + ":not(.loaded)").each(function () {
		loadquick(jQuery(this), size, ytop, ybottom);
	});
	jQuery("#carte-home:not(.loaded)").trigger("lazyquick").addClass("loaded");
//	bii_CL(jQuery(window).width()+" "+jQuery(window).height());
}

function loadquick($element, size, zonetop, zonebottom) {
	var top = $element.offset().top;
	var bottom = top + $element.height();
	if (bottom >= zonetop && top <= zonebottom && !$element.parents(".hidden, .hidden-" + size).length) {
		$element.trigger("loadquick");
		$element.trigger("loadquick-" + size);
		$element.addClass("loaded");
		console.log("loadquick-" + size);
	}
}





function getWindowSize() {
	var windowsize = "";
	if (window.matchMedia(bii_xxsmall).matches) {
		windowsize = "xxs";
	} else if (window.matchMedia(bii_xsmall).matches) {
		windowsize = "xs";
	} else if (window.matchMedia(bii_small).matches) {
		windowsize = "sm";
	} else if (window.matchMedia(bii_medium).matches) {
		windowsize = "md";
	} else {
		windowsize = "lg";
	}

//	console.log(windowsize);

	return windowsize;
}
	