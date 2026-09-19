declare var wp: any;

const listenFormPositionFieldChange = () => {
	wp.customize("ats_login[form_position]", function (setting: any) {
		setting.bind(function (val: string) {
			const formShadowEnabled: boolean | number = wp
				.customize("ats_login[enable_form_shadow]")
				.get();

			if (val === "default") {
				wp.customize.control("ats_login[box_width]").deactivate();
				wp.customize.control("ats_login[form_horizontal_padding]").activate();
				wp.customize.control("ats_login[form_border_width]").activate();
				wp.customize.control("ats_login[form_border_style]").activate();
				wp.customize.control("ats_login[form_border_color]").activate();
				wp.customize.control("ats_login[form_border_radius]").activate();
				wp.customize.control("ats_login[enable_form_shadow]").activate();

				if (formShadowEnabled) {
					wp.customize.control("ats_login[form_shadow_blur]").activate();
					wp.customize.control("ats_login[form_shadow_color]").activate();
				} else {
					wp.customize.control("ats_login[form_shadow_blur]").deactivate();
					wp.customize.control("ats_login[form_shadow_color]").deactivate();
				}
			} else {
				wp.customize.control("ats_login[box_width]").activate();

				wp.customize.control("ats_login[form_horizontal_padding]").deactivate();

				wp.customize.control("ats_login[form_border_width]").deactivate();
				wp.customize.control("ats_login[form_border_style]").deactivate();
				wp.customize.control("ats_login[form_border_color]").deactivate();
				wp.customize.control("ats_login[form_border_radius]").deactivate();
				wp.customize.control("ats_login[enable_form_shadow]").deactivate();
				wp.customize.control("ats_login[form_shadow_blur]").deactivate();
				wp.customize.control("ats_login[form_shadow_color]").deactivate();
			}
		});
	});
};

export default listenFormPositionFieldChange;
