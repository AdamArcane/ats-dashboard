declare var wp: any;

const listenFormWidthFieldChange = () => {
	wp.customize("ats_login[form_width]", function (setting: any) {
		setting.bind(function (val: string) {
			let formPosition = wp.customize("ats_login[form_position]").get();
			let content = "";

			formPosition = formPosition ? formPosition : "default";

			if (formPosition === "default") {
				content = "#login {width: " + val + ";}";
			} else {
				content = "#loginform {max-width: " + val + ";}";
			}

			document.querySelector(
				'[data-listen-value="ats_login[form_width]"]'
			).innerHTML = content;
		});
	});
};

export default listenFormWidthFieldChange;
