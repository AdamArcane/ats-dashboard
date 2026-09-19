declare var wp: any;

const listenEnableFormShadowFieldChange = () => {
	wp.customize("ats_login[enable_form_shadow]", function (setting: any) {
		setting.bind(function (val: boolean | number) {
			if (val) {
				wp.customize.control("ats_login[form_shadow_blur]").activate();
				wp.customize.control("ats_login[form_shadow_color]").activate();
			} else {
				wp.customize.control("ats_login[form_shadow_blur]").deactivate();
				wp.customize.control("ats_login[form_shadow_color]").deactivate();
			}
		});
	});
};

export default listenEnableFormShadowFieldChange;