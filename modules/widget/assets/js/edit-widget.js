(function ($) {
	function init() {
		setupWidgetTypeFields();
		setupWidgetRoles();
		setupVideoThumbnail();
		setupCustomRecipient();
		setupContactForm();
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
