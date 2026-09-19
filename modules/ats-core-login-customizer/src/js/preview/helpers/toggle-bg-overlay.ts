const toggleBgOverlay = (show: boolean | number) => {
	const target = document.querySelector(".ats-bg-overlay") as HTMLElement;
	if (!target) return;

	if (show) {
		target.style.display = "block";
	} else {
		target.style.display = "none";
	}
};

export default toggleBgOverlay;
