jQuery(function ($) {
	var intervalrefesh = setInterval(function () {
		$('.dschart.done').each(function () {
//			console.log("timeoutrefesh");
			$(this).removeClass("done");
		});
	}, 5000);
	var intervalrefesh2 = setInterval(function () {
//		console.log("timeoutrefesh2");
//		buildCharts();
		$(window).trigger("regenCharts");
	}, 60000);

	var intervalrefesh3 = setInterval(function () {
		$(window).trigger("regenWidgets");
		positivenegative();
	}, 60000 / 2);
	var intervalrefesh4 = setInterval(function () {
		positivenegative();
	}, 1000);

	$(".bii_finance_marquee_dialog").dialog({
		autoOpen: false,
		show: {
			effect: "blind",
			duration: 1000
		},
		hide: {
			effect: "explode",
			duration: 1000
		}
	});
	
	function positivenegative(){
		$(".sm-card").each(function(){
			var val = $(this).find(".sm-quote-div .sm-change").text();
//			bii_CL(val);
			if(val.indexOf("+") != -1){
				$(this).addClass("positive").removeClass("negative");
			}else{
				$(this).addClass("negative").removeClass("positive");
			}
		});
	}

	$("body").on("click", '.bii_marquees .sm-widget', function () {
		var ds = $(this).attr("data-symbol");
		
		var html = $(".bii_finance_marquee_dialog").html();
		var toreplace = $(".bii_finance_marquee_dialog .sm-widget").attr("data-symbol");
		var nhtml = html.replace(toreplace,ds);
		nhtml = nhtml.replace(toreplace,ds);
//		bii_CL(toreplace);
//		bii_CL(ds);
		$(".bii_finance_marquee_dialog").html(nhtml);
		$(window).trigger("regenWidgets");
		$(".bii_finance_marquee_dialog").dialog("open");
	});
});