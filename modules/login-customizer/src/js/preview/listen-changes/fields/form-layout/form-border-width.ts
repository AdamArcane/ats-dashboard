declare var wp: any;

const listenFormBorderWidthFieldChange = () => {
	wp.customize("ats_login[form_border_width]", function (setting: any) {
		setting.bind(function (val: string) {
			const content = val ? "#loginform {border-width: " + val + ";}" : "";

			document.querySelector(
				'[data-listen-value="ats_login[form_border_width]"]'
			).innerHTML = content;
		});
	});
};

export default listenFormBorderWidthFieldChange;
