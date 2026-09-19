declare var wp: any;

const listenBackToSiteToggle = () => {
	wp.customize("ats_login[remove_back_to_site_link]", function (setting: any) {
		setting.bind(function (val: boolean | number) {
			const display: string = val ? "none" : "block";

			document.querySelector(
				'[data-listen-value="ats_login[remove_back_to_site_link]"]'
			).innerHTML = ".login #backtoblog {display: " + display + ";}";
		});
	});
};

export default listenBackToSiteToggle;
