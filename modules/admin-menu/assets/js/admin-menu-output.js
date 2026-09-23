(function ($) {
	var customSvgItems = document.querySelectorAll(
		".wp-menu-image.svg"
	);

	if (!customSvgItems.length) return;

	[].slice.call(customSvgItems).forEach(function (el) {
		var styleValue = el.getAttribute("style");
		if (!styleValue) return;

		var matches = styleValue.match(/url\(['"]?([^'"]*)['"]?\)/);
		var newStyleValue = styleValue.replace(
			matches[0],
			matches[0] + " !important"
		);

		el.setAttribute("style", newStyleValue);
	});

})(jQuery);

(function () {
	var newTabLinks = document.querySelectorAll(
		"#adminmenu li.ats-open-new-tab > a"
	);

	if (!newTabLinks.length) return;

	[].slice.call(newTabLinks).forEach(function (link) {
		link.setAttribute("target", "_blank");
		link.setAttribute("rel", "noopener noreferrer");
	});
})();

(function () {
	var adminMenu = document.getElementById("adminmenu");
	if (!adminMenu) return;

	var hiddenShowableItems = adminMenu.querySelectorAll(
		"li.ats-menu-hidden-showable"
	);
	if (!hiddenShowableItems.length) return;

	// Build the toggle as a standalone control (not a real menu item), styled and
	// positioned like core's own "Collapse menu" button: small, muted, with a caret.
	// Core renders #collapse-menu as an <li> inside <ul id="adminmenu">, so ours
	// has to be a real <li> too (a bare <button> there would be invalid markup).
	var toggleItem = document.createElement("li");
	toggleItem.id = "ats-show-hidden-toggle";

	var toggle = document.createElement("button");
	toggle.type = "button";
	toggleItem.appendChild(toggle);

	var toggleLabel = document.createElement("span");
	toggleLabel.className = "ats-show-hidden-toggle-label";
	toggle.appendChild(toggleLabel);

	var toggleCaret = document.createElement("span");
	toggleCaret.className = "ats-show-hidden-toggle-caret";
	toggleCaret.setAttribute("aria-hidden", "true");
	toggle.appendChild(toggleCaret);

	var collapseMenu = document.getElementById("collapse-menu");

	if (collapseMenu && collapseMenu.parentNode) {
		collapseMenu.parentNode.insertBefore(toggleItem, collapseMenu);
	} else {
		adminMenu.appendChild(toggleItem);
	}

	var storageKey = "atsAdminMenuShowHiddenItems";

	function applyState(isShown) {
		adminMenu.classList.toggle("ats-show-hidden-items", isShown);
		toggleItem.classList.toggle("is-active", isShown);
		toggleLabel.textContent = isShown ? "Show Less" : "Show All";
	}

	var stored = false;

	try {
		stored = window.localStorage.getItem(storageKey) === "1";
	} catch (e) {
		stored = false;
	}

	applyState(stored);

	toggle.addEventListener("click", function (e) {
		e.preventDefault();

		var isShown = !adminMenu.classList.contains("ats-show-hidden-items");
		applyState(isShown);

		try {
			window.localStorage.setItem(storageKey, isShown ? "1" : "0");
		} catch (e) {
			//
		}
	});
})();
