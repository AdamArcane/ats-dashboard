declare var wp: any;

const listenFieldsHorizontalPaddingFieldChange = () => {
	wp.customize("ats_login[fields_horizontal_padding]", function (setting: any) {
		setting.bind(function (val: string) {
			var content = val
				? ".login input[type=text], .login input[type=password] {padding: 0 " +
				  val +
				  ";}"
				: "";

			document.querySelector(
				'[data-listen-value="ats_login[fields_horizontal_padding]"]'
			).innerHTML = content;
		});
	});
};

export default listenFieldsHorizontalPaddingFieldChange;
