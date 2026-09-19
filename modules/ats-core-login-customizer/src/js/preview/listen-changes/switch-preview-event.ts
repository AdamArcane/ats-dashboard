import { atsLoginCustomizerInterface } from "../../interfaces";

declare var wp: any;
declare var atsLoginCustomizer: atsLoginCustomizerInterface;

const listenPreviewSwitchEvent = () => {
	wp.customize.preview.bind(
		"ats-login-customizer-goto-login-page",
		function (data: any) {
			// When the section is expanded, open the login customizer page.
			if (data.expanded) {
				wp.customize.preview.send("url", atsLoginCustomizer.loginPageUrl);
			}
		}
	);

	wp.customize.preview.bind(
		"ats-login-customizer-goto-home-page",
		function (data: any) {
			wp.customize.preview.send("url", data.url);
		}
	);
};

export default listenPreviewSwitchEvent;
