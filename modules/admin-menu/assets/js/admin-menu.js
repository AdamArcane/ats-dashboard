/**
 * Used global objects:
 * - jQuery
 * - ajaxurl
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

	var elms = {};
	var loading = {};
	var state = {};
	var usersData = [];
	var pendingShowForUsersFields = [];

	/**
	 * Init the script.
	 * Call the main functions here.
	 */
	function init() {
		elms.form = document.querySelector(".ats-menu-builder--edit-form");
		elms.saveButton = elms.form.querySelector(
			".ats-menu-builder--submit-button"
		);
		elms.resetButton = elms.form.querySelector(
			".ats-menu-builder--reset-button"
		);

		state.isSaving = false;

		// There's a single (Default) menu list now - load it once.
		getMenu();

		elms.form.addEventListener("submit", submitForm);

		if (elms.resetButton) {
			elms.resetButton.addEventListener("click", resetMenu);
		}

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

		$(document).on("click", ".ats-menu-builder--add-new-menu", addNewMenu);
		$(document).on(
			"click",
			".ats-menu-builder--add-new-separator",
			addNewSeparator
		);
		$(document).on(
			"click",
			".ats-menu-builder--add-new-submenu",
			addNewSubmenu
		);
		$(document).on(
			"click",
			".ats-menu-builder--remove-menu-item",
			removeMenuItem
		);

		loadUsers();
	}

	/**
	 * Switch tabs.
	 *
	 * Generic - used for the per-item "Settings / Submenu" tabs and the
	 * "Dashicons / SVG Code" icon-switcher tabs.
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
	 * Add new menu item.
	 * @param {Event} e The event object.
	 */
	function addNewMenu(e) {
		var workspace = this.parentNode.parentNode;
		var randomId = Math.random().toString(36).substr(2, 10);
		var menu = {
			class: "menu-top menu-icon-custom ats-menu-top ats-menu-icon-custom",
			class_default:
				"menu-top menu-icon-custom ats-menu-top ats-menu-icon-custom",
			dashicon: "dashicons-admin-generic",
			dashicon_default: "dashicons-admin-generic",
			icon_svg: "",
			icon_svg_default: "",
			icon_type: "dashicon",
			icon_type_default: "dashicon",
			id: "menu-custom-" + randomId,
			id_default: "menu-custom-" + randomId,
			is_hidden: "0",
			open_new_tab: "",
			cap: "",
			role_hide_enabled: "0",
			role_hide_mode: "selected",
			role_hide_roles: [],
			show_for_users: [],
			submenu: [],
			title: "Custom Menu",
			title_default: "Custom Menu",
			type: "menu",
			url: "",
			url_default: "/wp-admin/",
			was_added: "1",
		};
		var template = replaceMenuPlaceholders(menu);

		$(workspace.querySelector(".ats-menu-builder--menu-list")).append(
			$(template)
		);

		var menuItem = workspace.querySelector(
			'[data-default-id="menu-custom-' + randomId + '"]'
		);

		setupNewMenuItem(menuItem);

		var submenuList = menuItem.querySelectorAll(
			".ats-menu-builder--submenu-list"
		);

		if (submenuList.length) {
			submenuList.forEach(function (submenu) {
				setupMenuItems(submenu, true);
			});
		}
	}

	/**
	 * Add new separator item.
	 * @param {Event} e The event object.
	 */
	function addNewSeparator(e) {
		var workspace = this.parentNode.parentNode;
		var randomId = Math.random().toString(36).substr(2, 5);
		var menu = {
			class: "wp-menu-separator ats-menu-separator",
			class_default: "wp-menu-separator ats-menu-separator",
			dashicon: "",
			dashicon_default: "",
			icon_svg: "",
			icon_svg_default: "",
			icon_type: "",
			icon_type_default: "dashicon",
			id: "separator-custom-" + randomId,
			id_default: "separator-custom-" + randomId,
			is_hidden: "0",
			submenu: [],
			title: "",
			title_default: "",
			type: "separator",
			url: "",
			url_default: "custom-separator-" + randomId,
			was_added: "1",
		};
		var template = replaceMenuPlaceholders(menu);

		$(workspace.querySelector(".ats-menu-builder--menu-list")).append(
			$(template)
		);

		var menuItem = workspace.querySelector(
			'[data-default-id="separator-custom-' + randomId + '"]'
		);

		setupNewMenuItem(menuItem);
	}

	/**
	 * Add new submenu item.
	 * @param {Event} e The event object.
	 */
	function addNewSubmenu(e) {
		var randomId = Math.random().toString(36).substr(2, 10);
		var submenu = {
			id: "submenu-custom-" + randomId,
			is_hidden: "0",
			open_new_tab: "",
			cap: "",
			role_hide_enabled: "0",
			role_hide_mode: "selected",
			role_hide_roles: [],
			show_for_users: [],
			title: "Custom Submenu",
			title_default: "Custom Submenu",
			url: "",
			url_default: "/wp-admin/",
			was_added: "1",
		};
		var template = replaceSubmenuPlaceholders(submenu);

		$(this.parentNode.querySelector(".ats-menu-builder--submenu-list")).append(
			$(template)
		);

		var submenuItem = this.parentNode.querySelector(
			'[data-submenu-id="submenu-custom-' + randomId + '"]'
		);

		setupNewMenuItem(submenuItem, true);
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
	 * Load users (used to populate the per-item "Always show for the following
	 * users" multiselects).
	 */
	function loadUsers() {
		$.ajax({
			type: "get",
			url: ajaxurl,
			cache: false,
			data: {
				action: "ats_admin_menu_get_users",
				nonce: atsAdminMenu.nonces.getUsers,
			},
		})
			.done(function (r) {
				if (!r.success) return;

				usersData = r.data.filter(function (user) {
					return user.id !== "";
				});

				state.usersLoaded = true;

				pendingShowForUsersFields.forEach(initShowForUsersSelect);
				pendingShowForUsersFields = [];
			})
			.fail(function () {
				console.log("Failed to load users");
			});
	}

	/**
	 * Init (or queue for later init) the select2 multiselect for one item's
	 * "Always show for the following users" field.
	 *
	 * @param {HTMLElement} field The <select multiple> field.
	 */
	function initShowForUsersSelect(field) {
		if (!field || $(field).hasClass("select2-hidden-accessible")) return;

		if (!state.usersLoaded) {
			pendingShowForUsersFields.push(field);
			return;
		}

		var selectedIds = (field.dataset.selectedUsers || "")
			.split(",")
			.filter(function (id) {
				return id !== "";
			});

		$(field).select2({
			placeholder: "Select users",
			data: usersData,
		});

		if (selectedIds.length) {
			$(field).val(selectedIds).trigger("change");
		}
	}

	/**
	 * Get the (single, Default) menu & submenu list.
	 */
	function getMenu() {
		$.ajax({
			url: ajaxurl,
			type: "post",
			dataType: "json",
			data: {
				action: "ats_admin_menu_get_menu",
				nonce: atsAdminMenu.nonces.getMenu,
			},
		}).done(function (r) {
			if (!r || !r.success) return;

			buildMenu(r.data);
		});
	}

	/**
	 * Build menu list.
	 *
	 * @param {array} menuList The menu list returned from ajax response.
	 */
	function buildMenu(menuList) {
		var editArea = document.querySelector(
			"#ats-menu-builder--default-edit-area"
		);
		if (!editArea) return;
		var listArea = editArea.querySelector(".ats-menu-builder--menu-list");
		var builtMenu = "";

		menuList.forEach(function (menu) {
			builtMenu += replaceMenuPlaceholders(menu);
		});

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
	}

	/**
	 * Get visibility meta (dashicon suffix, indicator class, label, and "selected" attrs)
	 * based on the stored is_hidden value.
	 *
	 * @param {string} isHidden "0" (normal), "1" (hidden), or "2" (hidden, but collapsed).
	 * @param {boolean} [roleRestricted] Whether a "hide for role(s)" rule also
	 *  applies on top of `isHidden` - only meaningful when isHidden is "0",
	 *  since "1"/"2" already read as hidden regardless.
	 * @return {object} The visibility meta.
	 */
	function getVisibilityMeta(isHidden, roleRestricted) {
		var value = String(isHidden);
		var meta = {
			icon: "visibility",
			indicatorClass: "is-visible",
			label: "Visible",
			normalSelected: "",
			hiddenSelected: "",
			collapsedSelected: "",
		};

		if (value === "1") {
			meta.icon = "hidden";
			// Not just "is-hidden" - that exact class name is this codebase's own
			// generic "display: none" utility (see .ats-menu-builder-box .is-hidden
			// in ats-menu-builder.css), and this indicator lives inside that same
			// box, so it would get hidden outright rather than just recolored.
			meta.indicatorClass = "is-visibility-hidden";
			meta.label = "Hidden";
			meta.hiddenSelected = "selected";
		} else if (value === "2") {
			meta.icon = "hidden";
			meta.indicatorClass = "is-hidden-collapsed";
			meta.label = "Hidden, but optional";
			meta.collapsedSelected = "selected";
		} else {
			meta.normalSelected = "selected";

			if (roleRestricted) {
				// Still "Normal", but a role-hide rule hides it from some
				// roles - the plain "visible" eye alone would be misleading.
				meta.indicatorClass = "is-visible is-role-restricted";
				meta.label = "Normal, but hidden for some role(s)";
			}
		}

		return meta;
	}

	/**
	 * Apply a visibility value's icon/class/title to an indicator element.
	 * Shared between the Visibility <select> change handler and the
	 * quick-select popover, so both stay visually in sync.
	 *
	 * @param {HTMLElement} indicator The visibility indicator element.
	 * @param {string} value "0", "1", or "2".
	 * @param {boolean} [roleRestricted] See getVisibilityMeta().
	 */
	function applyIndicatorVisibility(indicator, value, roleRestricted) {
		if (!indicator) return;

		var meta = getVisibilityMeta(value, roleRestricted);

		indicator.classList.remove(
			"dashicons-visibility",
			"dashicons-hidden",
			"is-visible",
			"is-visibility-hidden",
			"is-hidden-collapsed",
			"is-role-restricted"
		);
		indicator.classList.add("dashicons-" + meta.icon);
		meta.indicatorClass.split(" ").forEach(function (className) {
			if (className) indicator.classList.add(className);
		});
		indicator.setAttribute("title", meta.label);
	}

	/**
	 * Close any open visibility quick-select popovers.
	 */
	function closeVisibilityPopovers() {
		document
			.querySelectorAll(".ats-menu-builder--visibility-popover.is-active")
			.forEach(function (popover) {
				popover.classList.remove("is-active");
			});
	}

	/**
	 * Wire up the visibility indicator as a quick-select trigger: clicking it
	 * opens a small popover with the 3 Visibility states, and picking one
	 * updates the item immediately (indicator, the Visibility <select> so the
	 * expanded panel stays in sync, and the server via ajax) without needing
	 * to expand the item or hit the main Save button.
	 *
	 * @param {HTMLElement} menuItem The menu or submenu item element.
	 * @param {HTMLElement} indicator The visibility indicator element.
	 * @param {boolean} isSubmenuItem Whether menuItem is a submenu item.
	 */
	function setupVisibilityQuickToggle(menuItem, indicator, isSubmenuItem) {
		if (indicator.dataset.quickToggleInit) return;
		indicator.dataset.quickToggleInit = "1";

		var actionsWrap = indicator.closest(".ats-menu-builder--menu-actions");
		if (actionsWrap) {
			actionsWrap.classList.add("ats-menu-builder--actions-relative");
		}

		var options = [
			{ value: "0", icon: "visibility", label: "Normal" },
			{ value: "1", icon: "hidden", label: "Hidden" },
			{ value: "2", icon: "hidden", label: "Optional" },
		];

		var popover = document.createElement("div");
		popover.className = "ats-menu-builder--visibility-popover";

		options.forEach(function (option) {
			var button = document.createElement("button");
			button.type = "button";
			button.dataset.value = option.value;
			button.innerHTML =
				'<span class="dashicons dashicons-' + option.icon + '"></span>' +
				option.label;

			button.addEventListener("click", function (e) {
				e.stopPropagation();

				menuItem.dataset.hidden = option.value;
				applyIndicatorVisibility(
					indicator,
					option.value,
					isRoleRestricted(readRoleHideData(menuItem))
				);

				var visibilityFieldAttr = isSubmenuItem
					? '[data-name="submenu_visibility"]'
					: '[data-name="menu_visibility"]';
				var visibilityField = menuItem.querySelector(visibilityFieldAttr);
				if (visibilityField) visibilityField.value = option.value;

				closeVisibilityPopovers();
				quickSaveVisibility(menuItem, isSubmenuItem, option.value, indicator);
			});

			popover.appendChild(button);
		});

		(actionsWrap || indicator.parentNode).appendChild(popover);

		indicator.addEventListener("click", function (e) {
			e.stopPropagation();
			var willOpen = !popover.classList.contains("is-active");
			closeVisibilityPopovers();
			if (willOpen) popover.classList.add("is-active");
		});

		document.addEventListener("click", closeVisibilityPopovers);
	}

	/**
	 * Save one item's visibility to the server immediately, without touching
	 * (or requiring) the rest of the form. Finds (or, for a not-yet-customized
	 * default item, creates) that one item in the saved menu and patches only
	 * its is_hidden field - see Ajax\Quick_Update_Visibility::update().
	 *
	 * @param {HTMLElement} menuItem The menu or submenu item element.
	 * @param {boolean} isSubmenuItem Whether menuItem is a submenu item.
	 * @param {string} value The new visibility value.
	 * @param {HTMLElement} indicator The visibility indicator element (for saving-state feedback).
	 */
	function quickSaveVisibility(menuItem, isSubmenuItem, value, indicator) {
		var data = {
			action: "ats_admin_menu_quick_update_visibility",
			nonce: atsAdminMenu.nonces.quickUpdateVisibility,
			is_hidden: value,
			item_type: "menu",
		};

		if (isSubmenuItem) {
			var parentItem = menuItem.closest(
				".ats-menu-builder--menu-item:not(.ats-menu-builder--submenu-item)"
			);
			if (!parentItem) return;

			data.item_key = parentItem.dataset.defaultId;
			data.submenu_key = menuItem.dataset.defaultUrl;
		} else {
			data.item_key = menuItem.dataset.defaultId;
		}

		if (indicator) indicator.classList.add("is-saving");

		$.ajax({
			url: ajaxurl,
			type: "post",
			dataType: "json",
			data: data,
		})
			.done(function (r) {
				if (!r || !r.success) {
					console.warn("ATS Admin Menu: quick visibility update failed", r);
				}
			})
			.fail(function () {
				console.warn("ATS Admin Menu: quick visibility update request failed");
			})
			.always(function () {
				if (indicator) indicator.classList.remove("is-saving");
			});
	}

	/**
	 * Build the role-hide field's meta (checked/hidden attrs, the role checkbox
	 * grid, the capability note, and the show-for-users ids) for one item.
	 *
	 * @param {object} item The menu or submenu item.
	 * @return {object} The role-hide meta.
	 */
	/**
	 * Whether an item's "Always hide for user role(s)" rule actually hides it
	 * from at least one role, given its current mode/role selection. Used to
	 * flag the visibility indicator when the Visibility setting alone (e.g.
	 * "Normal") would otherwise look misleading.
	 *
	 * @param {object} item Item data with role_hide_enabled/role_hide_mode/role_hide_roles.
	 * @return {boolean} Whether the item is effectively role-restricted.
	 */
	function isRoleRestricted(item) {
		var enabled = String(item.role_hide_enabled || "0") === "1";
		if (!enabled) return false;

		var mode = item.role_hide_mode || "selected";
		var hideRoles = Array.isArray(item.role_hide_roles)
			? item.role_hide_roles
			: [];

		if ("all" === mode) return true;

		if ("except" === mode) {
			// hide_roles here is the allow-list (roles that still see it) - only
			// a no-op if literally every role is in that allow-list.
			var allRoles = (window.atsAdminMenu && atsAdminMenu.roles) || [];
			return !allRoles.length || hideRoles.length < allRoles.length;
		}

		// "selected" mode: hide_roles is the roles it's hidden from.
		return hideRoles.length > 0;
	}

	function getRoleHideMeta(item) {
		var enabled = String(item.role_hide_enabled || "0") === "1";
		var mode = item.role_hide_mode || "selected";
		var hideRoles = Array.isArray(item.role_hide_roles)
			? item.role_hide_roles
			: [];
		var roles = (window.atsAdminMenu && atsAdminMenu.roles) || [];

		var rolesCheckboxesHtml = roles
			.map(function (role) {
				var checked = hideRoles.indexOf(role.key) !== -1 ? "checked" : "";
				return (
					'<label class="ats-menu-builder--role-checkbox">' +
					'<input type="checkbox" data-name="role_hide_roles" data-role-slug="' +
					role.key +
					'" ' +
					checked +
					"> " +
					role.name +
					"</label>"
				);
			})
			.join("");

		var cap = item.cap || "";
		var capNoteText = "";

		if (cap && cap !== "read") {
			capNoteText =
				'This menu item already requires the "' +
				cap +
				'" capability - roles without it can\'t reach it regardless of this setting.';
		}

		var showForUsers = Array.isArray(item.show_for_users)
			? item.show_for_users
			: [];

		return {
			enabledChecked: enabled ? "checked" : "",
			optionsHiddenClass: enabled ? "" : "is-hidden",
			modeExceptChecked: "except" === mode ? "checked" : "",
			modeSelectedChecked: "selected" === mode ? "checked" : "",
			rolesHiddenClass: "all" === mode ? "is-hidden" : "",
			rolesCheckboxesHtml: rolesCheckboxesHtml,
			capNoteHiddenClass: capNoteText ? "" : "is-hidden",
			capNoteText: capNoteText,
			showForUsersIds: showForUsers.join(","),
		};
	}

	/**
	 * Replace menu placeholders.
	 *
	 * @param {object} menu The menu item.
	 */
	function replaceMenuPlaceholders(menu) {
		var template;
		var submenuTemplate;
		var icon;

		if (menu.type === "separator") {
			template = atsAdminMenu.templates.menuSeparator;
			template = template.replace(/{separator}/g, menu.url_default);

			template = template.replace(/{menu_is_hidden}/g, menu.is_hidden);
			template = template.replace(
				/{trash_icon}/g,
				parseInt(menu.was_added, 10)
					? '<span class="dashicons dashicons-trash ats-menu-builder--remove-menu-item"></span>'
					: ""
			);
			template = template.replace(
				/{hidden_icon}/g,
				menu.is_hidden == "1" ? "hidden" : "visibility"
			);
			template = template.replace(/{menu_was_added}/g, menu.was_added);
			template = template.replace(/{default_menu_id}/g, menu.id_default);
			template = template.replace(/{default_menu_url}/g, menu.url_default);
		} else {
			template = atsAdminMenu.templates.menuList;
			template = template.replace(/{menu_title}/g, menu.title);
			template = template.replace(/{default_menu_title}/g, menu.title_default);

			var parsedTitle = menu.title ? menu.title : menu.title_default;
			template = template.replace(/{parsed_menu_title}/g, parsedTitle);

			template = template.replace(/{menu_url}/g, menu.url);
			template = template.replace(/{default_menu_url}/g, menu.url_default);

			template = template.replace(
				/{menu_open_new_tab_checked}/g,
				menu.open_new_tab == "1" ? "checked" : ""
			);

			template = template.replace(/{menu_id}/g, menu.id);
			template = template.replace(/{default_menu_id}/g, menu.id_default);

			template = template.replace(/{menu_dashicon}/g, menu.dashicon);
			template = template.replace(
				/{default_menu_dashicon}/g,
				menu.dashicon_default
			);

			template = template.replace(/{menu_icon_svg}/g, menu.icon_svg);
			template = template.replace(
				/{default_menu_icon_svg}/g,
				menu.icon_svg_default
			);

			template = template.replace(/{menu_is_hidden}/g, menu.is_hidden);
			template = template.replace(
				/{trash_icon}/g,
				parseInt(menu.was_added, 10)
					? '<span class="dashicons dashicons-trash ats-menu-builder--remove-menu-item"></span>'
					: ""
			);

			var menuVisibilityMeta = getVisibilityMeta(
			menu.is_hidden,
			isRoleRestricted(menu)
		);

			template = template.replace(/{hidden_icon}/g, menuVisibilityMeta.icon);
			template = template.replace(
				/{visibility_indicator_class}/g,
				menuVisibilityMeta.indicatorClass
			);
			template = template.replace(
				/{visibility_label}/g,
				menuVisibilityMeta.label
			);
			template = template.replace(
				/{menu_visibility_normal_selected}/g,
				menuVisibilityMeta.normalSelected
			);
			template = template.replace(
				/{menu_visibility_hidden_selected}/g,
				menuVisibilityMeta.hiddenSelected
			);
			template = template.replace(
				/{menu_visibility_collapsed_selected}/g,
				menuVisibilityMeta.collapsedSelected
			);
			template = template.replace(/{menu_was_added}/g, menu.was_added);

			var menuRoleHideMeta = getRoleHideMeta(menu);

			template = template.replace(
				/{menu_role_hide_enabled_checked}/g,
				menuRoleHideMeta.enabledChecked
			);
			template = template.replace(
				/{menu_role_hide_options_hidden_class}/g,
				menuRoleHideMeta.optionsHiddenClass
			);
			template = template.replace(
				/{menu_role_hide_mode_except_checked}/g,
				menuRoleHideMeta.modeExceptChecked
			);
			template = template.replace(
				/{menu_role_hide_mode_selected_checked}/g,
				menuRoleHideMeta.modeSelectedChecked
			);
			template = template.replace(
				/{menu_role_hide_roles_hidden_class}/g,
				menuRoleHideMeta.rolesHiddenClass
			);
			template = template.replace(
				/{menu_role_hide_roles_checkboxes}/g,
				menuRoleHideMeta.rolesCheckboxesHtml
			);
			template = template.replace(
				/{menu_role_hide_cap_note_hidden_class}/g,
				menuRoleHideMeta.capNoteHiddenClass
			);
			template = template.replace(
				/{menu_role_hide_cap_note}/g,
				menuRoleHideMeta.capNoteText
			);
			template = template.replace(
				/{menu_show_for_users_ids}/g,
				menuRoleHideMeta.showForUsersIds
			);

			var menuIconSuffix =
				menu.icon_type && menu[menu.icon_type] ? "" : "_default";

			if (menu["icon_type" + menuIconSuffix] === "icon_svg") {
				icon = '<img alt="" src="' + menu["icon_svg" + menuIconSuffix] + '">';
				template = template.replace(/{icon_svg_tab_is_active}/g, "is-active");
				template = template.replace(/{dashicon_tab_is_active}/g, "");
			} else {
				icon =
					'<i class="dashicons ' + menu["dashicon" + menuIconSuffix] + '"></i>';
				template = template.replace(/{icon_svg_tab_is_active}/g, "");
				template = template.replace(/{dashicon_tab_is_active}/g, "is-active");
			}

			template = template.replace(/{menu_icon}/g, icon);

			if (menu.submenu) {
				submenuTemplate = buildSubmenu(menu);
				template = template.replace(/{submenu_template}/g, submenuTemplate);
			} else {
				template = template.replace(/{submenu_template}/g, "");
			}
		}

		template = template.replace(/{role}/g, "default");

		return template;
	}

	/**
	 * Build submenu list.
	 *
	 * @param {array} menu The menu item which contains the submenu list.
	 *
	 * @return {string} template The submenu template.
	 */
	function buildSubmenu(menu) {
		var template = "";

		menu.submenu.forEach(function (submenu) {
			template += replaceSubmenuPlaceholders(submenu, menu);
		});

		return template;
	}

	/**
	 * Replace submenu placeholders.
	 *
	 * @param {object} submenu The submenu item.
	 * @param {array} menu The menu item which contains the submenu list.
	 */
	function replaceSubmenuPlaceholders(submenu, menu) {
		var template = atsAdminMenu.templates.submenuList;

		template = template.replace(/{role}/g, "default");

		template = template.replace(
			/{default_menu_id}/g,
			menu ? menu.id_default : submenu.id
		);

		var submenuId = submenu.id ? submenu.id : submenu.url_default;
		submenuId = submenuId.replace(/\//g, "atsslashsign");
		template = template.replace(/{submenu_id}/g, submenuId);

		template = template.replace(/{submenu_title}/g, submenu.title);
		template = template.replace(
			/{default_submenu_title}/g,
			submenu.title_default
		);

		var parsedTitle = submenu.title ? submenu.title : submenu.title_default;
		template = template.replace(/{parsed_submenu_title}/g, parsedTitle);

		template = template.replace(/{submenu_url}/g, submenu.url);
		template = template.replace(/{default_submenu_url}/g, submenu.url_default);

		template = template.replace(
			/{submenu_open_new_tab_checked}/g,
			submenu.open_new_tab == "1" ? "checked" : ""
		);

		template = template.replace(/{submenu_is_hidden}/g, submenu.is_hidden);
		template = template.replace(
			/{trash_icon}/g,
			parseInt(submenu.was_added, 10)
				? '<span class="dashicons dashicons-trash ats-menu-builder--remove-menu-item"></span>'
				: ""
		);

		var submenuVisibilityMeta = getVisibilityMeta(
		submenu.is_hidden,
		isRoleRestricted(submenu)
	);

		template = template.replace(/{hidden_icon}/g, submenuVisibilityMeta.icon);
		template = template.replace(
			/{visibility_indicator_class}/g,
			submenuVisibilityMeta.indicatorClass
		);
		template = template.replace(
			/{visibility_label}/g,
			submenuVisibilityMeta.label
		);
		template = template.replace(
			/{submenu_visibility_normal_selected}/g,
			submenuVisibilityMeta.normalSelected
		);
		template = template.replace(
			/{submenu_visibility_hidden_selected}/g,
			submenuVisibilityMeta.hiddenSelected
		);
		template = template.replace(
			/{submenu_visibility_collapsed_selected}/g,
			submenuVisibilityMeta.collapsedSelected
		);
		template = template.replace(/{submenu_was_added}/g, submenu.was_added);

		var submenuRoleHideMeta = getRoleHideMeta(submenu);

		template = template.replace(
			/{submenu_role_hide_enabled_checked}/g,
			submenuRoleHideMeta.enabledChecked
		);
		template = template.replace(
			/{submenu_role_hide_options_hidden_class}/g,
			submenuRoleHideMeta.optionsHiddenClass
		);
		template = template.replace(
			/{submenu_role_hide_mode_except_checked}/g,
			submenuRoleHideMeta.modeExceptChecked
		);
		template = template.replace(
			/{submenu_role_hide_mode_selected_checked}/g,
			submenuRoleHideMeta.modeSelectedChecked
		);
		template = template.replace(
			/{submenu_role_hide_roles_hidden_class}/g,
			submenuRoleHideMeta.rolesHiddenClass
		);
		template = template.replace(
			/{submenu_role_hide_roles_checkboxes}/g,
			submenuRoleHideMeta.rolesCheckboxesHtml
		);
		template = template.replace(
			/{submenu_role_hide_cap_note_hidden_class}/g,
			submenuRoleHideMeta.capNoteHiddenClass
		);
		template = template.replace(
			/{submenu_role_hide_cap_note}/g,
			submenuRoleHideMeta.capNoteText
		);
		template = template.replace(
			/{submenu_show_for_users_ids}/g,
			submenuRoleHideMeta.showForUsersIds
		);

		return template;
	}

	/**
	 * Setup menu items.
	 */
	function setupMenuItems(listArea, isSubmenu) {
		setupSortable(listArea, isSubmenu);

		if (!isSubmenu) {
			setupItemChanges(listArea);
			$(listArea).find(".dashicons-picker").dashiconsPicker();
		}
	}

	/**
	 * Setup a newly added menu item (both parent menu item and submenu item).
	 *
	 * @param {HTMLElement} menuItem - The menu item. Can be either parent menu item or submenu item.
	 * @param {boolean} isSubmenu - Whether menuItem is a submenu item.F
	 */
	function setupNewMenuItem(menuItem, isSubmenu) {
		setupSortable(menuItem.parentNode, isSubmenu);
		setupItemChange(menuItem);

		if (!isSubmenu) {
			$(menuItem).find(".dashicons-picker").dashiconsPicker();
		}
	}

	/**
	 * Sortable setup for both active & available widgets.
	 */
	function setupSortable(listArea, isSubmenu) {
		$(listArea).sortable({
			connectWith: isSubmenu ? ".ats-menu-builder--submenu-list" : false,
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
	 * show / hide menu item (separators only - regular menu/submenu items use
	 * the Visibility dropdown instead, see setupItemChange()).
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
	 * Setup item changes.
	 * @param {HTMLElement} listArea The list area element.
	 */
	function setupItemChanges(listArea) {
		var menuItems = listArea.querySelectorAll(".ats-menu-builder--menu-item");
		if (!menuItems.length) return;

		menuItems.forEach(function (menuItem) {
			setupItemChange(menuItem);
		});
	}

	/**
	 * Setup item change.
	 * @param {HTMLElement} menuItem The menu item element.
	 */
	function setupItemChange(menuItem) {
		var isSubmenuItem = menuItem.classList.contains(
			"ats-menu-builder--submenu-item"
		);
		var iconFields = menuItem.querySelectorAll(".ats-menu-builder--icon-field");
		iconFields = iconFields.length ? iconFields : [];

		iconFields.forEach(function (field) {
			field.addEventListener("change", function () {
				var iconWrapper = menuItem.querySelector(
					".ats-menu-builder--menu-icon"
				);
				var iconOutput;

				if (this.dataset.name === "dashicon") {
					iconOutput = '<i class="dashicons ' + this.value + '"></i>';
				} else if (this.dataset.name === "icon_svg") {
					iconOutput = '<img alt="" src="' + this.value + '">';
				}

				iconWrapper.innerHTML = iconOutput;
			});
		});

		var visibilityFieldDataAttr = isSubmenuItem
			? '[data-name="submenu_visibility"]'
			: '[data-name="menu_visibility"]';

		var visibilityField = menuItem.querySelector(visibilityFieldDataAttr);
		var visibilityIndicator = menuItem.querySelector(
			".ats-menu-builder--visibility-indicator"
		);

		if (visibilityField) {
			visibilityField.addEventListener("change", function () {
				menuItem.dataset.hidden = this.value;
				applyIndicatorVisibility(
					visibilityIndicator,
					this.value,
					isRoleRestricted(readRoleHideData(menuItem))
				);
			});
		}

		// Keep the indicator's "restricted" badge in sync as the role-hide
		// rule itself is edited, independent of the Visibility <select>.
		function refreshVisibilityIndicator() {
			if (!visibilityIndicator) return;

			var currentValue = visibilityField
				? visibilityField.value
				: menuItem.dataset.hidden || "0";

			applyIndicatorVisibility(
				visibilityIndicator,
				currentValue,
				isRoleRestricted(readRoleHideData(menuItem))
			);
		}

		// Separators use a simple binary hide-menu toggle instead (see
		// showHideMenuItem()) - this quick-select popover is only for the
		// 3-way Visibility state on menu/submenu items.
		if (
			visibilityIndicator &&
			!menuItem.classList.contains("ats-menu-builder--separator-item")
		) {
			setupVisibilityQuickToggle(menuItem, visibilityIndicator, isSubmenuItem);
		}

		var titleFieldDataAttr = isSubmenuItem
			? '[data-name="submenu_title"]'
			: '[data-name="menu_title"]';

		var titleFields = menuItem.querySelectorAll(titleFieldDataAttr);
		titleFields = titleFields.length ? titleFields : [];

		titleFields.forEach(function (field) {
			field.addEventListener("change", function () {
				var value = this.value ? this.value : this.placeholder;
				menuItem.querySelector(".ats-menu-builder--menu-name").innerHTML =
					value;
			});
		});

		// "Always hide for user role(s)" - checkbox reveals the mode/role controls.
		var roleHideField = menuItem.querySelector(
			".ats-menu-builder--role-hide-field"
		);

		if (roleHideField) {
			var roleHideToggle = roleHideField.querySelector(
				'[data-name="role_hide_enabled"]'
			);
			var roleHideOptions = roleHideField.querySelector(
				".ats-menu-builder--role-hide-options"
			);

			if (roleHideToggle && roleHideOptions) {
				roleHideToggle.addEventListener("change", function () {
					roleHideOptions.classList.toggle("is-hidden", !this.checked);
					refreshVisibilityIndicator();
				});
			}

			var roleHideModeRadios = roleHideField.querySelectorAll(
				'[data-name="role_hide_mode"]'
			);
			var roleHideRolesContainer = roleHideField.querySelector(
				".ats-menu-builder--role-hide-roles"
			);

			roleHideModeRadios.forEach(function (radio) {
				radio.addEventListener("change", function () {
					if (!roleHideRolesContainer) return;
					roleHideRolesContainer.classList.toggle(
						"is-hidden",
						this.value === "all"
					);
					refreshVisibilityIndicator();
				});
			});

			// Individual role checkboxes (shown for "selected"/"except" modes)
			// also affect whether the rule is actually restrictive.
			roleHideField.addEventListener("change", function (e) {
				if (e.target && e.target.dataset.name === "role_hide_roles") {
					refreshVisibilityIndicator();
				}
			});
		}

		// "Always show for the following users" multiselect.
		var showForUsersField = menuItem.querySelector(
			'[data-name="show_for_users"]'
		);

		if (showForUsersField) {
			initShowForUsersSelect(showForUsersField);
		}

		// Menu/submenu URL - search-as-you-type autocomplete.
		var urlFieldDataAttr = isSubmenuItem
			? '[data-name="submenu_url"]'
			: '[data-name="menu_url"]';

		var urlField = menuItem.querySelector(urlFieldDataAttr);

		if (urlField) {
			setupUrlAutocomplete(urlField);
		}
	}

	/**
	 * Wire up search-as-you-type suggestions on a Menu URL / Submenu URL field -
	 * searches published content and the site's admin pages (this plugin's own
	 * included), similar to Elementor's URL control. Free text always still works;
	 * picking a suggestion just fills the field.
	 *
	 * @param {HTMLElement} field The text input to attach suggestions to.
	 */
	function setupUrlAutocomplete(field) {
		if (field.dataset.urlAutocompleteInit) return;
		field.dataset.urlAutocompleteInit = "1";

		var control = field.closest(".control");
		if (control) control.classList.add("ats-menu-builder--url-control");

		var dropdown = document.createElement("ul");
		dropdown.className = "ats-menu-builder--url-suggestions";
		field.insertAdjacentElement("afterend", dropdown);

		var debounceTimer = null;
		var requestId = 0;

		field.addEventListener("input", function () {
			var term = field.value.trim();
			clearTimeout(debounceTimer);

			if (term.length < 2) {
				dropdown.classList.remove("is-active");
				return;
			}

			showUrlSuggestionsLoading(dropdown);

			var thisRequestId = ++requestId;

			debounceTimer = setTimeout(function () {
				searchUrls(term, function (groups) {
					// A newer search has started since this one went out - drop this response.
					if (thisRequestId !== requestId) return;

					renderUrlSuggestions(dropdown, groups, field);
				});
			}, 300);
		});

		field.addEventListener("focus", function () {
			if (dropdown.childElementCount) dropdown.classList.add("is-active");
		});

		field.addEventListener("blur", function () {
			// Delay so a click on a suggestion (mousedown) registers before we hide.
			setTimeout(function () {
				dropdown.classList.remove("is-active");
			}, 150);
		});
	}

	/**
	 * Ajax search for a URL suggestion.
	 *
	 * @param {string} term The search term.
	 * @param {Function} callback Called with the select2-shaped grouped results.
	 */
	function searchUrls(term, callback) {
		$.ajax({
			url: ajaxurl,
			type: "post",
			dataType: "json",
			data: {
				action: "ats_admin_menu_search_urls",
				nonce: atsAdminMenu.nonces.searchUrls,
				term: term,
			},
		}).done(function (r) {
			if (!r || !r.success) return;
			callback(r.data);
		});
	}

	/**
	 * Show a "Searching..." placeholder in the suggestions dropdown while a
	 * search is in flight (covers both the ajax round trip and the debounce).
	 *
	 * @param {HTMLElement} dropdown The suggestions <ul>.
	 */
	function showUrlSuggestionsLoading(dropdown) {
		dropdown.innerHTML = "";

		var loading = document.createElement("li");
		loading.className = "ats-menu-builder--url-suggestions-loading";
		loading.textContent = "Searching…";
		dropdown.appendChild(loading);

		dropdown.classList.add("is-active");
	}

	/**
	 * Render grouped URL suggestions into a dropdown, and wire up selecting one.
	 *
	 * @param {HTMLElement} dropdown The suggestions <ul>.
	 * @param {array} groups The select2-shaped grouped results.
	 * @param {HTMLElement} field The URL field the suggestions belong to.
	 */
	function renderUrlSuggestions(dropdown, groups, field) {
		dropdown.innerHTML = "";

		var hasMatches =
			groups &&
			groups.some(function (group) {
				return group.children && group.children.length;
			});

		if (!hasMatches) {
			var empty = document.createElement("li");
			empty.className = "ats-menu-builder--url-suggestions-empty";
			empty.textContent = "No matches found";
			dropdown.appendChild(empty);
			dropdown.classList.add("is-active");
			return;
		}

		groups.forEach(function (group) {
			if (!group.children || !group.children.length) return;

			var groupLabel = document.createElement("li");
			groupLabel.className = "ats-menu-builder--url-suggestions-group";
			groupLabel.textContent = group.text;
			dropdown.appendChild(groupLabel);

			group.children.forEach(function (item) {
				var option = document.createElement("li");
				option.className = "ats-menu-builder--url-suggestion";

				var titleEl = document.createElement("span");
				titleEl.className = "ats-menu-builder--url-suggestion-title";
				titleEl.textContent = item.text;
				option.appendChild(titleEl);

				var urlEl = document.createElement("span");
				urlEl.className = "ats-menu-builder--url-suggestion-path";
				urlEl.textContent = shortenSuggestionUrl(item.id);
				option.appendChild(urlEl);

				option.addEventListener("mousedown", function (e) {
					// Keep focus on the field so the blur handler doesn't hide the
					// dropdown before this click is registered.
					e.preventDefault();

					field.value = item.id;
					field.dispatchEvent(new Event("change", { bubbles: true }));
					fillTitleFromUrlSuggestion(field, item.text);
					dropdown.classList.remove("is-active");
				});

				dropdown.appendChild(option);
			});
		});

		dropdown.classList.add("is-active");
	}

	/**
	 * Shorten a suggestion's full URL down to just its path + query, so
	 * same-titled entries (e.g. two different "Categories" pages) are
	 * distinguishable in the dropdown without a wall of repeated domain text.
	 *
	 * @param {string} url The full URL.
	 * @return {string} The shortened URL.
	 */
	function shortenSuggestionUrl(url) {
		try {
			var parsed = new URL(url, window.location.origin);
			return parsed.pathname + parsed.search;
		} catch (e) {
			return url;
		}
	}

	/**
	 * Fill in the Menu/Submenu Title field from a picked URL suggestion, but
	 * only if the title field is currently empty - never clobber a title the
	 * user already typed themselves.
	 *
	 * @param {HTMLElement} urlField The Menu URL / Submenu URL field just filled.
	 * @param {string} suggestedTitle The picked suggestion's title.
	 */
	function fillTitleFromUrlSuggestion(urlField, suggestedTitle) {
		if (!suggestedTitle) return;

		var menuItem = urlField.closest(".ats-menu-builder--menu-item");
		if (!menuItem) return;

		var titleFieldName =
			urlField.dataset.name === "submenu_url" ? "submenu_title" : "menu_title";
		var titleField = menuItem.querySelector('[data-name="' + titleFieldName + '"]');

		if (!titleField || titleField.value.trim() !== "") return;

		titleField.value = suggestedTitle;
		titleField.dispatchEvent(new Event("change", { bubbles: true }));
	}

	loading.start = function (button) {
		button.classList.add("is-loading");
	};

	loading.stop = function (button) {
		button.classList.remove("is-loading");
	};

	/**
	 * Read one item's (or submenu item's) role-hide fields, scoped to its own
	 * ".ats-menu-builder--role-hide-field" wrapper so a parent menu item's read
	 * never picks up a nested submenu item's fields (they're actually siblings
	 * in a different tab, but this keeps the lookup explicit either way).
	 *
	 * @param {HTMLElement} itemEl The menu or submenu item element.
	 * @return {object} { role_hide_enabled, role_hide_mode, role_hide_roles, show_for_users }
	 */
	function readRoleHideData(itemEl) {
		var roleHideField = itemEl.querySelector(
			".ats-menu-builder--role-hide-field"
		);

		var enabledField = roleHideField
			? roleHideField.querySelector('[data-name="role_hide_enabled"]')
			: null;
		var modeField = roleHideField
			? roleHideField.querySelector('[data-name="role_hide_mode"]:checked')
			: null;
		var roleCheckboxes = roleHideField
			? roleHideField.querySelectorAll('[data-name="role_hide_roles"]:checked')
			: [];

		var showForUsersField = itemEl.querySelector('[data-name="show_for_users"]');

		return {
			role_hide_enabled: enabledField && enabledField.checked ? "1" : "0",
			role_hide_mode: modeField ? modeField.value : "selected",
			role_hide_roles: Array.prototype.map.call(
				roleCheckboxes,
				function (checkbox) {
					return checkbox.dataset.roleSlug;
				}
			),
			show_for_users: showForUsersField
				? $(showForUsersField).val() || []
				: [],
		};
	}

	/**
	 * Function to execute on form submission.
	 *
	 * @param {Event} e The on submit event.
	 */
	function submitForm(e) {
		e.preventDefault();

		var workspace = this.querySelector(".ats-menu-builder--workspace");
		if (!workspace) return;

		var menuList = [];

		// The submenu <ul> also carries the "ats-menu-builder--menu-list" class (see
		// menu-list.php's Submenu tab), so this needs ":scope >" - without it, "…menu-list
		// > …menu-item" matches every submenu list's own direct children too, flattening
		// every submenu item into the top-level list as a duplicate fake top-level item.
		var menuItems = workspace.querySelectorAll(
			":scope > .ats-menu-builder--menu-list > .ats-menu-builder--menu-item"
		);
		menuItems = menuItems.length ? menuItems : [];

		menuItems.forEach(function (menuItem) {
			var menuData = {};

			menuData.type = menuItem.classList.contains(
				"ats-menu-builder--separator-item"
			)
				? "separator"
				: "menu";
			menuData.is_hidden = menuItem.dataset.hidden;
			menuData.was_added = menuItem.dataset.added;
			menuData.url = "";

			menuData.id = "";
			menuData.class = "";
			menuData.url_default = menuItem.dataset.defaultUrl;

			if (menuData.type === "separator") {
				menuData.title = "";
				menuData.dashicon = "";
				menuData.icon_svg = "";
				menuData.icon_type = "";

				if (parseInt(menuItem.dataset.added, 10)) {
					menuData.id_default = menuItem.dataset.defaultId;
					menuData.url = menuItem.dataset.defaultUrl;
					menuData.class_default = "wp-menu-separator ats-menu-separator";
				} else {
					menuData.id_default = "";
				}
			} else {
				menuData.id_default = menuItem.dataset.defaultId;

				var menuTitleField = menuItem.querySelector('[data-name="menu_title"]');
				if (!menuTitleField) {
					console.warn(
						"ATS Admin Menu: menu_title field missing for item",
						menuItem.dataset.defaultId,
						"- saving with an empty title. If this keeps happening, please report it."
					);
				}
				menuData.title = menuTitleField ? menuTitleField.value : "";

				// The menu_url didn't exist in v3.1.3 and below.
				if (menuItem.querySelector('[data-name="menu_url"]')) {
					menuData.url = menuItem.querySelector(
						'[data-name="menu_url"]'
					).value;
				}

				var menuDashiconField = menuItem.querySelector('[data-name="dashicon"]');
				var menuIconSvgField = menuItem.querySelector('[data-name="icon_svg"]');
				menuData.dashicon = menuDashiconField ? menuDashiconField.value : "";
				menuData.icon_svg = menuIconSvgField ? menuIconSvgField.value : "";
				menuData.icon_type = "";

				var menuOpenNewTabField = menuItem.querySelector(
					'[data-name="menu_open_new_tab"]'
				);
				menuData.open_new_tab =
					menuOpenNewTabField && menuOpenNewTabField.checked ? "1" : "";

				var iconSvgTab = menuItem.querySelector('[data-tab-name="icon_svg"]');

				if (menuData.dashicon || menuData.icon_svg) {
					menuData.icon_type = "dashicon";

					if (iconSvgTab && iconSvgTab.classList.contains("is-active")) {
						if (menuData.icon_svg) {
							menuData.icon_type = "icon_svg";
						}
					}
				}

				if (parseInt(menuItem.dataset.added, 10)) {
					menuData.id = menuItem.dataset.defaultId;
					menuData.class_default =
						"menu-top menu-icon-custom ats-menu-top ats-menu-icon-custom";
				}

				var menuRoleHideData = readRoleHideData(menuItem);
				menuData.role_hide_enabled = menuRoleHideData.role_hide_enabled;
				menuData.role_hide_mode = menuRoleHideData.role_hide_mode;
				menuData.role_hide_roles = menuRoleHideData.role_hide_roles;
				menuData.show_for_users = menuRoleHideData.show_for_users;
			}

			var submenuItems = menuItem.querySelectorAll(
				".ats-menu-builder--submenu-item"
			);
			submenuItems = submenuItems.length ? submenuItems : [];
			var submenuList = [];

			submenuItems.forEach(function (submenuItem) {
				var submenuData = {};

				submenuData.is_hidden = submenuItem.dataset.hidden;
				submenuData.was_added = submenuItem.dataset.added;

				var submenuTitleField = submenuItem.querySelector(
					'[data-name="submenu_title"]'
				);
				if (!submenuTitleField) {
					console.warn(
						"ATS Admin Menu: submenu_title field missing for a submenu item under",
						menuItem.dataset.defaultId,
						"- saving with an empty title. If this keeps happening, please report it."
					);
				}
				submenuData.title = submenuTitleField ? submenuTitleField.value : "";
				submenuData.url = "";

				// The submenu_url didn't exist in v3.1.3 and below.
				if (submenuItem.querySelector('[data-name="submenu_url"]')) {
					submenuData.url = submenuItem.querySelector(
						'[data-name="submenu_url"]'
					).value;
				}

				submenuData.url_default = submenuItem.dataset.defaultUrl;

				var submenuOpenNewTabField = submenuItem.querySelector(
					'[data-name="submenu_open_new_tab"]'
				);
				submenuData.open_new_tab =
					submenuOpenNewTabField && submenuOpenNewTabField.checked
						? "1"
						: "";

				var submenuRoleHideData = readRoleHideData(submenuItem);
				submenuData.role_hide_enabled = submenuRoleHideData.role_hide_enabled;
				submenuData.role_hide_mode = submenuRoleHideData.role_hide_mode;
				submenuData.role_hide_roles = submenuRoleHideData.role_hide_roles;
				submenuData.show_for_users = submenuRoleHideData.show_for_users;

				submenuList.push(submenuData);
			});

			if (submenuList) menuData.submenu = submenuList;
			menuList.push(menuData);
		});

		saveMenu(menuList);
	}

	/**
	 * Send ajax request to save the menu list.
	 *
	 * @param {array} menuList The menu list.
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
				action: "ats_admin_menu_save_menu",
				nonce: atsAdminMenu.nonces.saveMenu,
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
	 * Send ajax request to reset the menu back to WordPress defaults.
	 */
	function resetMenu() {
		var button = this;

		if (state.isSaving) return;

		if (!confirm(atsAdminMenu.warningMessages.resetMenu)) return;

		state.isSaving = true;

		loading.start(button);

		$.ajax({
			url: ajaxurl,
			type: "post",
			dataType: "json",
			data: {
				action: "ats_admin_menu_reset_menu",
				nonce: atsAdminMenu.nonces.resetMenu,
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
