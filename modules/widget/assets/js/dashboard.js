(function ($) {
	$('.postbox-container .inside').each(function () {
		if ($(this).children().hasClass('ats-content-wrapper')) {
			$(this).parent().addClass('ats-content');

			var widgetHeight = $(this).children().attr('data-ats-content-height');
			$(this).children().height(widgetHeight);

		}
	});

	$('.ats-video-preview-image-wrapper').click(function () {
		var $overlay = $(this).next('.ats-video-overlay');
		var $iframe = $overlay.find('.ats-video');
		var url = $overlay.attr('data-ats-video-src');

		$overlay.add($iframe).fadeIn(200);
		$iframe.attr("src", url);
		$(window).trigger('resize');
	});

	// Window Resize
	$(window).resize(function () {
		$('.ats-video').each(function () {
			var videoWidth = $(this).width();
			$(this).height(videoWidth * 0.5625);
		});
	});

	function close_ats_popup($overlay) {
		var $targetOverlay = $overlay || $('.ats-video-overlay');
		var $targetVideo = $targetOverlay.find('.ats-video');

		$targetOverlay.add($targetVideo).fadeOut(200);

		setTimeout(function () {
			$targetVideo.attr('src', '');
		}, 200);
	}

	$('.ats-video-overlay').click(function () {
		close_ats_popup($(this));
	});

	// Close on Escape
	$(document).keyup(function (e) {
		if (e.keyCode == 27) {
			if ($('.ats-video-overlay').is(':visible')) {
				close_ats_popup();
			}
		}
	});

	// Handle form widget
	$('.ats-form-widget').on('submit', function (e) {
		e.preventDefault();

		var $form = $(this);
		var $submitBtn = $form.find('.submit-button');
		var $notice = $form.find('.ats-form-notice');

		$submitBtn.html(ATSDashContactForm.labels.submitting);
		$submitBtn.attr('disabled', true);

		var data = {};

		$($form.serializeArray()).each(function (i, item) {
			if (item.name) {
				data[item.name] = item.value;
			}
		});

		data.action = 'ats_submit_contact_form';

		jQuery.ajax({
			type: "POST",
			dataType: "json",
			url: ATSDashDashboard.ajaxUrl,
			data: data
		}).done(function (data) {
			if (data.success) {
				$notice.show();
				$notice.html(data.data.message);
				$form.trigger('reset');
			} else {
				$notice.show();
				$notice.html(data.data.message);
			}
		}).fail(function () {
			// This will be executed on server error like "500 Internal Server Error".
			$notice.show();
			$notice.html("Server error or network problem");
		}).always(function () {
			$submitBtn.html(ATSDashContactForm.labels.submit);
			$submitBtn.attr('disabled', false);
			$notice.delay(4000).fadeOut(1000);
		});
	})
})(jQuery);