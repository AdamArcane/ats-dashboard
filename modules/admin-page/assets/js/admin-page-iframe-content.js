(function () {
	window.addEventListener('load', handleWindowLoad, false);
	window.addEventListener('resize', handleWindowResize, false);

	function handleWindowLoad(_e) {
		checkPageHeight();

		recheckPageHeightWithTimeout(500);
		recheckPageHeightWithTimeout(1000);
		recheckPageHeightWithTimeout(1500);
		recheckPageHeightWithTimeout(2000);
		recheckPageHeightWithTimeout(2500);
		recheckPageHeightWithTimeout(3000);
		recheckPageHeightWithTimeout(3500);
		recheckPageHeightWithTimeout(4000);
	}

	function handleWindowResize(_e) {
		checkPageHeight();
		recheckPageHeightWithTimeout(500);
		recheckPageHeightWithTimeout(1000);
	}

	function recheckPageHeightWithTimeout(timeoutMs) {
		setTimeout(function () {
			checkPageHeight();
		}, timeoutMs);
	}

	function checkPageHeight() {
		var pageOuterHeight = document.documentElement.offsetHeight;

		var data = {
			height: pageOuterHeight
		};

		/**
		 * This file is running in a page that is inside iframe.
		 * Let's notify the parent's window via postMessage and pass the data.
		 */
		window.parent.postMessage(data, '*');
	}
}());