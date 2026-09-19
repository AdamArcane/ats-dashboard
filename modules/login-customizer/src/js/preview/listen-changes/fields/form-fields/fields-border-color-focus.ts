declare var wp: any;

const listenFieldsBorderColorFocusFieldChange = () => {
	wp.customize("ats_login[fields_border_color_focus]", function (setting: any) {
		setting.bind(function (val: string) {
			var content = val
				? ".login input[type=text]:focus, .login input[type=password]:focus {border-color: " +
				  val +
				  ";}"
				: "";

			document.querySelector(
				'[data-listen-value="ats_login[fields_border_color_focus]"]'
			).innerHTML = content;
		});
	});
};

export default listenFieldsBorderColorFocusFieldChange;
