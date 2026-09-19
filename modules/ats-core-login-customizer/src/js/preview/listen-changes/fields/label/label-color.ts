declare var wp: any;

const listenLabelColorFieldChange = () => {
	wp.customize("ats_login[labels_color]", function (setting: any) {
		setting.bind(function (val: string) {
			val = val ? val : "#444444";

			document.querySelector(
				'[data-listen-value="ats_login[labels_color]"]'
			).innerHTML = "#loginform label {color: " + val + ";}";
		});
	});
};

export default listenLabelColorFieldChange;
