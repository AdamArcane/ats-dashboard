declare var wp: any;

const listenEnableFormShadowFieldChange = () => {
	wp.customize("ats_login[enable_form_shadow]", function (setting: any) {
		const syncShadowControls = (val: boolean | number) => {
			const controlsEnabled = !!val;
			const blurControl = wp.customize.control("ats_login[form_shadow_blur]");
			const colorControl = wp.customize.control("ats_login[form_shadow_color]");

			blurControl.container.toggle(controlsEnabled);
			colorControl.container.toggle(controlsEnabled);

			if (val) {
				blurControl.activate();
				colorControl.activate();
			} else {
				blurControl.deactivate();
				colorControl.deactivate();
			}
		};

		setting.bind(syncShadowControls);
		syncShadowControls(setting.get());
	});
};

export default listenEnableFormShadowFieldChange;