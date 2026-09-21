/**
 * This module handles the email notifications settings page.
 *
 * @param {Object} $ jQuery object.
 * @return {Object}
 */
(function ($) {
	// Run the module.
	init();

	/**
	 * Initialize the module, call the main functions.
	 */
	function init() {
		setupColorFields();
		setupTabsNavigation();
		setupLogoUpload();
	}

	/**
	 * Setup the media library picker for the email logo field.
	 */
	function setupLogoUpload() {
		const field = document.querySelector(".ats-email-notifications-logo-upload");
		const preview = document.querySelector(".ats-email-notifications-logo-preview");
		const uploadButton = document.querySelector(".ats-email-notifications-logo-upload-button");
		const clearButton = document.querySelector(".ats-email-notifications-logo-clear");

		if (!field || !uploadButton || typeof wp === "undefined" || !wp.media) return;

		let mediaFrame;

		uploadButton.addEventListener("click", function (e) {
			e.preventDefault();

			if (mediaFrame) {
				mediaFrame.open();
				return;
			}

			mediaFrame = wp.media({
				title: uploadButton.dataset.mediaLibraryTitle,
				button: {
					text: uploadButton.dataset.mediaLibraryTitle,
				},
				library: {
					type: "image",
				},
				multiple: false,
			});

			mediaFrame.on("select", function () {
				const attachment = mediaFrame.state().get("selection").first().toJSON();
				const url = (attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url) || attachment.url;

				field.value = url;

				if (preview) {
					preview.src = url;
					preview.style.display = "";
				}
			});

			mediaFrame.open();
		});

		if (clearButton) {
			clearButton.addEventListener("click", function (e) {
				e.preventDefault();

				field.value = "";

				if (preview) {
					preview.src = "";
					preview.style.display = "none";
				}
			});
		}
	}

	/**
	 * Setup color picker fields.
	 */
	function setupColorFields() {
		const colorFields = document.querySelectorAll(".ats-email-notifications-color-field");
		if (!colorFields.length) return;

		colorFields.forEach(function (el) {
			if (!(el instanceof HTMLInputElement)) return;

			$(el).wpColorPicker({
				defaultColor: el.dataset.default,
				hide: true,
				palettes: true,
			});
		});
	}

	/**
	 * Setup the tabs navigation for the email notifications page.
	 */
	function setupTabsNavigation() {
		$(".ats-email-notifications-page .heatbox-tab-nav-item").on("click", function () {
			$(".ats-email-notifications-page .heatbox-tab-nav-item").removeClass("active");
			$(this).addClass("active");

			const link = this.querySelector("a");
			const hashValue = link?.href.substring(link.href.indexOf("#") + 1) ?? "";

			setRefererValue(hashValue);

			$(".ats-email-notifications-form .heatbox-admin-panel").css("display", "none");
			$(".ats-email-notifications-form .ats-" + hashValue + "-panel").css("display", "block");
		});

		window.addEventListener("load", function () {
			var hashValue = window.location.hash.substr(1);

			if (!hashValue) {
				hashValue = "global";
			}

			setRefererValue(hashValue);

			$(".ats-email-notifications-page .heatbox-tab-nav-item").removeClass("active");
			$(".ats-email-notifications-page .heatbox-tab-nav-item." + hashValue + "-panel").addClass("active");

			$(".ats-email-notifications-form .heatbox-admin-panel").css("display", "none");
			$(".ats-email-notifications-form .ats-" + hashValue + "-panel").css("display", "block");
		});
	}

	/**
	 * Set referer value for the tabs navigation so the active tab survives a save.
	 *
	 * @param {string} hashValue The hash value.
	 */
	function setRefererValue(hashValue) {
		const refererField = document.querySelector(
			'.ats-email-notifications-form [name="_wp_http_referer"]'
		);
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
