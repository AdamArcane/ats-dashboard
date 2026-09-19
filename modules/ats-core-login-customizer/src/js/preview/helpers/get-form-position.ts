import { atsLoginCustomizerInterface } from "../../interfaces";

declare var wp: any;
declare var atsLoginCustomizer: atsLoginCustomizerInterface;

const getFormPosition = () => {
	let formPosition = wp.customize("ats_login[form_position]").get();
	formPosition = !atsLoginCustomizer.isProActive ? "default" : formPosition;

	return formPosition;
};

export default getFormPosition;
