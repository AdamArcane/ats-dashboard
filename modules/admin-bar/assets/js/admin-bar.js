/**
 * Used global objects:
 * - jQuery
 * - ajaxurl
 * - atsAdminBar
 * - atsAdminBarBuilder
 */
(function ($) {
	if (window.NodeList && !NodeList.prototype.forEach) {
		NodeList.prototype.forEach = Array.prototype.forEach;
	}

	if (!String.prototype.includes) {
		String.prototype.includes = function (search, start) {
			"use strict";

			if (search instanceof RegExp) {
				throw TypeError("first argument must not be a RegExp");
			}
			if (start === undefined) {
				start = 0;
			}
			return this.indexOf(search, start) !== -1;
		};
	}

	var state = {};
	var elms = {};
	var loading = {};
	// var usersData;

	/**
	 * Init the script.
	 * Call the main functions here.
	 */
	function init() {
		// loadUsers();
		buildMenu(atsAdminBarBuilder.builderItems);

		elms.form = document.querySelector(".ats-menu-builder--edit-form");
		elms.saveButton = elms.form.querySelector(
			".ats-menu-builder--submit-button"
		);

		document
			.querySelector(".ats-menu-builder--edit-form")
			.addEventListener("submit", submitForm);
		document
			.querySelector(".ats-menu-builder--reset-button")
			.addEventListener("click", resetMenu);

		$(document).on("click", ".ats-menu-builder--tab-menu-item", switchTab);

		$(document).on(
			"click",
			".ats-menu-builder--menu-actions .expand-menu",
			expandCollapseMenuItem
		);

		$(document).on(
			"click",
			".ats-menu-builder--menu-name",
			expandCollapseMenuItem
		);

		$(document).on(
			"click",
			".ats-menu-builder--menu-actions .hide-menu",
			showHideMenuItem
		);

		$(document).on("click", ".ats-menu-builder--add-new-menu", addNewMenuItem);
		$(document).on(
			"click",
			".ats-menu-builder--add-new-submenu",
			addNewSubmenuItem
		);
		$(document).on(
			"click",
			".ats-menu-builder--remove-menu-item",
			removeMenuItem
		);
	}

	/**
	 * Add new menu item.
	 * @param {Event} e The event object.
	 */
	function addNewMenuItem(e) {
		var randomId = Math.random().toString(36).substr(2, 10);
		var menu = {
			title: "Custom Menu",
			title_encoded: "Custom Menu",
			title_clean: "Custom Menu",
			title_default: "Custom Menu",
			title_default_encoded: "Custom Menu",
			title_default_clean: "Custom Menu",
			id: "menu-custom-" + randomId,
			id_default: "menu-custom-" + randomId,
			parent: "",
			parent_default: "",
			href: "",
			href_default: "/wp-admin/",
			group: false,
			group_default: false,
			meta: [],
			meta_default: [],
			was_added: 1,
			is_hidden: 0,
			icon: "",
			/**
			 * These properties are not being used currently.
			 * But leave it here because in the future, if requested, it would be used for
			 * "hide menu item for specific role(s) / user(s)" functionality (inside dropdowns).
			 */
			// disallowed_roles: [],
			// disallowed_users: []
		};
		var template = replaceMenuPlaceholders(menu);

		$(this.parentNode.querySelector(".ats-menu-builder--menu-list")).append(
			$(template)
		);

		var menuItem = this.parentNode.querySelector(
			'[data-default-id="menu-custom-' + randomId + '"]'
		);

		setupSortable(menuItem.parentNode);
		// setupSelect2Fields(menuItem);
		setupIconChange(menuItem);
		setupTitleChange(menuItem);
		$(menuItem).find(".dashicons-picker").dashiconsPicker();
	}

	/**
	 * Add new submenu item.
	 * @param {Event} e The event object.
	 */
	function addNewSubmenuItem(e) {
		var randomId = Math.random().toString(36).substr(2, 10);
		var submenu = {
			title: "Custom Submenu",
			title_encoded: "Custom Submenu",
			title_clean: "Custom Submenu",
			title_default: "Custom Submenu",
			title_default_encoded: "Custom Submenu",
			title_default_clean: "Custom Submenu",
			id: "submenu-custom-" + randomId,
			id_default: "submenu-custom-" + randomId,
			parent: "",
			parent_default: "",
			href: "",
			href_default: "/wp-admin/",
			group: false,
			group_default: false,
			meta: [],
			meta_default: [],
			was_added: 1,
			is_hidden: 0,
			/**
			 * These properties are not being used currently.
			 * But leave it here because in the future, if requested, it would be used for
			 * "hide menu item for specific role(s) / user(s)" functionality (inside dropdowns).
			 */
			// disallowed_roles: [],
			// disallowed_users: []
		};
		var submenuContainer = this.parentNode.querySelector(
			".ats-menu-builder--menu-list"
		);
		var $closestParent = $(submenuContainer.parentNode).closest(
			".ats-menu-builder--menu-item"
		);
		var parentId = $closestParent.length
			? $closestParent.data("menu-id")
			: $closestParent.data("default-id");
		var parentItem = atsAdminBarBuilder.builderItems[parentId]
			? atsAdminBarBuilder.builderItems[parentId]
			: atsAdminBarBuilder.existingMenu[parentId];

		// If this submenu is added to custom menu / custom submenu (that is added via ats).
		if (!parentItem) {
			parentItem = {
				id: $closestParent.data("menu-id"),
				id_default: $closestParent.data("default-id"),
			};
		}

		submenu.parent = parentId;
		submenu.parent_default = parentId;

		var template = replaceSubmenuPlaceholders({
			menu: parentItem,
			submenu: submenu,
			depth: submenuContainer.dataset.submenuLevel,
		});

		$(submenuContainer).append($(template));

		var submenuItem = this.parentNode.querySelector(
			'[data-menu-id="submenu-custom-' + randomId + '"]'
		);

		setupSortable(submenuItem.parentNode);
		// setupSelect2Fields(submenuItem);
		setupTitleChange(submenuItem);
	}

	/**
	 * Remove menu item.
	 * @param {Event} e The event object.
	 */
	function removeMenuItem(e) {
		var menuItem = this.parentNode.parentNode.parentNode;
		if (!parseInt(menuItem.dataset.added, 10)) return;
		var menuList = menuItem.parentNode;

		menuList.removeChild(menuItem);
	}

	/**
	 * Load users as select2 data.
	 *
	 * This function is not used currently.
	 * But leave it here because in the future, if requested, it would be used for
	 * "hide menu item for specific user(s)" functionality (inside a dropdown).
	 */
	// function loadUsers() {
	// 	$.ajax({
	// 		type: 'get',
	// 		url: ajaxurl,
	// 		cache: false,
	// 		data: {
	// 			action: 'ats_admin_bar_get_users',
	// 			nonce: atsAdminBar.nonces.getUsers
	// 		}
	// 	}).done(function (r) {
	// 		if (!r.success) return;

	// 		usersData = r.data;

	// 		buildMenu(atsAdminBarBuilder.builderItems);
	// 	}).fail(function () {
	// 		console.log('Failed to load users');
	// 	}).always(function () {
	// 		//
	// 	});
	// }

	/**
	 * Switch tabs.
	 */
	function switchTab(e) {
		if (e.target.classList.contains("delete-icon")) return;
		var tabArea = this.parentNode.parentNode;
		var tabId = this.dataset.atsTabContent;

		var tabHasIdByDefault = false;

		if (tabArea.id) {
			tabHasIdByDefault = true;
		} else {
			tabArea.id =
				"ats-menu-builder--tab" + Math.random().toString(36).substring(7);
		}

		var menus = document.querySelectorAll(
			"#" +
				tabArea.id +
				" > .ats-menu-builder--tab-menu > .ats-menu-builder--tab-menu-item"
		);
		var contents = document.querySelectorAll(
			"#" +
				tabArea.id +
				" > .ats-menu-builder--tab-content > .ats-menu-builder--tab-content-item"
		);

		if (!tabHasIdByDefault) tabArea.removeAttribute("id");

		menus.forEach(function (menu) {
			if (menu.dataset.atsTabContent !== tabId) {
				menu.classList.remove("is-active");
			} else {
				menu.classList.add("is-active");
			}
		});

		contents.forEach(function (content) {
			if (content.id !== tabId) {
				content.classList.remove("is-active");
			} else {
				content.classList.add("is-active");
			}
		});
	}

	/**
	 * Build menu list.
	 *
	 * @param {array} menuList List of menu object.
	 */
	function buildMenu(menuList) {
		var editArea = document.querySelector("#ats-menu-builder--workspace");
		if (!editArea) return;
		var listArea = editArea.querySelector(".ats-menu-builder--menu-list");
		var builtMenu = "";

		for (var menu in menuList) {
			if (menuList.hasOwnProperty(menu)) {
				builtMenu += replaceMenuPlaceholders(menuList[menu]);
			}
		}

		listArea.innerHTML = builtMenu;

		setupMenuItems(listArea);

		var submenuList = listArea.querySelectorAll(
			".ats-menu-builder--submenu-list"
		);

		if (submenuList.length) {
			submenuList.forEach(function (submenu) {
				setupMenuItems(submenu, true);
			});
		}

		// setupSelect2Fields(listArea);
	}

	/**
	 * Setup select2 fields.
	 *
	 * This function is not used currently.
	 * But leave it here because in the future, if requested, it would be used for
	 * "hide menu item for specific role(s) / user(s)" functionality (inside dropdowns).
	 *
	 * @param {HTMLElement} area The setup area.
	 */
	// function setupSelect2Fields(area) {
	// 	var select2Fields = area.querySelectorAll('.ats-menu-builder--select2-field');

	// 	select2Fields.forEach(function (selectbox) {
	// 		if (selectbox.dataset.name !== 'disallowed_roles' && selectbox.dataset.name !== 'disallowed_users') return;

	// 		var select2Data = [];
	// 		var disallowedRoles = [];
	// 		var disallowedUsers = [];

	// 		if ('disallowed_roles' === selectbox.dataset.name) {
	// 			disallowedRoles = selectbox.dataset.disallowedRoles.split(', ');

	// 			atsAdminBar.roles.forEach(function (role) {
	// 				if (disallowedRoles.indexOf(role.id) > -1) {
	// 					role.selected = true;
	// 				}

	// 				select2Data.push(role);
	// 			});
	// 		} else if ('disallowed_users' === selectbox.dataset.name) {
	// 			disallowedUsers = selectbox.dataset.disallowedUsers.split(', ');
	// 			disallowedUsers = disallowedUsers.map(function (user) {
	// 				return parseInt(user, 10);
	// 			});

	// 			usersData.forEach(function (userData) {
	// 				if (disallowedUsers.indexOf(userData.id) > -1) {
	// 					userData.selected = true;
	// 				}

	// 				select2Data.push(userData);
	// 			});
	// 		}

	// 		$(selectbox).select2({
	// 			data: select2Data
	// 		});
	// 	});
	// }

	/**
	 * Replace menu placeholders.
	 *
	 * @param {Object} menu The menu item.
	 */
	function replaceMenuPlaceholders(menu) {
		var template;
		var submenuTemplate;
		var icon;

		template = atsAdminBar.templates.menuList;
		template = template.replace(/{menu_title}/g, menu.title);
		template = template.replace(
			/{encoded_default_menu_title}/g,
			menu.title_default_encoded
		);

		if (menu.group) {
			template = template.replace(
				/{empty_menu_settings_text}/g,
				"No settings available."
			);
			template = template.replace(/{menu_title_field_is_hidden}/g, "is-hidden");
			template = template.replace(/{menu_href_field_is_hidden}/g, "is-hidden");
		} else {
			if ("wp-logo" === menu.id_default) {
				template = template.replace(
					/{menu_title_field_is_hidden}/g,
					"is-hidden"
				);
			} else {
				template = template.replace(/{menu_title_field_is_hidden}/g, "");
			}

			template = template.replace(/{empty_menu_settings_text}/g, "");

			if ("customize" === menu.id_default || "edit" === menu.id_default) {
				template = template.replace(
					/{menu_href_field_is_hidden}/g,
					"is-hidden"
				);
			} else {
				template = template.replace(/{menu_href_field_is_hidden}/g, "");
			}
		}

		var parsedTitle;

		if (
			"menu-toggle" === menu.id_default ||
			"wp-logo" === menu.id_default ||
			"updates" === menu.id_default ||
			"edit" === menu.id_default ||
			"appearance" === menu.id_default ||
			"comments" === menu.id_default ||
			"search" === menu.id_default ||
			false === menu.title_default
		) {
			template = template.replace(/{menu_title_is_disabled}/g, "disabled");

			if ("wp-logo" === menu.id_default) {
				parsedTitle = "WP Logo";
			} else if ("comments" === menu.id_default) {
				parsedTitle = "Comments";
			} else if ("search" === menu.id_default) {
				parsedTitle = "Search Form";
			} else {
				parsedTitle = menu.id ? menu.id : menu.id_default;
			}
		} else {
			template = template.replace(/{menu_title_is_disabled}/g, "");

			if ("updates" === menu.id_default) {
				parsedTitle = menu.meta.title ? menu.meta.title : menu.id_default;
			} else {
				parsedTitle = menu.title ? menu.title_clean : menu.title_default_clean;
			}
		}

		template = template.replace(/{parsed_menu_title}/g, parsedTitle);

		template = template.replace(/{menu_href}/g, menu.href);
		template = template.replace(/{default_menu_href}/g, menu.href_default);

		template = template.replace(
			/{default_menu_group}/g,
			menu.group_default ? menu.group_default : "false"
		);

		if (
			false === menu.href_default ||
			"my-sites" === menu.id_default ||
			"site-name" === menu.id_default ||
			"site-name-frontend" === menu.id_default ||
			"new-content" === menu.id_default ||
			"comments" === menu.id_default ||
			"updates" === menu.id_default
		) {
			template = template.replace(/{menu_href_is_disabled}/g, "disabled");
		} else {
			template = template.replace(/{menu_href_is_disabled}/g, "");
		}

		template = template.replace(/{menu_id}/g, menu.id);
		template = template.replace(/{default_menu_id}/g, menu.id_default);

		template = template.replace(/{default_menu_parent}/g, menu.parent_default);

		template = template.replace(
			/{menu_icon_is_disabled}/g,
			menu.was_added ? "" : "disabled"
		);

		template = template.replace(/{menu_was_added}/g, menu.was_added.toString());
		template = template.replace(/{menu_is_hidden}/g, menu.is_hidden.toString());
		template = template.replace(
			/{frontend_only_indicator}/g,
			menu.frontend_only
				? '<span class="ats-menu-builder--tag ats-menu-builder--frontend-only-tag">Frontend</span>'
				: ""
		);
		template = template.replace(
			/{group_indicator}/g,
			menu.group
				? '<span class="ats-menu-builder--tag ats-menu-builder--group-tag">Group</span>'
				: ""
		);
		template = template.replace(
			/{trash_icon}/g,
			menu.was_added
				? '<span class="dashicons dashicons-trash ats-menu-builder--remove-menu-item"></span>'
				: ""
		);
		template = template.replace(
			/{hidden_icon}/g,
			menu.is_hidden ? "hidden" : "visibility"
		);

		/**
		 * These codes are not being used currently.
		 * But leave it here because in the future, if requested, it would be used for
		 * "hide menu item for specific role(s) / user(s)" functionality (inside dropdowns).
		 */
		// var disallowedRoles = menu.disallowed_roles.join(', ');
		// var disallowedUsers = menu.disallowed_users.join(', ');

		// template = template.replace(/{disallowed_roles}/g, disallowedRoles);
		// template = template.replace(/{disallowed_users}/g, disallowedUsers);

		if (menu.was_added) {
			template = template.replace(/{menu_icon_field_is_hidden}/g, "");
			template = template.replace(/{menu_icon}/g, menu.icon);

			if (menu.icon) {
				icon = '<i class="dashicons ' + menu.icon + '"></i>';
				template = template.replace(/{render_menu_icon}/g, icon);
			} else {
				template = template.replace(/{render_menu_icon}/g, "");
			}
		} else {
			template = template.replace(/{menu_icon_field_is_hidden}/g, "is-hidden");
			template = template.replace(/{menu_icon}/g, "");

			if ("wp-logo" === menu.id_default) {
				template = template.replace(
					/{render_menu_icon}/g,
					'<i class="dashicons dashicons-wordpress"></i>'
				);
			} else if ("my-sites" === menu.id_default) {
				template = template.replace(
					/{render_menu_icon}/g,
					'<i class="dashicons dashicons-admin-multisite"></i>'
				);
			} else if ("site-name" === menu.id_default) {
				template = template.replace(
					/{render_menu_icon}/g,
					'<i class="dashicons dashicons-admin-home"></i>'
				);
			} else if ("site-name-frontend" === menu.id_default) {
				template = template.replace(
					/{render_menu_icon}/g,
					'<i class="dashicons dashicons-dashboard"></i>'
				);
			} else if ("customize" === menu.id_default) {
				template = template.replace(
					/{render_menu_icon}/g,
					'<i class="dashicons dashicons-admin-customizer"></i>'
				);
			} else if ("updates" === menu.id_default) {
				template = template.replace(
					/{render_menu_icon}/g,
					'<i class="dashicons dashicons-update"></i>'
				);
			} else if ("comments" === menu.id_default) {
				template = template.replace(
					/{render_menu_icon}/g,
					'<i class="dashicons dashicons-admin-comments"></i>'
				);
			} else if ("new-content" === menu.id_default) {
				template = template.replace(
					/{render_menu_icon}/g,
					'<i class="dashicons dashicons-plus"></i>'
				);
			} else if ("edit" === menu.id_default) {
				template = template.replace(
					/{render_menu_icon}/g,
					'<i class="dashicons dashicons-edit"></i>'
				);
			} else {
				template = template.replace(/{render_menu_icon}/g, "");
			}
		}

		if (menu.submenu && Object.keys(menu.submenu).length) {
			submenuTemplate = buildSubmenu({
				menu: menu,
				depth: 1,
			});

			template = template.replace(/{submenu_template}/g, submenuTemplate);
		} else {
			template = template.replace(/{submenu_template}/g, "");
		}

		return template;
	}

	/**
	 * Build submenu list.
	 *
	 * @param {Object} param The submenu parameter containing some arguments.
	 *
	 * @param {array} param.menu The menu item which contains the submenu list.
	 * @param {int} param.depth The submenu depth level.
	 *
	 * @return {string} template The submenu template.
	 */
	function buildSubmenu(param) {
		var menu = param.menu;
		var depth = param.depth;

		var template = "";

		for (var submenuId in menu.submenu) {
			if (menu.submenu.hasOwnProperty(submenuId)) {
				template += replaceSubmenuPlaceholders({
					menu: menu,
					// Current submenu item.
					submenu: menu.submenu[submenuId],
					depth: depth,
				});
			}
		}

		return template;
	}

	/**
	 * Replace submenu placeholders.
	 *
	 * @param {Object} param The parameter containing some arguments.
	 *
	 * @param {Object} param.menu The menu item which contains the submenu list.
	 * @param {Object} param.submenu The current submenu item.
	 * @param {int} param.depth The submenu depth level.
	 */
	function replaceSubmenuPlaceholders(param) {
		var menu = param.menu;
		var submenu = param.submenu;
		var depth = param.depth;

		var template = atsAdminBar.templates.submenuList;

		template = template.replace(/{default_menu_id}/g, menu.id_default);

		template = template.replace(/{submenu_id}/g, submenu.id);
		template = template.replace(/{default_submenu_id}/g, submenu.id_default);

		template = template.replace(
			/{default_submenu_parent}/g,
			submenu.parent_default
		);

		template = template.replace(/{submenu_level}/g, depth.toString());
		template = template.replace(
			/{submenu_next_level}/g,
			(depth + 1).toString()
		);
		template = template.replace(/{submenu_title}/g, submenu.title);
		template = template.replace(
			/{encoded_default_submenu_title}/g,
			submenu.title_default_encoded
		);

		if (submenu.group || submenu.id_default === "search") {
			template = template.replace(
				/{empty_submenu_settings_text}/g,
				"No settings available."
			);
			template = template.replace(
				/{submenu_title_field_is_hidden}/g,
				"is-hidden"
			);
			template = template.replace(
				/{submenu_href_field_is_hidden}/g,
				"is-hidden"
			);
		} else {
			template = template.replace(/{empty_submenu_settings_text}/g, "");
			template = template.replace(/{submenu_title_field_is_hidden}/g, "");
			template = template.replace(/{submenu_href_field_is_hidden}/g, "");
		}

		var parsedTitle;

		if (
			"wp-logo" === submenu.id_default ||
			"appearance" === submenu.id_default ||
			"comments" === submenu.id_default ||
			"search" === submenu.id_default ||
			"user-info" === submenu.id_default ||
			false === submenu.title_default
		) {
			template = template.replace(/{submenu_title_is_disabled}/g, "disabled");
			parsedTitle = submenu.id ? submenu.id : submenu.id_default;
		} else {
			if ("my-account" === submenu.id_default) {
				template = template.replace(/{submenu_title_is_disabled}/g, "disabled");
			} else {
				template = template.replace(/{submenu_title_is_disabled}/g, "");
			}

			if ("updates" === menu.id_default) {
				parsedTitle = menu.meta.title ? menu.meta.title : menu.id_default;
				parsedTitle = submenu.meta.title
					? submenu.meta.title
					: submenu.id_default;
			} else {
				parsedTitle = submenu.title
					? submenu.title_clean
					: submenu.title_default_clean;
			}
		}

		template = template.replace(/{parsed_submenu_title}/g, parsedTitle);

		if ("logout" === submenu.id_default) {
			template = template.replace(/{submenu_href_is_disabled}/g, "disabled");
		} else {
			if (!submenu.was_added) {
				if (
					"my-sites-super-admin" === submenu.parent_default ||
					"my-sites-list" === submenu.parent_default ||
					"network-admin" === submenu.parent_default ||
					"blog-1" === submenu.parent_default ||
					"site-name" === submenu.parent_default ||
					"site-name-frontend" === submenu.parent_default ||
					"appearance" === submenu.parent_default ||
					"new-content" === submenu.parent_default
				) {
					template = template.replace(
						/{submenu_href_is_disabled}/g,
						"disabled"
					);
				} else {
					template = template.replace(/{submenu_href_is_disabled}/g, "");
				}
			} else {
				template = template.replace(/{submenu_href_is_disabled}/g, "");
			}
		}

		template = template.replace(/{submenu_href}/g, submenu.href);
		template = template.replace(
			/{default_submenu_href}/g,
			submenu.href_default
		);

		template = template.replace(
			/{default_submenu_group}/g,
			submenu.group_default ? submenu.group_default : "false"
		);

		template = template.replace(
			/{submenu_tab_is_hidden}/g,
			3 === depth ? "is-hidden" : ""
		);
		template = template.replace(
			/{submenu_was_added}/g,
			submenu.was_added.toString()
		);
		template = template.replace(
			/{submenu_is_hidden}/g,
			submenu.is_hidden.toString()
		);
		template = template.replace(
			/{frontend_only_indicator}/g,
			submenu.frontend_only
				? '<span class="ats-menu-builder--tag ats-menu-builder--frontend-only-tag">Frontend</span>'
				: ""
		);
		template = template.replace(
			/{group_indicator}/g,
			submenu.group
				? '<span class="ats-menu-builder--tag ats-menu-builder--group-tag">Group</span>'
				: ""
		);
		template = template.replace(
			/{trash_icon}/g,
			submenu.was_added
				? '<span class="dashicons dashicons-trash ats-menu-builder--remove-menu-item"></span>'
				: ""
		);
		template = template.replace(
			/{hidden_icon}/g,
			submenu.is_hidden ? "hidden" : "visibility"
		);

		/**
		 * These codes are not being used currently.
		 * But leave it here because in the future, if requested, it would be used for
		 * "hide menu item for specific role(s) / user(s)" functionality (inside dropdowns).
		 */
		// var disallowedRoles = submenu.disallowed_roles.join(', ');
		// var disallowedUsers = submenu.disallowed_users.join(', ');

		// template = template.replace(/{disallowed_roles}/g, disallowedRoles);
		// template = template.replace(/{disallowed_users}/g, disallowedUsers);

		if (submenu.submenu && Object.keys(submenu.submenu).length) {
			submenuTemplate = buildSubmenu({
				menu: submenu,
				depth: depth + 1,
			});
			template = template.replace(/{submenu_template}/g, submenuTemplate);
		} else {
			template = template.replace(/{submenu_template}/g, "");
		}

		return template;
	}

	/**
	 * Setup menu items.
	 */
	function setupMenuItems(listArea, isSubmenu) {
		setupSortable(listArea);

		var menuItems;

		if (!isSubmenu) {
			menuItems = listArea.querySelectorAll(".ats-menu-builder--menu-item");
			if (!menuItems.length) return;

			menuItems.forEach(function (menuItem) {
				setupIconChange(menuItem);
				setupTitleChange(menuItem);
			});

			$(listArea).find(".dashicons-picker").dashiconsPicker();
		}
	}

	/**
	 * Sortable setup for both active & available widgets.
	 */
	function setupSortable(listArea) {
		$(listArea).sortable({
			receive: function (e, ui) {
				//
			},
			update: function (e, ui) {
				//
			},
		});
	}

	/**
	 * Expand / collapse menu item.
	 * @param {Event} e The event object.
	 */
	function expandCollapseMenuItem(e) {
		var parent = this.classList.contains("expand-menu")
			? this.parentNode.parentNode.parentNode
			: this.parentNode.parentNode;

		var target = parent.querySelector(".ats-menu-builder--expanded-panel");

		if (parent.classList.contains("is-expanded")) {
			$(target)
				.stop()
				.slideUp(350, function () {
					parent.classList.remove("is-expanded");
				});
		} else {
			$(target)
				.stop()
				.slideDown(350, function () {
					parent.classList.add("is-expanded");
				});
		}
	}

	/**
	 * show / hide menu item.
	 *
	 * @param {Event} listArea The event object.
	 */
	function showHideMenuItem(e) {
		var parent = this.parentNode.parentNode.parentNode;
		var isHidden = parent.dataset.hidden === "1" ? true : false;

		if (isHidden) {
			this.classList.add("dashicons-visibility");
			this.classList.remove("dashicons-hidden");
			parent.dataset.hidden = 0;
		} else {
			parent.dataset.hidden = 1;
			this.classList.remove("dashicons-visibility");
			this.classList.add("dashicons-hidden");
		}
	}

	/**
	 * Setup icon field's change.
	 * @param {HTMLElement} menuItem The menu item element.
	 */
	function setupIconChange(menuItem) {
		var iconFields = menuItem.querySelectorAll(".ats-menu-builder--icon-field");
		iconFields = iconFields.length ? iconFields : [];

		iconFields.forEach(function (field) {
			field.addEventListener("change", function () {
				var iconWrapper = menuItem.querySelector(
					".ats-menu-builder--menu-icon"
				);
				var iconOutput = '<i class="dashicons ' + this.value + '"></i>';

				iconWrapper.innerHTML = iconOutput;
			});
		});
	}

	/**
	 * Setup title field's change.
	 * @param {HTMLElement} menuItem The menu item element.
	 */
	function setupTitleChange(menuItem) {
		var fieldPrefix = menuItem.classList.contains(
			"ats-menu-builder--submenu-item"
		)
			? "submenu"
			: "menu";
		var titleFields = menuItem.querySelectorAll(
			'[data-name="' + fieldPrefix + '_title"]'
		);
		titleFields = titleFields.length ? titleFields : [];

		titleFields.forEach(function (field) {
			field.addEventListener("change", function () {
				menuItem.querySelector(".ats-menu-builder--menu-name").innerHTML =
					this.value;
			});
		});
	}

	loading.start = function (button) {
		button.classList.add("is-loading");
	};

	loading.stop = function (button) {
		button.classList.remove("is-loading");
	};

	/**
	 * Collect menu item's data for submission.
	 *
	 * @param {HTMLElement} menuItem The menu item which data is being collected.
	 * @return Object
	 */
	function collectData(menuItem) {
		var fieldPrefix =
			"submenu" === menuItem.parentNode.dataset.menuType ? "submenu" : "menu";
		var menuData = {};

		menuData.was_added = parseInt(menuItem.dataset.added, 10);
		menuData.is_hidden = parseInt(menuItem.dataset.hidden, 10);

		menuData.id = menuItem.dataset.menuId;
		menuData.id_default = menuItem.dataset.defaultId;

		if (!menuData.id) {
			menuData.id = menuItem.dataset.defaultId;
		}

		var titleField = menuItem.querySelector(
			'[data-name="' + fieldPrefix + '_title"]'
		);

		if (titleField) {
			menuData.title = titleField.value;
		}

		var matchedItemObj;

		if (atsAdminBarBuilder.parsedMenu[menuData.id_default]) {
			matchedItemObj = atsAdminBarBuilder.parsedMenu[menuData.id_default];
		} else if (atsAdminBarBuilder.existingMenu[menuData.id_default]) {
			matchedItemObj = atsAdminBarBuilder.existingMenu[menuData.id_default];
		}

		var $closestParent = $(menuItem.parentNode).closest(
			".ats-menu-builder--menu-item"
		);

		menuData.parent = $closestParent.length
			? $closestParent.data("default-id")
			: false;
		menuData.parent_default = menuItem.dataset.defaultParent;

		var hrefField = menuItem.querySelector(
			'[data-name="' + fieldPrefix + '_href"]'
		);

		if (hrefField) {
			menuData.href = hrefField.value;
		}

		menuData.href_default = menuItem.dataset.defaultHref;

		var iconField = menuItem.querySelector(
			'[data-name="' + fieldPrefix + '_icon"]'
		);

		if (iconField) {
			menuData.icon = iconField.value;
		}

		if (matchedItemObj) {
			if (matchedItemObj.frontend_only) {
				menuData.frontend_only = 1;
			}

			menuData.title_default = matchedItemObj.title_default;
		} else {
			menuData.title_default = "";
		}

		if ("site-name-frontend" === menuData.parent) {
			menuData.frontend_only = 1;
			menuData.parent = "site-name";
		}

		if (!menuData.title_default && menuData.was_added) {
			menuData.title_default = menuData.title;
		}

		/**
		 * These codes are not being used currently.
		 * But leave it here because in the future, if requested, it would be used for
		 * "hide menu item for specific role(s) / user(s)" functionality (inside dropdowns).
		 */
		// menuData.disallowed_roles = $('#disallowed_roles_' + menuData.id_default).val();
		// menuData.disallowed_users = $('#disallowed_users_' + menuData.id_default).val();

		// if (menuData.disallowed_users.length) {
		// 	menuData.disallowed_users = menuData.disallowed_users.map(function (user) {
		// 		return parseInt(user, 10);
		// 	});
		// }

		return menuData;
	}

	/**
	 * Function to execute on form submission.
	 *
	 * @param {Event} e The on submit event.
	 */
	function submitForm(e) {
		e.preventDefault();

		var workspace = this.querySelector("#ats-menu-builder--workspace");
		var menuList = {};

		var menuItems = document.querySelectorAll(
			"#" +
				workspace.id +
				" .ats-menu-builder--menu-list .ats-menu-builder--menu-item"
		);
		menuItems = menuItems.length ? menuItems : [];

		menuItems.forEach(function (menuItem) {
			var menuId = menuItem.dataset.menuId
				? menuItem.dataset.menuId
				: menuItem.dataset.defaultId;
			menuList[menuId] = collectData(menuItem);
		});

		saveMenu(menuList);
	}

	/**
	 * Send ajax request to save the menu list.
	 *
	 * @param {Object} menuList The menu list.
	 */
	function saveMenu(menuList) {
		if (state.isSaving) return;
		state.isSaving = true;

		loading.start(elms.saveButton);

		$.ajax({
			url: ajaxurl,
			type: "post",
			dataType: "json",
			data: {
				action: "ats_admin_bar_save_menu",
				nonce: atsAdminBar.nonces.saveMenu,
				menu: JSON.stringify(menuList),
			},
		})
			.done(function (r) {
				location.reload();
			})
			.always(function () {
				loading.stop(elms.saveButton);
				state.isSaving = false;
			});
	}

	/**
	 * Send ajax request to reset the menu list.
	 */
	function resetMenu() {
		var button = this;

		if (state.isSaving) return;

		if (!confirm(atsAdminBar.warningMessages.resetMenu)) return;

		state.isSaving = true;

		loading.start(button);

		$.ajax({
			url: ajaxurl,
			type: "post",
			dataType: "json",
			data: {
				action: "ats_admin_bar_reset_menu",
				nonce: atsAdminBar.nonces.resetMenu,
			},
		})
			.done(function (r) {
				location.reload();
			})
			.always(function () {
				loading.stop(button);
				state.isSaving = false;
			});
	}

	init();
})(jQuery);
