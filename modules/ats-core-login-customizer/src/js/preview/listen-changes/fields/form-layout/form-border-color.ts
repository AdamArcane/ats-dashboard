declare var wp: any;

const listenFormBorderColorFieldChange = () => {
	wp.customize("ats_login[form_border_color]", function (setting: any) {
		setting.bind(function (val: string) {
			val = val ? val : "#dddddd";

			document.querySelector(
				'[data-listen-value="ats_login[form_border_color]"]'
			).innerHTML = "#loginform {border-color: " + val + ";}";
		});
	});
};

export default listenFormBorderColorFieldChange;
