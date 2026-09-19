import {
	LogoLinkOpts,
	atsLoginCustomizerInterface,
} from "../../../../interfaces";

declare var wp: any;
declare var atsLoginCustomizer: atsLoginCustomizerInterface;

const listenLogoUrlFieldChange = (opts: LogoLinkOpts) => {
	const logoLink = opts.logoLink;

	wp.customize("ats_login[logo_url]", function (setting: any) {
		setting.bind(function (val: string) {
			val = val.replace("{home_url}", atsLoginCustomizer.homeUrl);

			logoLink.href = val;
		});
	});
};

export default listenLogoUrlFieldChange;
