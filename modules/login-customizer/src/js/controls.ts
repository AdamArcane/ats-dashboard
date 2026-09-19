import listenFormPositionFieldChange from "./controls/listen-changes/fields/form-position";
import listenTemplateFieldChange from "./controls/listen-changes/fields/template";
import listenLayoutSectionState from "./controls/listen-changes/sections/layout-section";
import listenFormPositionToTemplateSync from "./controls/listen-changes/fields/form-position-to-template";

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
	};

	const listenFieldsChange = () => {
		listenTemplateFieldChange();
		listenFormPositionFieldChange();
		listenFormPositionToTemplateSync();
	};
})();
