(function($) {
	function initBlogDefault() {
		if (window.tgpLazyItems !== undefined) {
			var isShowed = window.tgpLazyItems.checkGroupShowed(this, function(node) {
				initBlogDefault.call(node);
			});
			if (!isShowed) {
				return;
			}
		}

		var $blog = $(this);

		window.thegemUpdateLikesIcons($blog);

		$('.blog-load-more', $blog.parent()).on('click', function() {
			window.thegemBlogLoadMoreRequest($blog, $(this), false);
		});

		window.thegemInitBlogScrollNextPage($blog, $blog.siblings('.blog-scroll-pagination'));

		var itemsAnimations = $blog.itemsAnimations({
			itemSelector: 'article',
			scrollMonitor: true
		});
		itemsAnimations.show();

		window.thegemBlogImagesLoaded($blog, 'article img', function() {
			if ($blog.hasClass('blog-style-justified-2x') || $blog.hasClass('blog-style-justified-3x') || $blog.hasClass('blog-style-justified-4x') || $blog.hasClass('blog-style-justified-100')) {
				window.thegemBlogOneSizeArticles($blog);
			}

		});
	}

	$('.blog:not(body,.blog-style-timeline_new,.blog-style-masonry)').each(initBlogDefault);

	$.fn.initBlogGrid = function () {
		$(this).each(initBlogDefault);
	};

	
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/thegem-bloglist.default', function ($scope, $) {
			window.thegemUpdateLikesIcons($('.blog', $scope));
			$('.blog, .bloglist-pagination, .blog-load-more', $scope).thegemPreloader(function() {});
		});
	});
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/thegem-blog-grid.default', function ($scope, $) {
			window.thegemUpdateLikesIcons($('.blog', $scope));
			$('.blog, .blog-grid-pagination, .blog-load-more', $scope).thegemPreloader(function() {});
		});
	});
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/thegem-blogtimeline.default', function ($scope, $) {
			window.thegemUpdateLikesIcons($('.blog', $scope));
			$('.blog, .blogtimeline-pagination, .blog-load-more', $scope).thegemPreloader(function() {});
		});
	});

	$(window).on('resize', function(){
		$(".blog-style-justified-2x, .blog-style-justified-3x, .blog-style-justified-4x, .blog-style-justified-100").each(function(){
			window.thegemBlogOneSizeArticles($(this));
		});
	});

})(jQuery);
