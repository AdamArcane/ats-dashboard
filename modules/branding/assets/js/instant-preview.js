(function ($) {
	var adminMenuWrap = document.querySelector("#adminmenuwrap");
	var atsuiOverlays = document.querySelectorAll(".atsui-overlay");
	var instantPreviewStyleTags = document.querySelectorAll(
		".ats-instant-preview",
	);

	var brandingCheckbox = document.querySelector(".ats-enable-branding");
	var layoutSelector = document.querySelector('[name="ats_branding[layout]"]');

	var wpAdminDarkmodeCheckbox = document.querySelector(
		"#ats_branding--wp_admin_darkmode",
	);
	var wpAdminDarkModeStyleTag = document.querySelector(
		"style.ats-wp-admin-darkmode-preview",
	);

	var removeWpLogoCheckbox = document.querySelector(".ats-remove-wp-logo");
	var adminBarLogoImageFieldRow = document.querySelector(
		".admin-bar-logo-image-field",
	);
	var adminBarLogoImageSrcField = document.querySelector(
		".ats-branding-upload-image",
	);
	var adminBarLogoLinkUrlField = document.querySelector(
		".ats-admin-bar-logo-url",
	);
	var wpLogo = document.querySelector(".ats-wp-logo");
	var wpLogoLink = document.querySelector(".ats-wp-logo a");

	var modernLogoWrappers = document.querySelectorAll(".ats-admin-logo-wrapper");
	var inheritedModernLogoWrapper = document.querySelector(
		".ats-admin-logo-wrapper.ats-inherited-from-blueprint",
	);
	var modernLogoLinks = document.querySelectorAll(
		".ats-admin-logo-wrapper .ats-admin-logo-link",
	);
	var modernLogos = document.querySelectorAll(
		".ats-admin-logo-wrapper .ats-admin-logo",
	);

	var removeWpIconStyleTag = document.querySelector(
		".ats-style-remove-wp-icon",
	);
	var adminBarLogoImageUrlStyleTag = document.querySelector(
		".ats-style-admin-bar-logo-image-url",
	);
	var removeWpIconSubmenuWrapperStyleTag = document.querySelector(
		".ats-style-remove-wp-icon-submenu-wrapper",
	);

	var inheritedOutputStyleTag = document.querySelector(
		".ats-admin-colors-output.ats-inherited-from-blueprint",
	);
	var defaultOutputStyleTag = document.querySelector(
		".ats-admin-colors-preview.ats-default-admin-colors-output",
	);
	var modernOutputStyleTag = document.querySelector(
		".ats-admin-colors-preview.ats-modern-admin-colors-output",
	);

	function init() {
		setupColorPreview();
		setupColorReset();
		$(".ats-branding-admin-bar-logo-upload").click(function (e) {
			e.preventDefault();
			var button = this;
			var field = button.parentNode.querySelector(".ats-branding-upload-image");
			var mediaLibraryTitle = button.dataset.mediaLibraryTitle;

			var custom_uploader = wp
				.media({
					title: mediaLibraryTitle,
					button: {
						text: "Upload Image",
					},
					// Allow only single file selection.
					multiple: false,
				})
				.on("select", function () {
					var attachment = custom_uploader
						.state()
						.get("selection")
						.first()
						.toJSON();
					field.value = attachment.url;
					field.dispatchEvent(new Event("change"));
				})
				.open();
		});

		$(".ats-branding-clear-upload").click(function (e) {
			e.preventDefault();
			$(this).prev().prev().val("");
		});

		if (wpLogoLink) wpLogoLink.dataset.atsDefaultHref = wpLogoLink.href;

		modernLogoLinks.forEach(function (modernLogoLink) {
			modernLogoLink.dataset.atsDefaultHref = modernLogoLink.href;
		});

		modernLogos.forEach(function (modernLogo) {
			modernLogo.dataset.atsDefaultSrc = modernLogo.src;
		});

		checkBranding();
		toggleDarkModeClass();

		if (brandingCheckbox) brandingCheckbox.addEventListener("change", checkBranding);
		if (layoutSelector) layoutSelector.addEventListener("change", checkLayout);

		if (wpAdminDarkmodeCheckbox) {
			wpAdminDarkmodeCheckbox.addEventListener("change", toggleWpAdminDarkmode);
		}

		if (removeWpLogoCheckbox) removeWpLogoCheckbox.addEventListener("change", checkWpLogo);
		if (adminBarLogoImageSrcField) adminBarLogoImageSrcField.addEventListener("change", checkCustomAdminBarLogoImageSrc);
		if (adminBarLogoLinkUrlField) adminBarLogoLinkUrlField.addEventListener("change", checkCustomAdminBarLogoLinkUrl);
	}

	function setupColorPreview() {
		var fields = document.querySelectorAll( '.ats-branding-color-field' );
		if ( ! fields.length ) return;

		var style = document.getElementById( 'ats-live-branding-colors' );
		if ( ! style ) {
			style = document.createElement( 'style' );
			style.id = 'ats-live-branding-colors';
			document.head.appendChild( style );
		}

		function update() {
			var colors = {};
			fields.forEach(function (field) {
				colors[field.dataset.atsTriggerName] = field.value;
			});

			style.textContent = [
				'#wpadminbar{background-color:' + (colors['admin-bar-bg-color'] || '') + ' !important}',
				'#wpadminbar #wp-admin-bar-wp-logo > .ab-item{background-color:' + (colors['admin-bar-logo-bg-color'] || '') + ' !important}',
				'#adminmenu .ats-admin-logo-wrapper a{background-color:' + (colors['admin-bar-logo-bg-color'] || '') + ' !important}',
				'#adminmenu,#adminmenuback,#adminmenuwrap{background-color:' + (colors['admin-menu-bg-color'] || '') + ' !important}',
				'#adminmenu .wp-submenu,#adminmenu .wp-has-current-submenu .wp-submenu{background-color:' + (colors['admin-submenu-bg-color'] || '') + ' !important}',
				'#adminmenu a,#wpadminbar a{color:' + (colors['menu-item-color'] || '') + ' !important}',
				'#wpadminbar a:hover,#adminmenu a:hover,#adminmenu .current a{color:' + (colors['accent-color'] || '') + ' !important}'
			].join('');
		}

		fields.forEach(function (field) {
			field.addEventListener( 'input', update );
			field.addEventListener( 'change', update );
			$(field).on( 'change irischange', update );
		});
		update();
	}

	function setupColorReset() {
		var resetButton = document.getElementById( 'ats-reset-branding-colors' );
		if ( ! resetButton ) return;

		resetButton.addEventListener( 'click', function () {
			document.querySelectorAll( '.ats-branding-color-field' ).forEach(function (field) {
				var defaultColor = field.dataset.default || '';
				field.value = defaultColor;

				if ( window.jQuery && window.jQuery.fn.wpColorPicker ) {
					window.jQuery( field ).wpColorPicker( 'color', defaultColor );
				}

				field.dispatchEvent( new Event( 'input', { bubbles: true } ) );
				field.dispatchEvent( new Event( 'change', { bubbles: true } ) );
				window.jQuery && window.jQuery( field ).trigger( 'change' );
			});
		});
	}

	function brandingCheckboxChecked() {
		if (!brandingCheckbox || !brandingCheckbox.checked) return false;
		return true;
	}

	function checkBranding() {
		if (!brandingCheckbox) return;
		if (brandingCheckbox.checked) {
			enableBranding();
			checkLayout();
			checkWpLogo();
			checkCustomAdminBarLogoImageSrc();
			checkCustomAdminBarLogoLinkUrl();
		} else {
			disableBranding();
			disableLayout();
			showWpLogo();
			disableCustomAdminBarLogoImageSrc();
			disableCustomAdminBarLogoLinkUrl();
		}
	}

	function enableBranding() {
		if (layoutSelector) layoutSelector.disabled = false;

		instantPreviewStyleTags.forEach(function (tag) {
			tag.type = "text/css";
		});

		atsuiOverlays.forEach(function (overlay) {
			overlay.classList.add("is-hidden");
		});
	}

	function disableBranding() {
		if (layoutSelector) layoutSelector.disabled = true;

		instantPreviewStyleTags.forEach(function (tag) {
			tag.type = "text/ats";
		});

		atsuiOverlays.forEach(function (overlay) {
			overlay.classList.remove("is-hidden");
		});
	}

	function checkLayout() {
		if (inheritedOutputStyleTag) inheritedOutputStyleTag.type = "text/ats";

		if ("modern" === layoutSelector.value) {
			defaultOutputStyleTag.type = "text/ats";
			modernOutputStyleTag.type = "text/css";

			if (removeWpLogoCheckbox.checked) {
				hideModernLogoWrapper();
			} else {
				showModernLogoWrapper();
			}

			if (wpLogo) wpLogo.classList.add("ats-is-hidden");

			if (adminMenuWrap) {
				if (removeWpLogoCheckbox.checked) {
					adminMenuWrap.classList.add("ats-remove-padding");
					adminMenuWrap.classList.remove("ats-use-padding");
				} else {
					adminMenuWrap.classList.remove("ats-remove-padding");
					adminMenuWrap.classList.add("ats-use-padding");
				}
			}
		} else {
			defaultOutputStyleTag.type = "text/css";
			modernOutputStyleTag.type = "text/ats";

			hideModernLogoWrapper();

			if (wpLogo) {
				if (removeWpLogoCheckbox.checked) {
					wpLogo.classList.add("ats-is-hidden");
				} else {
					wpLogo.classList.remove("ats-is-hidden");
				}
			}

			if (adminMenuWrap) {
				adminMenuWrap.classList.remove("ats-remove-padding");
				adminMenuWrap.classList.add("ats-use-padding");
			}
		}

		if (inheritedModernLogoWrapper) {
			inheritedModernLogoWrapper.classList.add("ats-is-hidden");
		}
	}

	function hideModernLogoWrapper() {
		if (!modernLogoWrappers.length) return;

		modernLogoWrappers.forEach(function (modernLogoWrapper) {
			modernLogoWrapper.classList.add("ats-is-hidden");
		});
	}

	function showModernLogoWrapper() {
		if (!modernLogoWrappers.length) return;

		modernLogoWrappers.forEach(function (modernLogoWrapper) {
			modernLogoWrapper.classList.remove("ats-is-hidden");
		});
	}

	function disableLayout() {
		if (inheritedOutputStyleTag) inheritedOutputStyleTag.type = "text/css";
		defaultOutputStyleTag.type = "text/ats";
		modernOutputStyleTag.type = "text/ats";

		hideModernLogoWrapper();

		if (inheritedModernLogoWrapper) {
			inheritedModernLogoWrapper.classList.remove("ats-is-hidden");
		}

		if (wpLogo) wpLogo.classList.remove("ats-is-hidden");
	}

	function toggleDarkModeClass() {
		if (!wpAdminDarkmodeCheckbox || !wpAdminDarkModeStyleTag) return;

		if (wpAdminDarkmodeCheckbox.checked) {
			document.body.classList.add("dark-mode");
		} else {
			document.body.classList.remove("dark-mode");
		}
	}

	function toggleWpAdminDarkmode() {
		if (!wpAdminDarkmodeCheckbox || !wpAdminDarkModeStyleTag) return;

		if (wpAdminDarkmodeCheckbox.checked) {
			wpAdminDarkModeStyleTag.type = "text/css";
		} else {
			wpAdminDarkModeStyleTag.type = "text/ats";
		}

		toggleDarkModeClass();
	}

	function checkWpLogo() {
		if (removeWpLogoCheckbox.checked) {
			hideAdminBarLogoFieldsRow();
			hideWpLogo();
		} else {
			showAdminBarLogoFieldsRow();
			showWpLogo();
		}
	}

	function hideAdminBarLogoFieldsRow() {
		if (adminBarLogoImageFieldRow) {
			adminBarLogoImageFieldRow.classList.add("is-hidden");
		}

		if (adminBarLogoLinkUrlField) {
			adminBarLogoLinkUrlField.parentNode.parentNode.classList.add("is-hidden");
		}
	}

	function showAdminBarLogoFieldsRow() {
		if (adminBarLogoImageFieldRow) {
			adminBarLogoImageFieldRow.classList.remove("is-hidden");
		}

		if (adminBarLogoLinkUrlField) {
			adminBarLogoLinkUrlField.parentNode.parentNode.classList.remove(
				"is-hidden",
			);
		}
	}

	function hideWpLogo() {
		if (wpLogo) wpLogo.classList.add("ats-is-hidden");

		hideModernLogoWrapper();

		if ("modern" === layoutSelector.value) {
			adminMenuWrap.classList.add("ats-remove-padding");
			adminMenuWrap.classList.remove("ats-use-padding");
		} else {
			adminMenuWrap.classList.remove("ats-remove-padding");
			adminMenuWrap.classList.add("ats-use-padding");
		}
	}

	function showWpLogo() {
		if (brandingCheckboxChecked() && "modern" === layoutSelector.value) {
			showModernLogoWrapper();
		} else {
			if (wpLogo) wpLogo.classList.remove("ats-is-hidden");
		}

		adminMenuWrap.classList.remove("ats-remove-padding");
		adminMenuWrap.classList.add("ats-use-padding");
	}

	function checkCustomAdminBarLogoImageSrc() {
		if (!adminBarLogoImageSrcField) return;

		var removeWpIconStyle = removeWpIconStyleTag ? removeWpIconStyleTag.innerHTML : "";
		var removeWpIconSubmenuWrapperStyle = removeWpIconSubmenuWrapperStyleTag ? removeWpIconSubmenuWrapperStyleTag.innerHTML : "";
		var adminBarLogoImageUrlStyle = adminBarLogoImageUrlStyleTag ? adminBarLogoImageUrlStyleTag.innerHTML : "";

		if (
			!adminBarLogoImageSrcField.value ||
			"" === adminBarLogoImageSrcField.value
		) {
			if (removeWpIconStyleTag) removeWpIconStyleTag.innerHTML = buildCssContent(
				removeWpIconStyle,
				"",
			);
			if (removeWpIconSubmenuWrapperStyleTag) removeWpIconSubmenuWrapperStyleTag.innerHTML = buildCssContent(
				removeWpIconSubmenuWrapperStyle,
				"",
			);
			if (adminBarLogoImageUrlStyleTag) adminBarLogoImageUrlStyleTag.innerHTML = buildCssContent(
				adminBarLogoImageUrlStyle,
				"",
			);

			modernLogos.forEach(function (modernLogo) {
				modernLogo.src = modernLogo.dataset.atsDefaultSrc;
			});
		} else {
			if (removeWpIconStyleTag) removeWpIconStyleTag.innerHTML = buildCssContent(
				removeWpIconStyleTag.innerHTML,
				"display: none;",
			);
			if (removeWpIconSubmenuWrapperStyleTag) removeWpIconSubmenuWrapperStyleTag.innerHTML = buildCssContent(
				removeWpIconSubmenuWrapperStyleTag.innerHTML,
				"display: none;",
			);

			if (adminBarLogoImageUrlStyleTag) adminBarLogoImageUrlStyleTag.innerHTML = buildCssContent(
				adminBarLogoImageUrlStyleTag.innerHTML,
				"background-image: url(" + adminBarLogoImageSrcField.value + ");",
			);

			modernLogos.forEach(function (modernLogo) {
				modernLogo.src = adminBarLogoImageSrcField.value;
			});
		}
	}

	function disableCustomAdminBarLogoImageSrc() {
		if (removeWpIconStyleTag) removeWpIconStyleTag.innerHTML = buildCssContent(
			removeWpIconStyleTag.innerHTML,
			"display: inline;",
		);
		if (removeWpIconSubmenuWrapperStyleTag) removeWpIconSubmenuWrapperStyleTag.innerHTML = buildCssContent(
			removeWpIconSubmenuWrapperStyleTag.innerHTML,
			"",
		);
		if (adminBarLogoImageUrlStyleTag) adminBarLogoImageUrlStyleTag.innerHTML = buildCssContent(
			adminBarLogoImageUrlStyleTag.innerHTML,
			"",
		);

		modernLogos.forEach(function (modernLogo) {
			modernLogo.src = modernLogo.dataset.atsDefaultSrc;
		});
	}

	function checkCustomAdminBarLogoLinkUrl() {
		if (
			!adminBarLogoLinkUrlField.value ||
			"" === adminBarLogoLinkUrlField.value
		) {
			disableCustomAdminBarLogoLinkUrl();
		} else {
			if (wpLogoLink) wpLogoLink.href = adminBarLogoLinkUrlField.value;

			modernLogoLinks.forEach(function (modernLogoLink) {
				modernLogoLink.href = adminBarLogoLinkUrlField.value;
			});
		}
	}

	function disableCustomAdminBarLogoLinkUrl() {
		if (wpLogoLink) wpLogoLink.href = wpLogoLink.dataset.atsDefaultHref;

		modernLogoLinks.forEach(function (modernLogoLink) {
			modernLogoLink.href = modernLogoLink.dataset.atsDefaultHref;
		});
	}

	function buildCssContent(content, cssRule) {
		var str = content.split("{");

		return str[0] + "{" + cssRule + "}";
	}

	init();
})(jQuery);
