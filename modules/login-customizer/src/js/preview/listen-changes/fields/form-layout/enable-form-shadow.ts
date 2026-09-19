import buildBoxShadowCssRule from "../../../css-utilities/build-box-shadow-css-rule";

declare var wp: any;

const listenEnableFormShadowFieldChange = () => {
	wp.customize("ats_login[enable_form_shadow]", function (setting: any) {
		setting.bind(function (val: boolean | number) {
			let shadowBlur: string;
			let shadowColor: string;
			let content: string;

			if (val) {
				shadowBlur = wp.customize("ats_login[form_shadow_blur]").get();
				shadowColor = wp.customize("ats_login[form_shadow_color]").get();
				content = buildBoxShadowCssRule(shadowBlur, shadowColor);
			} else {
				content = "box-shadow: none;";
			}

			content = "#loginform {" + content + "}";
			const styleTag = document.querySelector(
				'[data-listen-value="ats_login[enable_form_shadow]"]'
			) as HTMLStyleElement;
			if (styleTag) styleTag.innerHTML = content;
		});
	});
};

export default listenEnableFormShadowFieldChange;
