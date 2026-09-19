import listenFormPositionFieldChange from "./controls/listen-changes/fields/form-position";
import listenEnableFormShadowFieldChange from "./controls/listen-changes/fields/enable-form-shadow";
import listenTemplateFieldChange from "./controls/listen-changes/fields/template";
import listenLayoutSectionState from "./controls/listen-changes/sections/layout-section";
import listenFormPositionToTemplateSync from "./controls/listen-changes/fields/form-position-to-template";
import setupRangeControl from "./controls/setup-controls/range-control";
import setupColorControl from "./controls/setup-controls/color-control";
import setupColorPickerControl from "./controls/setup-controls/color-picker-control";
import setupLoginTemplateControl from "./controls/setup-controls/login-template-control";

declare var wp: any;

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
		listen();
	});

	const listen = () => {
		listenSectionsState();
		listenFieldsChange();
	};

	const listenSectionsState = () => {
		listenLayoutSectionState();
		setupRangeControl();
		setupColorControl();
		setupColorPickerControl();
		setupLoginTemplateControl();
	};

	const listenFieldsChange = () => {
		listenTemplateFieldChange();
		listenFormPositionFieldChange();
		listenEnableFormShadowFieldChange();
		listenFormPositionToTemplateSync();
	};
})();
