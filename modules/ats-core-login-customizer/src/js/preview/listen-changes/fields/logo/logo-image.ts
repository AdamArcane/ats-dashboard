import {
	LogoLinkOpts,
	atsLoginCustomizerInterface,
} from "../../../../interfaces";

declare var wp: any;
declare var atsLoginCustomizer: atsLoginCustomizerInterface;

const listenLogoImageFieldChange = (opts: LogoLinkOpts) => {
	const logoLink = opts.logoLink;

	wp.customize("ats_login[logo_image]", function (setting: any) {
		setting.bind(function (val: string) {
			if (val) {
				logoLink.style.backgroundImage = "url(" + val + ")";
			} else {
				logoLink.style.backgroundImage =
					"url(" + atsLoginCustomizer.wpLogoUrl + ")";
			}
		});
	});
};

export default listenLogoImageFieldChange;
