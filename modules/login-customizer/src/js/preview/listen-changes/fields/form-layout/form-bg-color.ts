import writeStyles from "../../../css-utilities/write-styles";

declare var wp: any;

const listenFormBgColorFieldChange = () => {
	wp.customize("ats_login[form_bg_color]", function (setting) {
		const formBgColorStyleTag = document.querySelector(
			'[data-listen-value="ats_login[form_bg_color]"]'
		) as HTMLStyleElement;

		setting.bind(function (val) {
			var formPosition = wp.customize("ats_login[form_position]").get();

			val = val ? val : "#ffffff";
			formPosition = formPosition ? formPosition : "default";

			// The "default" value is handled in the pro version.
			if (formPosition !== "default") {
				writeStyles({
					styleEl: formBgColorStyleTag,
					styles: [
						{
							cssSelector: "#login",
							cssRules: "background-color: " + val + ";",
						},
						{
							cssSelector: ".login form, #loginform",
							cssRules: "background-color: transparent;",
						},
					],
				});
			}
		});
	});
};

export default listenFormBgColorFieldChange;
