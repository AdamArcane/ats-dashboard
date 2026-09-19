declare var wp: any;

const listenLangSwitcherToggle = () => {
	wp.customize("ats_login[remove_lang_switcher]", function (setting: any) {
		setting.bind(function (val: boolean | number) {
			const display: string = val ? "none" : "block";

			document.querySelector(
				'[data-listen-value="ats_login[remove_lang_switcher]"]'
			).innerHTML = "#login .language-switcher {display: " + display + ";}";
		});
	});
};

export default listenLangSwitcherToggle;
