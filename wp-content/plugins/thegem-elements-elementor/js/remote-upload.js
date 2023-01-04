(function($) {
	$(function() {

		$('body').on('click', '#thegem-remote-upload-button', function(e) {
			var urls = $(this).prev('textarea').val();
			e.preventDefault();
			$.ajax({
				url: ajaxurl,
				data: {action: 'thegem_remote_upload_ajax', urls: urls, security: thegem_remote_upload_object.security},
				method: 'POST',
				timeout: 50000
			}).done(function(msg) {
				console.log(msg);
			}).fail(function() {
				console.log('error');
			});
		});

	});
})(jQuery);
