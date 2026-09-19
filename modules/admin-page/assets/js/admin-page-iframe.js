(function () {
	var iframe = document.getElementById('ats-admin-page-iframe');
	if (!iframe) return;

	// After the page builder's last retry (4s), force-clear the loading guard
	// so short pages don't keep a permanent min-height gap.
	setTimeout(function () {
		iframe.style.minHeight = '0';
	}, 4500);

	// Listen postMessage event from the page inside iframe.
	window.addEventListener('message', function (event) {
		if (!event.data || !event.data.height) return;
		iframe.style.height = event.data.height + 'px';

		// Clear the loading guard early once the content is tall enough.
		if (event.data.height >= 500) {
			iframe.style.minHeight = '0';
		}
	}, false);
}());