(function ($) {
	var customSvgItems = document.querySelectorAll(
		".wp-menu-image.svg"
	);

	if (!customSvgItems.length) return;

	[].slice.call(customSvgItems).forEach(function (el) {
		var styleValue = el.getAttribute("style");
		if (!styleValue) return;
	
		var matches = styleValue.match(/url\(['"]?([^'"]*)['"]?\)/);
		var newStyleValue = styleValue.replace(
			matches[0],
			matches[0] + " !important"
		);
	
		el.setAttribute("style", newStyleValue);
	});

})(jQuery);
