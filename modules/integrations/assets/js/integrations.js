/**
 * Tab navigation for the Integrations page.
 *
 * Mirrors the Settings page tab behavior (assets/js/settings.js) but scoped
 * to the Integrations form/panels so it doesn't rely on the settings page
 * markup being present.
 *
 * @param {Object} $ jQuery object.
 * @return {Object}
 */
(function ($) {
	init();

	/**
	 * Initialize the module.
	 */
	function init() {
		setupTabsNavigation();
	}

	/**
	 * Setup the tabs navigation for the integrations page.
	 */
	function setupTabsNavigation() {
		$(".heatbox-tab-nav-item").on("click", function () {
			$(".heatbox-tab-nav-item").removeClass("active");
			$(this).addClass("active");

			const link = this.querySelector("a");
			const hashValue = link?.href.substring(link.href.indexOf("#") + 1) ?? "";

			showPanel(hashValue);
		});

		window.addEventListener("load", function () {
			var hashValue = window.location.hash.substr(1);

			if (!hashValue) {
				hashValue = "mainwp";
			}

			$(".heatbox-tab-nav-item").removeClass("active");
			$(".heatbox-tab-nav-item." + hashValue + "-panel").addClass("active");

			showPanel(hashValue);
		});
	}

	/**
	 * Show the panel matching the given tab id, hide the rest.
	 *
	 * @param {string} hashValue The tab id.
	 */
	function showPanel(hashValue) {
		$(".ats-integrations-form .heatbox-admin-panel").css("display", "none");
		$(".ats-integrations-form .ats-" + hashValue + "-panel").css(
			"display",
			"block"
		);
	}

	return {};
})(jQuery);
