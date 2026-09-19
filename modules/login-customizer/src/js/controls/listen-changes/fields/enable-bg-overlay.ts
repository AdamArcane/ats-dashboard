declare var wp: any;

const listenEnableBgOverlayFieldChange = () => {
	wp.customize("ats_login[enable_bg_overlay_color]", function (setting: any) {
		setting.bind(function (val: boolean | number) {
			if (val) {
				wp.customize.control("ats_login[bg_overlay_color]").activate();
			} else {
				wp.customize.control("ats_login[bg_overlay_color]").deactivate();
			}
		});
	});
};

export default listenEnableBgOverlayFieldChange;
