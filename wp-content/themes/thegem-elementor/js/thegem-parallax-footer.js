(function($){

	$.updateParalaxFooter = function() {
		var $footer = $('.parallax-footer').first();
		if($footer.size() == 0) return ;
		$footer.thegemPreloader(function() {
			$('#page').css({marginBottom: ''});
			$footer.removeClass('parallax-footer-inited');
			if($footer.outerHeight() + $('#site-header').outerHeight() > $(window).height()) return ;
			$footer.addClass('parallax-footer-inited');
			$('#page').css({marginBottom: $footer.outerHeight()+'px'});
		});
	}

	$(function() {
		$.updateParalaxFooter();
	});

	$(window).resize(function() {
		$.updateParalaxFooter();
	});

})(jQuery);