/**
 * This module is intended to handle the settings page.
 *
 * @param {Object} $ jQuery object.
 * @return {Object}
 */
(function ($) {
	/** @type {CodeMirrorEditor[]} */
	const codeMirrorInstances = [];

	// Run the module.
	init();

	/**
	 * Initialize the module, call the main functions.
	 *
	 * This function is the only function that should be called on top level scope.
	 * Other functions are called / hooked from this function.
	 */
	function init() {
		setupCssFields();
		setupColorFields();
		setupTabsNavigation();
	}

	/**
	 * Setup the CSS fields using CodeMirror.
	 */
	function setupCssFields() {
		const customCSSFields = document.querySelectorAll(".ats-custom-css");
		if (!customCSSFields.length) return;

		/** @type {Record<string, unknown>} */
		const editorSettings = wp.codeEditor.defaultSettings
			? _.clone(wp.codeEditor.defaultSettings)
			: {};

		editorSettings.codemirror = _.extend({}, editorSettings.codemirror, {
			indentUnit: 4,
			tabSize: 4,
			mode: "css",
		});

		[].slice.call(customCSSFields).forEach(function (el) {
			var codeEditor = wp.codeEditor.initialize(el, editorSettings);
			codeMirrorInstances.push(codeEditor.codemirror);
		});

		setTimeout(function () {
			codeMirrorInstances.forEach(function (codeMirror) {
				codeMirror.refresh();

				// Setting up a timeout again to make sure the CodeMirror is refreshed
				setTimeout(function () {
					codeMirror.refresh();
				}, 1000);
			});
		}, 2000); // Yes, 2seconds, you're right. It's necessary.
	}

	/**
	 * Setup color picker fields.
	 */
	function setupColorFields() {
		const colorFields = document.querySelectorAll(".ats-color-field");
		if (!colorFields.length) return;

		colorFields.forEach(function (el) {
			if (!(el instanceof HTMLInputElement)) return;

			$(el).wpColorPicker({
				defaultColor: el.dataset.default,
				change: function (event, ui) {},
				clear: function () {},
				hide: true,
				palettes: true,
			});
		});
	}

	/**
	 * Setup the tabs navigation for settings page.
	 */
	function setupTabsNavigation() {
		// A tab bar can mix #hash-routed panel tabs (shown/hidden on one page)
		// with plain real-link tabs to a separate page (e.g. the "Dashboard
		// Widgets" tab on the Widget Settings page). Only the hash tabs get
		// panel-toggle behavior; real links just navigate normally.
		function isHashLink(link) {
			const href = link ? link.getAttribute("href") : "";
			return !!href && href.charAt(0) === "#";
		}

		$(".atsui-tab-nav-item").on("click", function () {
			const link = this.querySelector("a");

			if (!isHashLink(link)) {
				return;
			}

			$(".atsui-tab-nav-item").removeClass("active");
			$(this).addClass("active");

			const hashValue = link.getAttribute("href").substring(1);

			setRefererValue(hashValue);

			$(".atsui-admin-panel").css("display", "none");
			$(".ats-" + hashValue + "-panel").css("display", "block");
		});

		window.addEventListener("load", function () {
			var firstHashLink = document.querySelector(
				'.atsui-tab-nav-item a[href^="#"]'
			);

			if (!firstHashLink) {
				return;
			}

			var hashValue =
				window.location.hash.substr(1) ||
				firstHashLink.getAttribute("href").substring(1);

			setRefererValue(hashValue);

			$(".atsui-tab-nav-item").removeClass("active");
			$(".atsui-tab-nav-item." + hashValue + "-panel").addClass("active");

			$(".atsui-admin-panel").css("display", "none");
			$(".ats-" + hashValue + "-panel").css("display", "block");
		});
	}

	/**
	 * Set referer value for the tabs navigation of settings page.
	 * This is being used to preserve the active tab after saving the settings page.
	 *
	 * @param {string} hashValue The hash value.
	 */
	function setRefererValue(hashValue) {
		const refererField = document.querySelector('[name="_wp_http_referer"]');
		if (
			!(refererField instanceof HTMLInputElement) &&
			!(refererField instanceof HTMLTextAreaElement)
		) {
			return;
		}

		let url;

		if (refererField.value.includes("#")) {
			url = refererField.value.split("#");
			url = url[0];

			refererField.value = url + "#" + hashValue;
		} else {
			refererField.value = refererField.value + "#" + hashValue;
		}
	}

	return {};
})(jQuery);
