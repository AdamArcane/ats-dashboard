import proNotice from "../../helpers/pro-notice";

declare var wp: any;

const listenTemplateFieldChange = () => {
	wp.customize("ats_login[template]", function (setting: any) {
		setting.bind(function (val: any) {
			if (val !== "default") {
				proNotice.show();
			} else {
				proNotice.hide();
			}
		});
	});
};

export default listenTemplateFieldChange;