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
		setupOverrideToggles();
	}

	/**
	 * Setup the tabs navigation for the integrations page.
	 */
	function setupTabsNavigation() {
		$(".atsui-tab-nav-item").on("click", function () {
			$(".atsui-tab-nav-item").removeClass("active");
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

			$(".atsui-tab-nav-item").removeClass("active");
			$(".atsui-tab-nav-item." + hashValue + "-panel").addClass("active");

			showPanel(hashValue);
		});
	}

	/**
	 * Show the panel matching the given tab id, hide the rest.
	 *
	 * @param {string} hashValue The tab id.
	 */
	function showPanel(hashValue) {
		$(".ats-integrations-form .atsui-admin-panel").css("display", "none");
		$(".ats-integrations-form .ats-" + hashValue + "-panel").css(
			"display",
			"block"
		);
	}

	/**
	 * Wire up the Label | Auto Value | Override rows: clicking the auto value
	 * or the edit icon reveals the override input; Cancel reverts an
	 * unsaved edit and hides it again.
	 */
	function setupOverrideToggles() {
		$(document).on(
			"click",
			".ats-override-auto-cell, .ats-override-edit-toggle",
			function () {
				var $row = $(this).closest(".ats-override-row");
				openOverrideEditor($row);
			}
		);

		$(document).on("click", ".ats-override-cancel", function () {
			var $row = $(this).closest(".ats-override-row");
			var $input = $row.find(".ats-override-edit input");

			$input.val($input.data("original"));
			closeOverrideEditor($row);
		});
	}

	/**
	 * Show a row's override input, hiding its read-only display.
	 *
	 * @param {Object} $row jQuery-wrapped .ats-override-row element.
	 */
	function openOverrideEditor($row) {
		$row.find(".ats-override-display").hide();
		$row.find(".ats-override-edit").show().find("input").trigger("focus");
	}

	/**
	 * Hide a row's override input and show its read-only display again.
	 *
	 * @param {Object} $row jQuery-wrapped .ats-override-row element.
	 */
	function closeOverrideEditor($row) {
		var $input = $row.find(".ats-override-edit input");
		var hasValue = "" !== String($input.val()).trim();

		$row.toggleClass("has-override", hasValue);
		$row.find(".ats-override-edit").hide();
		$row.find(".ats-override-display").show();
	}

	return {};
})(jQuery);
