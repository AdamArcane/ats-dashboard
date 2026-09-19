import setupPolyfills from "./polyfills";
import { atsLoginCustomizerInterface } from "./interfaces";
import insertProLink from "./controls/helpers/insert-pro-link";

// Import controls setup functions.
import setupLoginTemplateControl from "./controls/setup-controls/login-template-control";
import setupColorControl from "./controls/setup-controls/color-control";
import setupRangeControl from "./controls/setup-controls/range-control";
import setupColorPickerControl from "./controls/setup-controls/color-picker-control";

// Imports panels state listener.
import listenLoginCustomizerPanelState from "./controls/listen-changes/panels/login-customizer-panel";

// Imports sections state listener.
import listenBgSectionState from "./controls/listen-changes/sections/bg-section";
import listenTemplateSectionState from "./controls/listen-changes/sections/template-section";
import listenLayoutSectionState from "./controls/listen-changes/sections/layout-section";

// Imports fields change listener.
import listenEnableFormShadowFieldChange from "./controls/listen-changes/fields/enable-form-shadow";
import listenBgImageFieldChange from "./controls/listen-changes/fields/bg-image";
import listenEnableBgOverlayFieldChange from "./controls/listen-changes/fields/enable-bg-overlay";
import listenBgPositionFieldChange from "./controls/listen-changes/fields/bg-position";
import listenBgSizeFieldChange from "./controls/listen-changes/fields/bg-size";

declare var wp: any;
declare var atsLoginCustomizer: atsLoginCustomizerInterface;

setupPolyfills();

/**
 * Scripts within customizer control panel.
 *
 * Used global objects:
 * - jQuery
 * - wp
 * - atsLoginCustomizer
 */
(function () {
	wp.customize.bind("ready", function () {
		setupControls();
		listen();
		if (!atsLoginCustomizer.isProActive) insertProLink();
	});

	const setupControls = () => {
		setupColorPickerControl();
		setupRangeControl();
		setupColorControl();
		setupLoginTemplateControl();
	};

	const listen = () => {
		listenPanelsState();
		listenSectionsState();
		listenFieldsChange();
	};

	const listenPanelsState = () => {
		listenLoginCustomizerPanelState();
	};

	const listenSectionsState = () => {
		listenLayoutSectionState();

		listenBgSectionState({
			sectionName: "ats_login_customizer_bg_section",
			keyPrefix: "",
		});

		listenBgSectionState({
			sectionName: "ats_login_customizer_layout_section",
			keyPrefix: "form_",
		});

		if (!atsLoginCustomizer.isProActive) {
			listenTemplateSectionState();
		} else {
			document.body.classList.add("ats-pro-active");
		}
	};

	const listenFieldsChange = () => {
		listenBgImageFieldChange({
			keyPrefix: "",
		});

		listenEnableBgOverlayFieldChange();

		listenBgImageFieldChange({
			keyPrefix: "form_",
		});

		listenBgPositionFieldChange({
			keyPrefix: "",
		});

		listenBgPositionFieldChange({
			keyPrefix: "form_",
		});

		listenBgSizeFieldChange({
			keyPrefix: "",
		});

		listenBgSizeFieldChange({
			keyPrefix: "form_",
		});

		listenEnableFormShadowFieldChange();
	};
})();
