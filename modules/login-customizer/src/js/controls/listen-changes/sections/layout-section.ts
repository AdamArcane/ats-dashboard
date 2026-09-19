declare var wp: any;

const listenLayoutSectionState = () => {
	wp.customize.section(
		"ats_login_customizer_layout_section",
		function (section: any) {
			const syncShadowControls = () => {
				const controlsEnabled = !!wp
					.customize("ats_login[enable_form_shadow]")
					.get();
				const blurControl = wp.customize.control("ats_login[form_shadow_blur]");
				const colorControl = wp.customize.control("ats_login[form_shadow_color]");
				blurControl.container.toggle(controlsEnabled);
				colorControl.container.toggle(controlsEnabled);

				if (controlsEnabled) {
					blurControl.activate();
					colorControl.activate();
				} else {
					blurControl.deactivate();
					colorControl.deactivate();
				}
			};

			section.expanded.bind(function (isExpanded: boolean | number) {
				if (isExpanded) {
					syncShadowControls();

					// The rest of "default" is handled in the free version.
					if (wp.customize("ats_login[form_position]").get() === "default") {
						wp.customize.control("ats_login[box_width]").deactivate();
					} else {
						wp.customize.control("ats_login[box_width]").activate();

						wp.customize
							.control("ats_login[form_horizontal_padding]")
							.deactivate();

						wp.customize.control("ats_login[form_border_width]").deactivate();
						wp.customize.control("ats_login[form_border_style]").deactivate();
						wp.customize.control("ats_login[form_border_color]").deactivate();

						wp.customize.control("ats_login[form_border_radius]").deactivate();

						wp.customize.control("ats_login[enable_form_shadow]").deactivate();

						wp.customize.control("ats_login[form_shadow_blur]").deactivate();
						wp.customize.control("ats_login[form_shadow_color]").deactivate();
					}
				}
			});
		}
	);
};

export default listenLayoutSectionState;
