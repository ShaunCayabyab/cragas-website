$(document).ready(function () {
	$('body, header, footer').hide();

	$('body').fadeIn(400, () => {
		$('header').fadeIn(700, () => {
			$('footer').fadeIn(700);
			
			$('.works-thumbnails').slick({
				autoplay     : true,
				autoplaySpeed: 4000,
				dots         : true,
				variableWidth: false,
			});

			setThumbnailListeners('featured');
		});
	});
})