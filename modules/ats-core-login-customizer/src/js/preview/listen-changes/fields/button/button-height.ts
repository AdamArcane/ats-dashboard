declare var wp: any;

const listenButtonHeightFieldChange = () => {
	wp.customize("ats_login[button_height]", function (setting: any) {
		setting.bind(function (val: string) {
			var content = val
				? ".wp-core-ui .button.button-large {height: " +
				  val +
				  "; line-height: " +
				  val +
				  ";}"
				: "";

			document.querySelector(
				'[data-listen-value="ats_login[button_height]"]'
			).innerHTML = content;
		});
	});
};

export default listenButtonHeightFieldChange;
