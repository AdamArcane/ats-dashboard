declare var wp: any;

const listenLogoHeightFieldChange = () => {
	wp.customize("ats_login[logo_height]", function (setting: any) {
		setting.bind(function (val: string) {
			const el = document.querySelector(
				'[data-listen-value="ats_login[logo_height]"]'
			);

			if (!el) return;

			el.innerHTML =
				".login h1 a, .login .wp-login-logo a {background-size: auto " +
				val +
				";}";
		});
	});
};

export default listenLogoHeightFieldChange;
