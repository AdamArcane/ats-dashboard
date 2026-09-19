import listenFormLayoutFieldsChange from "./preview/listen-changes/form-layout-fields";

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
		listenFormLayoutFieldsChange();
	};
})();
