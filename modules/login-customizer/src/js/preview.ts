import listenFormLayoutFieldsChange from "./preview/listen-changes/form-layout-fields";
import listenBgFieldsChange from "./preview/listen-changes/bg-fields";
import listenLogoFieldsChange from "./preview/listen-changes/logo-fields";
import listenFormFieldsChange from "./preview/listen-changes/form-fields";
import listenLabelFieldsChange from "./preview/listen-changes/label-fields";
import listenButtonFieldsChange from "./preview/listen-changes/button-fields";
import listenFooterFieldsChange from "./preview/listen-changes/footer-fields";

declare var wp: any;

/**
 * Scripts within customizer preview window.
 *
 * Used global objects:
 * - jQuery
 * - wp
 * - atsLoginCustomizer
 *
 * @param jQuery $ The jQuery object.
 * @param wp.customize api The wp.customize object.
 */
(function () {
	wp.customize.bind("preview-ready", function () {
		listen();
	});

	const listen = () => {
		listenFieldsChange();
	};

	const listenFieldsChange = () => {
		listenBgFieldsChange({ cssSelector: "#login", keyPrefix: "" });
		listenLogoFieldsChange();
		listenFormLayoutFieldsChange();
		listenFormFieldsChange();
		listenLabelFieldsChange();
		listenButtonFieldsChange();
		listenFooterFieldsChange();
	};

	listen();
	window.setTimeout(listen, 250);
})();
