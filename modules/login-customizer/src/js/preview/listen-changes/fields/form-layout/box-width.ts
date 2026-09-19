declare var wp: any;

const listenBoxWidthFieldChange = () => {
	wp.customize("ats_login[box_width]", function (setting: any) {
		setting.bind(function (val: string) {
			let formPosition = wp.customize("ats_login[form_position]").get();
			let content = "";

			formPosition = formPosition ? formPosition : "default";

			if (formPosition !== "default") {
				content = "#login {width: " + val + ";}";
			}

			document.querySelector(
				'[data-listen-value="ats_login[box_width]"]'
			).innerHTML = content;
		});
	});
};

export default listenBoxWidthFieldChange;
