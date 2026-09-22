(function ($) {
	var htmlCodeEditor = null;

	function init() {
		setupWidgetTypeFields();
		setupWidgetRoles();
		setupVideoThumbnail();
		setupCustomRecipient();
		setupContactForm();
		setupHtmlCodeEditor();
		setupCssCodeEditor();
		setupColorFields();
	}

	/**
	 * Set up the color picker on any `.ats-color-field` inputs (e.g. the
	 * Icon widget's icon color field).
	 */
	function setupColorFields() {
		var fields = document.querySelectorAll( ".ats-color-field" );
		if ( ! fields.length ) return;

		fields.forEach( function ( el ) {
			$( el ).wpColorPicker( { defaultColor: el.dataset.default } );
		} );
	}

	/**
	 * Set up CodeMirror (line numbers, HTML syntax highlighting, HTMLHint
	 * error checking) on the HTML widget's content field.
	 */
	function setupHtmlCodeEditor() {
		var el = document.getElementById( "ats_html" );
		if ( ! el || ! window.wp || ! wp.codeEditor ) return;

		// Guard against this script running more than once on the page (e.g.
		// a "pro" build enqueuing the same file under a second handle) —
		// initializing CodeMirror twice on one textarea doubles the editor.
		if ( el.dataset.atsCodemirrorInitialized ) return;
		el.dataset.atsCodemirrorInitialized = "1";

		var editorSettings = wp.codeEditor.defaultSettings
			? _.clone( wp.codeEditor.defaultSettings )
			: {};

		editorSettings.codemirror = _.extend( {}, editorSettings.codemirror, {
			indentUnit: 4,
			tabSize: 4,
			mode: "htmlmixed",
		} );

		htmlCodeEditor = wp.codeEditor.initialize( el, editorSettings );

		setTimeout( function () {
			htmlCodeEditor.codemirror.refresh();
		}, 300 );
	}

	/**
	 * Set up CodeMirror (line numbers, CSS syntax highlighting) on the
	 * per-widget Custom CSS field. This field is always visible regardless
	 * of widget type, so it doesn't need the same visibility-refresh
	 * handling as the HTML field.
	 */
	function setupCssCodeEditor() {
		var el = document.getElementById( "ats_custom_css" );
		if ( ! el || ! window.wp || ! wp.codeEditor ) return;

		if ( el.dataset.atsCodemirrorInitialized ) return;
		el.dataset.atsCodemirrorInitialized = "1";

		var editorSettings = wp.codeEditor.defaultSettings
			? _.clone( wp.codeEditor.defaultSettings )
			: {};

		editorSettings.codemirror = _.extend( {}, editorSettings.codemirror, {
			indentUnit: 4,
			tabSize: 4,
			mode: "css",
		} );

		var cssCodeEditor = wp.codeEditor.initialize( el, editorSettings );

		setTimeout( function () {
			cssCodeEditor.codemirror.refresh();
		}, 300 );
	}

	/**
	 * Show the fields for the selected widget type.
	 */
	function setupWidgetTypeFields() {
		var $type = $( 'select[name="ats_widget_type"]' );
		var $fields = $( '#ats-main-metabox .widget-fields [data-type]' );

		if ( ! $type.length || ! $fields.length ) {
			return;
		}

		function updateFields() {
			var selectedType = $type.val();

			$fields.removeClass( 'is-active' );
			$fields.filter( '[data-type="' + selectedType + '"]' ).addClass( 'is-active' );

			// CodeMirror measures itself at init time — if the HTML field was
			// hidden then, it renders collapsed until refreshed while visible.
			if ( "html" === selectedType && htmlCodeEditor ) {
				setTimeout( function () {
					htmlCodeEditor.codemirror.refresh();
				}, 10 );
			}
		}

		$type.on( 'change', updateFields );
		updateFields();
	}

	/**
	 * Setup widget roles.
	 */
	function setupWidgetRoles() {
		var fields = document.querySelectorAll(".ats-widget-roles-field");
		if (!fields.length) return;

		fields.forEach(function (field) {
			setupWidgetRole(field);
		});
	}

	/**
	 * Setup widget role.
	 *
	 * @param HTMLElement field The widget role's select box.
	 */
	function setupWidgetRole(field) {
		var $field = $(field);

		$field.select2();

		$field.on("select2:select", function (e) {
			var selections = $field.select2("data");
			var values = [];

			if (e.params.data.id === "all") {
				$field.val("all");
				$field.trigger("change");
			} else {
				if (selections.length) {
					selections.forEach(function (role) {
						if (role.id !== "all") {
							values.push(role.id);
						}
					});

					$field.val(values);
					$field.trigger("change");
				}
			}
		});
	}

	function setupVideoThumbnail() {
		$(".ats-video-thumbnail-upload").click(function (e) {
			e.preventDefault();

			var custom_uploader = wp
				.media({
					title: "Video Thumbnail",
					button: {
						text: "Upload Image",
					},
					multiple: false, // Set this to true to allow multiple files to be selected
				})
				.on("select", function () {
					var attachment = custom_uploader
						.state()
						.get("selection")
						.first()
						.toJSON();
					$(".ats-video-thumbnail-url").val(attachment.url);
				})
				.open();
		});

		$(".ats-video-thumbnail-remove").click(function (e) {
			e.preventDefault();
			$(this).prev().prev().val("");
		});
	}

	function setupCustomRecipient() {
		$(document).on("change", "#ats_form_enable_custom_to_address", function () {
			var check = $("#ats_form_enable_custom_to_address").is(":checked");
			if (check === true) {
				$("#ats-form-widget-custom-recipient").slideDown();
				$("#ats_form_custom_to_address").attr("required", true);
			} else {
				$("#ats-form-widget-custom-recipient").slideUp();
				$("#ats_form_custom_to_address").attr("required", false);
			}
		});
	}

	function setupContactForm() {
		$("#ats-clear-log").on("click", clearContactFormLogs);
	}

	function clearContactFormLogs() {
		var $clearBtn = $(this);
		var $logNotice = $("#clear_log_notice");
		var isCleared = false;

		$clearBtn.html(ATSDashEditWidget.labels.clearingLog);
		$clearBtn.attr("disabled", true);

		$.ajax({
			type: "GET",
			dataType: "json",
			url: ATSDashEditWidget.ajaxUrl,
			data: {
				action: "ats_contact_form_clear_logs",
				nonce: document.querySelector("#ats_clear_contact_form_nonce").value,
				post_id: $clearBtn.attr("data-post-id"),
			},
		})
			.done(function (data) {
				if (data.success) {
					isCleared = true;

					// Hide the log content container, then remove it from the DOM.
					$clearBtn
						.siblings(".ats-metabox-field")
						.has("#ats_clear_contact_form_nonce")
						.slideUp(function () {
							$(this).remove();
						});

					$logNotice.show();
					$logNotice.html(data.data.message);
					$logNotice.delay(4000).fadeOut(1000);
				} else {
					$logNotice.show();
					$logNotice.html(data.data.message);
					$logNotice.delay(4000).fadeOut(1000);
				}
			})
			.always(function () {
				$clearBtn.html(ATSDashEditWidget.labels.clearLog);

				// Only re-enable the button if the logs were not cleared.
				if (!isCleared) {
					$clearBtn.attr("disabled", false);
				}
			});
	}

	init();
})(jQuery);
