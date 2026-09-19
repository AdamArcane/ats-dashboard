declare var wp: any;

const listenFormBorderRadiusFieldChange = () => {
	wp.customize("ats_login[form_border_radius]", function (setting: any) {
		setting.bind(function (val: string) {
			const content = val ? "#loginform {border-radius: " + val + ";}" : "";

			document.querySelector(
				'[data-listen-value="ats_login[form_border_radius]"]'
			).innerHTML = content;
		});
	});
};

export default listenFormBorderRadiusFieldChange;
