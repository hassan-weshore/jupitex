(function($) {
	$(document).ready(function() {

		$('.gem-nivoslider').each(function() {
			var $slider = $(this);
			$slider.thegemPreloader(function() {

				$slider.nivoSlider({
					effect: thegem_nivoslider_options.effect,
					slices: parseInt(thegem_nivoslider_options.slices),
					boxCols: parseInt(thegem_nivoslider_options.boxCols),
					boxRows: parseInt(thegem_nivoslider_options.boxRows),
					animSpeed: parseInt(thegem_nivoslider_options.animSpeed),
					pauseTime: parseInt(thegem_nivoslider_options.pauseTime),
					directionNav: thegem_nivoslider_options.directionNav,
					controlNav: thegem_nivoslider_options.controlNav,
					beforeChange: function(){
						$('.nivo-caption', $slider).animate({ opacity: 'hide' }, 500);
					},
					afterChange: function(){
						var data = $slider.data('nivo:vars');
						var index = data.currentSlide;
						if($('.gem-nivoslider-slide:eq('+index+') .gem-nivoslider-caption', $slider).length) {
							$('.nivo-caption', $slider).html($('.gem-nivoslider-slide:eq('+index+') .gem-nivoslider-caption', $slider).html());
							$('.nivo-caption', $slider).animate({ opacity: 'show' }, 500);
						} else {
							$('.nivo-caption', $slider).html('');
						}
					},
					afterLoad: function(){
						$slider.next('.nivo-controlNav').appendTo($slider).addClass('gem-mini-pagination');
						$('.nivo-directionNav .nivo-prevNav', $slider).addClass('gem-prev');
						$('.nivo-directionNav .nivo-nextNav', $slider).addClass('gem-next');
						if($('.gem-nivoslider-slide:eq(0) .gem-nivoslider-caption', $slider).length) {
							$('.nivo-caption', $slider).html($('.gem-nivoslider-slide:eq(0) .gem-nivoslider-caption', $slider).html());
							$('.nivo-caption', $slider).show();
						}
					}
				});

			});
		});

	});
})(jQuery);