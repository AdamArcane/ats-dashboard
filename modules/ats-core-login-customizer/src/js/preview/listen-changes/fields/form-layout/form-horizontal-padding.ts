declare var wp: any;

const listenFormHorizontalPaddingFieldChange = () => {
	wp.customize("ats_login[form_horizontal_padding]", function (setting: any) {
		setting.bind(function (val: string) {
			var content = val
				? "#loginform {padding-left: " + val + "; padding-right: " + val + ";}"
				: "";

			document.querySelector(
				'[data-listen-value="ats_login[form_horizontal_padding]"]'
			).innerHTML = content;
		});
	});
};

export default listenFormHorizontalPaddingFieldChange;
