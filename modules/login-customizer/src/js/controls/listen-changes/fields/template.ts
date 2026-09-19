declare var wp: any;

const listenTemplateFieldChange = () => {
	wp.customize("ats_login[template]", function (setting: any) {
		setting.bind(function (val: string) {
			const selected = document.querySelector(
				'[data-control-name="ats_login[template]"] .is-selected img'
			) as HTMLImageElement;

			const bgImage = selected ? selected.dataset.bgImage : "";

			if (bgImage) wp.customize("ats_login[bg_image]").set(bgImage);

			switch (val) {
				case "left":
					wp.customize("ats_login[form_position]").set("left");
					break;

				case "right":
					wp.customize("ats_login[form_position]").set("right");
					break;

				default:
					wp.customize("ats_login[form_position]").set("default");
			}
		});
	});
};

export default listenTemplateFieldChange;
