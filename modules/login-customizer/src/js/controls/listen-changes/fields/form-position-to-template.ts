declare var wp: any;

/**
 * Sync the template picker when form_position is changed directly.
 *
 * The template picker (ats_login[template]) already syncs form_position
 * when a template is selected. This is the reverse: when form_position
 * is changed from the Login Form section, update the template picker
 * so both controls stay visually in sync.
 */
const listenFormPositionToTemplateSync = () => {
	wp.customize("ats_login[form_position]", function (setting: any) {
		setting.bind(function (val: string) {
			const currentTemplate = wp.customize("ats_login[template]").get();

			// Map form_position values to template keys.
			// Template "left" -> form_position "left"
			// Template "right" -> form_position "right"
			// Template "" (default) -> form_position "default"
			const expectedTemplate = val === "default" ? "" : val;

			// Only update the template if it doesn't already match,
			// to avoid an infinite update loop and to avoid
			// unintentionally re-applying a template's background image.
			if (currentTemplate !== expectedTemplate) {
				wp.customize("ats_login[template]").set(expectedTemplate);
			}
		});
	});
};

export default listenFormPositionToTemplateSync;
