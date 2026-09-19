import jQuery from 'jquery';

const insertProLink = () => {
	var proLink =
		'\
		<li class="accordion-section control-section ats-pro-control-section">\
			<a href="https://ats-dashboard.io/docs/login-customizer/?utm_source=plugin&utm_medium=login_customizer_link&utm_campaign=ats" class="accordion-section-title" target="_blank" tabindex="0">\
				PRO Features available! ›\
			</a>\
		</li>\
		';

	jQuery(proLink).insertBefore(
		"#accordion-section-ats_login_customizer_template_section"
	);
}

export default insertProLink;