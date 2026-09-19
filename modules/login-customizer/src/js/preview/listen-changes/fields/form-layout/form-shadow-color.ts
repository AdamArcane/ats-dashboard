import buildBoxShadowCssRule from "../../../css-utilities/build-box-shadow-css-rule";

declare var wp: any;

const listenFormShadowColorFieldChange = () => {
	wp.customize("ats_login[form_shadow_color]", function (setting: any) {
		setting.bind(function (val: string) {
			const shadowBlur = wp.customize("ats_login[form_shadow_blur]").get();
			let content = buildBoxShadowCssRule(shadowBlur, val);
			content = "#loginform {" + content + "}";

			const styleTag = document.querySelector(
				'[data-listen-value="ats_login[enable_form_shadow]"]'
			) as HTMLStyleElement;
			if (styleTag) styleTag.innerHTML = content;
		});
	});
};

export default listenFormShadowColorFieldChange;
