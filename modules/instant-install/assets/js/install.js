/**
 * Global variables used:
 * - jQuery
 * - wp
 */
(function ($) {
	var isRequesting = false;

	function init() {
		$(document).on("click", ".ats-update-plugin", updatePlugin);
		$(document).on("click", ".ats-install-plugin", installPlugin);
		$(document).on("click", ".ats-activate-plugin", activatePlugin);
	}

	function updatePlugin() {
		if (isRequesting) return;

		var button = this;
		
		button.innerHTML = atsInstantInstall.texts.updating;
		button.classList.add("is-loading");
		isRequesting = true;

		wp.updates.updatePlugin({
			plugin: atsInstantInstall.pluginPath,
			slug: atsInstantInstall.pluginSlug,
			success: function (r) {
				if (atsInstantInstall.isActivated) {
					// Reload the page.
					window.location.replace(atsInstantInstall.redirectUrl);
				} else {
					// Show activate button.
					button.innerHTML = atsInstantInstall.texts.activate;
					button.classList.remove("ats-install-plugin");
					button.classList.add("ats-activate-plugin");
				}

				button.classList.remove("is-loading");
				isRequesting = false;
			},
			error: function (r) {
				alert(r.errorMessage);

				button.innerHTML = atsInstantInstall.texts.update;
				button.classList.remove("is-loading");
				isRequesting = false;
			},
		});
	}

	function installPlugin() {
		if (isRequesting) return;

		var button = this;
		
		button.innerHTML = atsInstantInstall.texts.installing;
		button.classList.add("is-loading");
		isRequesting = true;

		wp.updates.installPlugin({
			slug: "ats-dashboard",
			success: function () {
				// Show activate button.
				button.innerHTML = atsInstantInstall.texts.activate;
				button.classList.remove("ats-install-plugin");
				button.classList.add("ats-activate-plugin");
			},
			always: function () {
				button.classList.remove("is-loading");
				isRequesting = false;
			},
		});
	}

	function activatePlugin() {
		if (isRequesting) return;

		var button = this;

		button.innerHTML = atsInstantInstall.texts.activating;
		button.classList.add("is-loading");
		isRequesting = true;

		$.ajax({
			async: true,
			type: "GET",
			url: atsInstantInstall.activateUrl,
			success: function () {
				// Reload the page.
				window.location.replace(atsInstantInstall.redirectUrl);
			},
			error: function (jqXHR, exception) {
				var msg = "";
				if (jqXHR.status === 0) {
					msg = "Not connect.\n Verify Network.";
				} else if (jqXHR.status === 404) {
					msg = "Requested page not found. [404]";
				} else if (jqXHR.status === 500) {
					msg = "Internal Server Error [500].";
				} else if (exception === "parsererror") {
					msg = "Requested JSON parse failed.";
				} else if (exception === "timeout") {
					msg = "Time out error.";
				} else if (exception === "abort") {
					msg = "Ajax request aborted.";
				} else {
					msg = "Uncaught Error.\n" + jqXHR.responseText;
				}

				console.log(msg);
			},
			always: function () {
				button.classList.remove("is-loading");
				isRequesting = false;
			},
		});
	}

	init();
})(jQuery);