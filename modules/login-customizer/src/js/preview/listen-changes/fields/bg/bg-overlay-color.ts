declare var wp: any;

const listenBgOverlayColorFieldChange = () => {
	wp.customize("ats_login[bg_overlay_color]", function (setting: any) {
		const bgOverlayColorStyleTag = document.querySelector(
			'[data-listen-value="ats_login[bg_overlay_color]"]'
		) as HTMLElement;

		setting.bind(function (val: string) {
			let rule: string;

			if (val) {
				rule = "background-color: " + val + ";";
				bgOverlayColorStyleTag.innerHTML = ".ats-bg-overlay {" + rule + "}";
			} else {
				rule = "background-color: transparent;";
				bgOverlayColorStyleTag.innerHTML = ".ats-bg-overlay {" + rule + "}";
			}
		});
	});
};

export default listenBgOverlayColorFieldChange;
