declare var wp: any;

const listenFormTopPaddingFieldChange = () => {
	wp.customize("ats_login[form_top_padding]", function (setting: any) {
		setting.bind(function (val: string) {
			const content = "#loginform {padding-top: " + val + ";}";

			document.querySelector(
				'[data-listen-value="ats_login[form_top_padding]"]'
			).innerHTML = content;
		});
	});
};

export default listenFormTopPaddingFieldChange;
