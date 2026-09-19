/**
 * This module is intended to handle the loading redirect settings page.
 * 
 * Global object used:
 * - atsLoginRedirect
 *
 * @param {Object} $ jQuery object.
 * @return {Object}
 */
(function ($) {

	var $repeater = $('.ats-login-redirect--repeater');
	var $roleSelector = $('.ats-login-redirect--role-selector');

	// Run the module.
	init();

	/**
	 * Initialize the module, call the main functions.
	 *
	 * This function is the only function that should be called on top level scope.
	 * Other functions are called / hooked from this function.
	 */
	function init() {

		setupTabNav();

	}

	/**
	 * Setup the role selector that functioning like a repeater.
	 */
	function setupTabNav() {

		$(document).on('click', '.ats-login-redirect--tab-menu-item', switchTab);

	}

	/**
	 * Switch tab on tab menu item click.
	 */
	function switchTab() {

		var heatbox = this.parentNode.parentNode.parentNode;
		var tabMenus = heatbox.querySelectorAll('.ats-login-redirect--tab-menu-item');
		var activeTabMenu = this;

		tabMenus.forEach(function (tabMenu) {
			if (tabMenu === activeTabMenu) {
				tabMenu.classList.add('is-active');
			} else {
				tabMenu.classList.remove('is-active');
			}
		});

		var tabContents = heatbox.querySelectorAll('.ats-login-redirect--wrapper');
		var activeTabContent = heatbox.querySelector('.ats-login-redirect--' + this.dataset.atsTab + '-wrapper');

		tabContents.forEach(function (tabContent) {
			if (tabContent === activeTabContent) {
				tabContent.parentNode.parentNode.style.display = 'table-row';
			} else {
				tabContent.parentNode.parentNode.style.display = 'none';
			}
		});

	}

	return {};

})(jQuery);
