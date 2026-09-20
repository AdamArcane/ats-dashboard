declare var wp: any;

/**
 * Split a css length like "10px" into its number and its unit.
 *
 * A value with no unit keeps the unit already in use, so typing a bare "10"
 * into the text field doesn't turn "10px" into the unitless "10" (which the
 * browser throws away) or into "10%" (which is invalid for most of these
 * properties).
 */
const parseValue = (value: string, currentUnit: string) => {
	const matches = String(value).match(/^\s*(-?\d*\.?\d+)\s*(.*)$/);

	if (!matches) return { number: "", unit: currentUnit };

	const unit = matches[2].trim();

	return { number: matches[1], unit: unit ? unit : currentUnit };
};

const setupRangeControl = () => {
	const controls = document.querySelectorAll(".ats-customize-control-range") as NodeListOf<HTMLElement>;
	if (!controls.length) return;

	[].slice.call(controls).forEach(function (control: HTMLElement) {
		var controlName = control.dataset.controlName;
		var slider = control.querySelector(
			'[data-slider-for="' + controlName + '"]'
		) as HTMLInputElement;

		const textField = control.querySelector(
			".ats-customize-range-field"
		) as HTMLInputElement;

		let unitValue = parseValue(wp.customize(controlName).get() + "", "%").unit;

		wp.customize(controlName, function (setting) {
			setting.bind(function (val) {
				const parsed = parseValue(val + "", unitValue);

				unitValue = parsed.unit;

				// Assigning an unparseable value would jump the slider to its
				// midpoint, so leave the thumb where it is instead.
				if (parsed.number !== "") slider.value = parsed.number;
			});
		});

		slider.addEventListener("input", function (e) {
			const numberValue = this.value;

			wp.customize(controlName).set(numberValue + unitValue);
		});

		// The customizer already saves what was typed on every keystroke. Once
		// editing is done, put the unit back on if it was left off.
		if (textField) {
			textField.addEventListener("change", function (e) {
				const parsed = parseValue(this.value, unitValue);

				if (parsed.number === "") return;

				wp.customize(controlName).set(parsed.number + parsed.unit);
			});
		}

		control
			.querySelector(".ats-customize-control-reset")
			.addEventListener("click", function (e) {
				wp.customize(controlName).set(this.dataset.resetValue);
			});
	});
}

export default setupRangeControl;
